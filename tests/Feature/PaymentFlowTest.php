<?php

namespace Tests\Feature;

use App\Models\AcademicSession;
use App\Models\AppSetting;
use App\Models\BankAccount;
use App\Models\Level;
use App\Models\Payment;
use App\Models\PriceSetting;
use App\Models\User;
use App\Notifications\PaymentStatusChanged;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class PaymentFlowTest extends TestCase
{
    use RefreshDatabase;

    private AcademicSession $session;

    private Level $level;

    private BankAccount $bank;

    private User $member;

    protected function setUp(): void
    {
        parent::setUp();

        Storage::fake('local');
        Notification::fake();

        $this->session = AcademicSession::create([
            'name' => '2025-2026',
            'starts_at_year' => 2025,
            'ends_at_year' => 2026,
            'current_semester' => 'First',
            'is_current' => 'Yes',
            'is_active' => 'Yes',
        ]);
        $this->level = Level::create(['name' => 'ND1', 'sort_order' => 0, 'is_active' => 'Yes']);

        foreach (['First' => 5000, 'Second' => 3000] as $semester => $amount) {
            PriceSetting::create([
                'name' => 'Dues',
                'amount' => $amount,
                'academic_session_id' => $this->session->id,
                'level_id' => $this->level->id,
                'semester' => $semester,
                'is_active' => 'Yes',
            ]);
        }

        $this->bank = BankAccount::create([
            'bank_name' => 'First Bank',
            'account_name' => 'NABAMS',
            'account_number' => '0123456789',
            'is_active' => 'Yes',
        ]);

        $this->member = User::factory()->create(['role' => 'Member', 'level_id' => $this->level->id, 'matno' => 'MEM001']);
        $this->setMode(AppSetting::PAYMENT_SESSION_SEMESTER);
    }

    public function test_unpaid_member_is_locked_out_but_can_use_payment_pages_and_profile(): void
    {
        $this->actingAs($this->member)->get(route('dashboard'))->assertRedirect(route('payments.create'));
        $this->actingAs($this->member)->get(route('election.index'))->assertRedirect(route('payments.create'));

        $this->actingAs($this->member)->get(route('payments.create'))->assertOk()->assertSee('Pay your NABAMS dues');
        $this->actingAs($this->member)->get(route('payments.index'))->assertOk();
        $this->actingAs($this->member)->get(route('profile.edit'))->assertOk();
    }

    public function test_pay_page_creates_one_payment_with_an_8_character_reference_for_the_semester(): void
    {
        $this->actingAs($this->member)->get(route('payments.create'))->assertOk();
        $this->actingAs($this->member)->get(route('payments.create'))->assertOk();

        $payment = Payment::sole();

        $this->assertMatchesRegularExpression('/^[A-HJ-NP-Z2-9]{8}$/', $payment->reference);
        $this->assertSame(5000, $payment->amount_due);
        $this->assertSame('First', $payment->semester);
        $this->assertSame(Payment::STATUS_AWAITING, $payment->status);
    }

    public function test_session_only_rule_charges_all_semesters_once(): void
    {
        $this->setMode(AppSetting::PAYMENT_SESSION);

        $this->actingAs($this->member)->get(route('payments.create'))->assertOk();

        $payment = Payment::sole();

        $this->assertNull($payment->semester);
        $this->assertSame(8000, $payment->amount_due);
        $this->assertCount(2, $payment->items);
    }

    public function test_full_flow_submit_approve_unlock_and_download_receipt(): void
    {
        $admin = $this->admin();
        $payment = $this->submitPayment();

        $this->assertSame(Payment::STATUS_PENDING, $payment->status);
        Storage::disk('local')->assertExists($payment->evidence_path);
        Notification::assertSentTo($this->member, PaymentStatusChanged::class);

        // Still locked while pending.
        $this->actingAs($this->member)->get(route('dashboard'))->assertRedirect(route('payments.create'));
        $this->actingAs($this->member)->get(route('payments.create'))->assertSee('24 hours');

        $this->actingAs($admin)
            ->patch(route('admin.payments.approve', $payment), ['amount_paid' => 5000])
            ->assertSessionHas('success');

        $payment->refresh();
        $this->assertSame(Payment::STATUS_APPROVED, $payment->status);
        $this->assertSame($admin->name, $payment->reviewer_name);
        $this->assertSame('Yes', $this->member->fresh()->fee_paid);

        $this->actingAs($this->member)->get(route('dashboard'))->assertOk();
        $this->actingAs($this->member)
            ->get(route('payments.receipt', $payment))
            ->assertOk()
            ->assertHeader('content-type', 'application/pdf');
    }

    public function test_rejected_payment_can_be_resubmitted_with_the_same_reference(): void
    {
        $payment = $this->submitPayment();
        $reference = $payment->reference;

        $this->actingAs($this->admin())
            ->patch(route('admin.payments.reject', $payment), ['rejection_reason' => 'Transfer not found'])
            ->assertSessionHas('success');

        $this->actingAs($this->member)->get(route('payments.create'))->assertSee('Transfer not found');

        $payment = $this->submitPayment();

        $this->assertSame($reference, $payment->reference);
        $this->assertSame(Payment::STATUS_PENDING, $payment->status);
        $this->assertNull($payment->rejection_reason);
    }

    public function test_semester_change_locks_members_until_they_pay_again(): void
    {
        $payment = $this->submitPayment();
        $this->actingAs($this->admin())->patch(route('admin.payments.approve', $payment), ['amount_paid' => 5000]);

        $this->actingAs($this->member)->get(route('dashboard'))->assertOk();

        $this->session->update(['current_semester' => 'Second']);

        $this->actingAs($this->member)->get(route('dashboard'))->assertRedirect(route('payments.create'));
        $this->assertSame('No', $this->member->fresh()->fee_paid);

        // Old receipts stay downloadable while locked.
        $this->actingAs($this->member)->get(route('payments.receipt', $payment))->assertOk();
    }

    public function test_full_session_payment_covers_semester_rule(): void
    {
        $this->setMode(AppSetting::PAYMENT_SESSION);
        $payment = $this->submitPayment();
        $this->actingAs($this->admin())->patch(route('admin.payments.approve', $payment), ['amount_paid' => 8000]);

        $this->setMode(AppSetting::PAYMENT_SESSION_SEMESTER);
        $this->session->update(['current_semester' => 'Second']);

        $this->actingAs($this->member)->get(route('dashboard'))->assertOk();
    }

    public function test_member_is_not_locked_when_no_price_is_set_for_their_level_or_rule_is_off(): void
    {
        $otherLevel = Level::create(['name' => 'HND1', 'sort_order' => 1, 'is_active' => 'Yes']);
        $unpricedMember = User::factory()->create(['role' => 'Member', 'level_id' => $otherLevel->id, 'matno' => 'MEM002']);

        $this->actingAs($unpricedMember)->get(route('dashboard'))->assertOk();

        $this->setMode(AppSetting::PAYMENT_OFF);
        $this->actingAs($this->member)->get(route('dashboard'))->assertOk();
    }

    public function test_members_cannot_see_others_payments_or_review_payments(): void
    {
        $payment = $this->submitPayment();
        $stranger = User::factory()->create(['role' => 'Member', 'level_id' => $this->level->id, 'matno' => 'MEM003']);

        $this->actingAs($stranger)->get(route('payments.evidence', $payment))->assertForbidden();
        $this->actingAs($stranger)->post(route('payments.submit', $payment), [])->assertForbidden();
        $this->actingAs($this->member)->patch(route('admin.payments.approve', $payment), ['amount_paid' => 1])->assertForbidden();
        $this->actingAs($this->member)->get(route('payments.receipt', $payment))->assertNotFound();
    }

    public function test_admin_can_list_and_review_payments(): void
    {
        $payment = $this->submitPayment();
        $admin = $this->admin();

        $this->actingAs($admin)->get(route('admin.payments.index'))->assertOk()->assertSee($payment->reference);
        $this->actingAs($admin)->get(route('admin.payments.index', ['q' => strtolower($payment->reference)]))->assertOk()->assertSee($payment->reference);
        $this->actingAs($admin)->get(route('admin.payments.show', $payment))->assertOk()->assertSee('Approve Payment');
        $this->actingAs($admin)->get(route('payments.evidence', $payment))->assertOk();
    }

    private function submitPayment(): Payment
    {
        $this->actingAs($this->member)->get(route('payments.create'));
        $payment = Payment::query()->where('user_id', $this->member->id)->latest('id')->firstOrFail();

        $this->actingAs($this->member)
            ->post(route('payments.submit', $payment), [
                'bank_account_id' => $this->bank->id,
                'amount_paid' => $payment->amount_due,
                'payer_name' => 'Test Payer',
                'paid_at' => now()->toDateString(),
                'evidence' => UploadedFile::fake()->image('receipt.jpg'),
            ])
            ->assertRedirect(route('payments.create'))
            ->assertSessionHasNoErrors();

        return $payment->fresh();
    }

    private function admin(): User
    {
        return User::factory()->create(['role' => 'Admin', 'user_role_id' => 1, 'matno' => 'ADMIN001']);
    }

    private function setMode(string $mode): void
    {
        AppSetting::paymentRequirement()->update(['value' => $mode, 'active' => 'Yes']);
    }
}
