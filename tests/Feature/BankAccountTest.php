<?php

namespace Tests\Feature;

use App\Models\BankAccount;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class BankAccountTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_can_view_bank_accounts(): void
    {
        $this->actingAs($this->admin())
            ->get(route('admin.bank-accounts.index'))
            ->assertOk()
            ->assertSee('Where members pay');
    }

    public function test_member_cannot_manage_bank_accounts(): void
    {
        $member = User::factory()->create(['role' => 'Member', 'matno' => 'MEM001']);

        $this->actingAs($member)->get(route('admin.bank-accounts.index'))->assertForbidden();
        $this->actingAs($member)->post(route('admin.bank-accounts.store'), $this->payload())->assertForbidden();
    }

    public function test_admin_can_add_bank_account_and_spaces_are_stripped(): void
    {
        $admin = $this->admin();

        $this->actingAs($admin)
            ->post(route('admin.bank-accounts.store'), $this->payload(['account_number' => '0123 456 789', 'sort_order' => '']))
            ->assertRedirect(route('admin.bank-accounts.index'))
            ->assertSessionHas('success');

        $account = BankAccount::firstOrFail();

        $this->assertSame('0123456789', $account->account_number);
        $this->assertSame(0, $account->sort_order);
        $this->assertSame($admin->id, $account->updated_by);
    }

    public function test_account_number_must_be_digits_and_unique_per_bank(): void
    {
        $admin = $this->admin();

        $this->actingAs($admin)
            ->post(route('admin.bank-accounts.store'), $this->payload(['account_number' => '01234ABC89']))
            ->assertSessionHasErrors('account_number');

        $this->actingAs($admin)->post(route('admin.bank-accounts.store'), $this->payload());

        $this->actingAs($admin)
            ->post(route('admin.bank-accounts.store'), $this->payload())
            ->assertSessionHasErrors('account_number');

        $this->actingAs($admin)
            ->post(route('admin.bank-accounts.store'), $this->payload(['bank_name' => 'Access Bank']))
            ->assertSessionHasNoErrors();

        $this->assertSame(2, BankAccount::count());
    }

    public function test_admin_can_update_and_delete_bank_account(): void
    {
        $admin = $this->admin();
        $account = BankAccount::create($this->payload());

        $this->actingAs($admin)
            ->put(route('admin.bank-accounts.update', $account), $this->payload(['is_active' => 'No']))
            ->assertRedirect(route('admin.bank-accounts.index'));

        $this->assertSame('No', $account->fresh()->is_active);

        $this->actingAs($admin)
            ->delete(route('admin.bank-accounts.destroy', $account))
            ->assertSessionHas('success');

        $this->assertDatabaseMissing('bank_accounts', ['id' => $account->id]);
    }

    private function admin(): User
    {
        return User::factory()->create([
            'role' => 'Admin',
            'user_role_id' => 1,
            'matno' => 'ADMIN001',
        ]);
    }

    private function payload(array $overrides = []): array
    {
        return [
            'bank_name' => 'First Bank',
            'account_name' => 'NABAMS Chapter',
            'account_number' => '0123456789',
            'instructions' => 'Use your matric number as narration.',
            'sort_order' => 1,
            'is_active' => 'Yes',
            ...$overrides,
        ];
    }
}
