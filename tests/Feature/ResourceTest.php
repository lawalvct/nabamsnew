<?php

namespace Tests\Feature;

use App\Models\AppSetting;
use App\Models\BankAccount;
use App\Models\Payment;
use App\Models\Resource;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class ResourceTest extends TestCase
{
    use RefreshDatabase;

    private User $admin;

    private User $member;

    protected function setUp(): void
    {
        parent::setUp();

        Storage::fake('local');
        Notification::fake();
        AppSetting::paymentRequirement()->update(['value' => AppSetting::PAYMENT_OFF]);

        $this->admin = User::factory()->create(['role' => 'Admin', 'user_role_id' => 1, 'matno' => 'ADMIN001']);
        $this->member = User::factory()->create(['role' => 'Member', 'matno' => 'MEM001']);
    }

    public function test_admin_can_upload_free_and_paid_resources(): void
    {
        $this->actingAs($this->admin)
            ->post(route('admin.resources.store'), $this->payload())
            ->assertRedirect(route('admin.resources.index'))
            ->assertSessionHasNoErrors();

        $resource = Resource::sole();

        $this->assertSame('pdf', $resource->file_extension);
        $this->assertStringStartsWith('resources/', $resource->file_path);
        $this->assertStringNotContainsString('notes', $resource->file_path, 'Stored name must be random, not the client filename.');
        Storage::disk('local')->assertExists($resource->file_path);

        $this->actingAs($this->admin)
            ->post(route('admin.resources.store'), $this->payload(['access' => 'paid', 'price' => '']))
            ->assertSessionHasErrors('price');
    }

    public function test_member_can_download_free_resource_and_counts_are_tracked(): void
    {
        $resource = $this->createResource();

        $this->actingAs($this->member)->get(route('resources.index'))->assertOk()->assertSee($resource->title);
        $this->actingAs($this->member)->get(route('resources.show', $resource))->assertOk()->assertSee('Download');
        $this->actingAs($this->member)->get(route('resources.show', $resource))->assertOk();

        $response = $this->actingAs($this->member)->get(route('resources.download', $resource));
        $response->assertOk();
        $this->assertStringContainsString('attachment; filename=bam-211-lecture-notes.pdf', $response->headers->get('content-disposition'));
        $this->assertSame('nosniff', $response->headers->get('x-content-type-options'));

        $resource->refresh();
        $this->assertSame(1, $resource->view_count, 'Repeated views in one session count once.');
        $this->assertSame(1, $resource->download_count);
    }

    public function test_paid_resource_requires_verified_purchase(): void
    {
        $resource = $this->createResource(['access' => 'paid', 'price' => 1500]);
        $bank = BankAccount::create(['bank_name' => 'First Bank', 'account_name' => 'NABAMS', 'account_number' => '0123456789', 'is_active' => 'Yes']);

        $this->actingAs($this->member)->get(route('resources.download', $resource))->assertForbidden();

        $this->actingAs($this->member)->post(route('resources.purchase', $resource))->assertRedirect();
        $payment = Payment::sole();

        $this->assertSame(Payment::TYPE_RESOURCE, $payment->type);
        $this->assertSame(1500, $payment->amount_due);
        $this->assertMatchesRegularExpression('/^[A-HJ-NP-Z2-9]{8}$/', $payment->reference);

        $this->actingAs($this->member)->get(route('payments.show', $payment))->assertOk()->assertSee($payment->reference);

        $this->actingAs($this->member)->post(route('payments.submit', $payment), [
            'bank_account_id' => $bank->id,
            'amount_paid' => 1500,
            'payer_name' => 'Test Payer',
            'paid_at' => now()->toDateString(),
            'evidence' => UploadedFile::fake()->image('proof.png'),
        ])->assertRedirect(route('payments.show', $payment));

        $this->actingAs($this->member)->get(route('resources.download', $resource))->assertForbidden();

        $this->actingAs($this->admin)->patch(route('admin.payments.approve', $payment), ['amount_paid' => 1500]);

        $this->actingAs($this->member)->get(route('resources.download', $resource))->assertOk();
        $this->actingAs($this->member)->get(route('payments.receipt', $payment))->assertOk();

        // Buying again just points back to the resource.
        $this->actingAs($this->member)->post(route('resources.purchase', $resource))->assertRedirect(route('resources.show', $resource));
        $this->assertSame(1, Payment::count());
    }

    public function test_resource_purchase_does_not_count_as_dues(): void
    {
        $resource = $this->createResource(['access' => 'paid', 'price' => 1500]);
        $this->actingAs($this->member)->post(route('resources.purchase', $resource));
        $payment = Payment::sole();
        $payment->update(['status' => Payment::STATUS_APPROVED, 'evidence_path' => 'x', 'amount_paid' => 1500]);

        $this->assertSame(0, Payment::approved()->dues()->count());
    }

    public function test_hidden_resources_and_bought_resources_are_protected(): void
    {
        $hidden = $this->createResource(['is_published' => 'No']);

        $this->actingAs($this->member)->get(route('resources.show', $hidden))->assertNotFound();
        $this->actingAs($this->member)->get(route('resources.download', $hidden))->assertForbidden();
        $this->actingAs($this->admin)->get(route('resources.download', $hidden))->assertOk();

        $paid = $this->createResource(['access' => 'paid', 'price' => 500]);
        Payment::create(['type' => 'resource', 'resource_id' => $paid->id, 'user_id' => $this->member->id, 'status' => 'approved', 'amount_due' => 500, 'amount_paid' => 500]);

        $this->actingAs($this->admin)->delete(route('admin.resources.destroy', $paid))->assertSessionHas('error');
        $this->assertModelExists($paid);

        $this->actingAs($this->member)->post(route('admin.resources.store'), $this->payload())->assertForbidden();
    }

    private function createResource(array $overrides = []): Resource
    {
        $this->actingAs($this->admin)->post(route('admin.resources.store'), $this->payload($overrides))->assertSessionHasNoErrors();

        return Resource::latest('id')->firstOrFail();
    }

    private function payload(array $overrides = []): array
    {
        return [
            'title' => 'BAM 211 Lecture Notes',
            'description' => 'Complete notes.',
            'category' => 'Lecture Notes',
            'level_id' => null,
            'access' => 'free',
            'price' => null,
            'is_published' => 'Yes',
            'file' => UploadedFile::fake()->createWithContent('notes.pdf', "%PDF-1.4\n1 0 obj << /Type /Catalog >> endobj\ntrailer << /Root 1 0 R >>\n%%EOF"),
            ...$overrides,
        ];
    }
}
