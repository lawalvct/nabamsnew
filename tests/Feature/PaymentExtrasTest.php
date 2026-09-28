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
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class PaymentExtrasTest extends TestCase
{
    use RefreshDatabase;

    private AcademicSession $session;

    private User $member;

    private User $admin;

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
        $level = Level::create(['name' => 'ND1', 'sort_order' => 0, 'is_active' => 'Yes']);

        foreach (['First' => 5000, 'Second' => 3000] as $semester => $amount) {
            PriceSetting::create([
                'name' => 'Dues',
                'amount' => $amount,
                'academic_session_id' => $this->session->id,
                'level_id' => $level->id,
                'semester' => $semester,
                'is_active' => 'Yes',
            ]);
        }

        AppSetting::paymentRequirement()->update(['value' => AppSetting::PAYMENT_SESSION_SEMESTER]);

        $this->member = User::factory()->create(['role' => 'Member', 'level_id' => $level->id, 'matno' => 'HBAF/23/0001']);
        $this->admin = User::factory()->create(['role' => 'Admin', 'user_role_id' => 1, 'matno' => 'ADMIN001']);
    }

    public function test_admin_can_record_cash_payment_which_unlocks_member(): void
    {
        $this->actingAs($this->member)->get(route('dashboard'))->assertRedirect(route('payments.create'));

        $this->actingAs($this->admin)
            ->post(route('admin.payments.store'), $this->recordPayload())
            ->assertSessionHasNoErrors()
            ->assertSessionHas('success');

        $payment = Payment::sole();

        $this->assertSame(Payment::STATUS_APPROVED, $payment->status);
        $this->assertSame('cash', $payment->payment_method);
        $this->assertSame(5000, $payment->amount_due);
        $this->assertSame($this->admin->name, $payment->reviewer_name);
        $this->assertSame('Paid at meeting', $payment->admin_note);
        Notification::assertSentTo($this->member, PaymentStatusChanged::class);

        $this->actingAs($this->member)->get(route('dashboard'))->assertOk();
        $this->actingAs($this->member)->get(route('payments.receipt', $payment))->assertOk();
    }

    public function test_recording_reuses_members_open_payment_and_keeps_reference(): void
    {
        $this->actingAs($this->member)->get(route('payments.create'));
        $reference = Payment::sole()->reference;

        $this->actingAs($this->admin)->post(route('admin.payments.store'), $this->recordPayload(['member' => 'hbaf/23/0001']));

        $this->assertSame(1, Payment::count());
        $this->assertSame($reference, Payment::sole()->reference);
        $this->assertTrue(Payment::sole()->isApproved());
    }

    public function test_recording_is_blocked_when_period_already_paid_or_member_unknown(): void
    {
        $this->actingAs($this->admin)->post(route('admin.payments.store'), $this->recordPayload(['period' => 'Full', 'amount_paid' => 8000]));

        $this->actingAs($this->admin)
            ->post(route('admin.payments.store'), $this->recordPayload())
            ->assertSessionHasErrors('period');

        $this->actingAs($this->admin)
            ->post(route('admin.payments.store'), $this->recordPayload(['member' => 'nobody@example.com']))
            ->assertSessionHasErrors('member');

        $this->actingAs($this->admin)
            ->post(route('admin.payments.store'), $this->recordPayload(['payment_method' => 'transfer', 'bank_account_id' => null]))
            ->assertSessionHasErrors('bank_account_id');

        $this->assertSame(1, Payment::count());
    }

    public function test_admin_can_export_filtered_transactions_as_csv(): void
    {
        $this->actingAs($this->admin)->post(route('admin.payments.store'), $this->recordPayload());
        $reference = Payment::sole()->reference;

        $response = $this->actingAs($this->admin)->get(route('admin.payments.export', ['status' => 'approved']));

        $response->assertOk();
        $this->assertStringContainsString('text/csv', $response->headers->get('content-type'));

        $csv = $response->streamedContent();
        $this->assertStringContainsString('Reference,Member', $csv);
        $this->assertStringContainsString($reference, $csv);
        $this->assertStringContainsString('Cash', $csv);

        $this->assertStringNotContainsString($reference, $this->actingAs($this->admin)->get(route('admin.payments.export', ['status' => 'rejected']))->streamedContent());
        $this->actingAs($this->member)->get(route('admin.payments.export'))->assertForbidden();
    }

    public function test_public_can_verify_approved_receipts_only(): void
    {
        $this->actingAs($this->admin)->post(route('admin.payments.store'), $this->recordPayload());
        $payment = Payment::sole();

        auth()->logout();

        $this->get(route('receipts.verify', ['reference' => strtolower($payment->reference)]))
            ->assertOk()
            ->assertSee('Valid receipt')
            ->assertSee($this->member->name)
            ->assertSee('HBAF/23/****')
            ->assertDontSee($this->member->email);

        $pending = Payment::create([
            'user_id' => $this->member->id,
            'academic_session_id' => $this->session->id,
            'semester' => 'Second',
            'status' => Payment::STATUS_PENDING,
        ]);

        $this->get(route('receipts.verify', ['reference' => $pending->reference]))
            ->assertOk()
            ->assertSee('No approved payment found');

        $this->get(route('receipts.verify'))->assertOk()->assertDontSee('No approved payment found');
    }

    private function recordPayload(array $overrides = []): array
    {
        return [
            'member' => 'HBAF/23/0001',
            'academic_session_id' => $this->session->id,
            'period' => 'First',
            'payment_method' => 'cash',
            'bank_account_id' => null,
            'amount_paid' => 5000,
            'paid_at' => now()->toDateString(),
            'admin_note' => 'Paid at meeting',
            ...$overrides,
        ];
    }
}
