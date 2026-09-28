<?php

namespace Tests\Feature;

use App\Models\AcademicSession;
use App\Models\Level;
use App\Models\PriceSetting;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PriceSettingTest extends TestCase
{
    use RefreshDatabase;

    private AcademicSession $session;

    private Level $level;

    protected function setUp(): void
    {
        parent::setUp();

        $this->session = AcademicSession::create([
            'name' => '2025-2026',
            'starts_at_year' => 2025,
            'ends_at_year' => 2026,
            'is_current' => 'Yes',
            'is_active' => 'Yes',
        ]);

        $this->level = Level::create(['name' => 'ND1', 'sort_order' => 0, 'is_active' => 'Yes']);
    }

    public function test_admin_can_view_price_settings(): void
    {
        $this->actingAs($this->admin())
            ->get(route('admin.price-settings.index'))
            ->assertOk()
            ->assertSee('Manage NABAMS fees and prices');
    }

    public function test_member_cannot_access_price_settings(): void
    {
        $member = User::factory()->create(['role' => 'Member', 'matno' => 'MEM001']);

        $this->actingAs($member)
            ->get(route('admin.price-settings.index'))
            ->assertForbidden();
    }

    public function test_admin_can_create_price_for_level_session_and_semester(): void
    {
        $admin = $this->admin();

        $this->actingAs($admin)
            ->post(route('admin.price-settings.store'), $this->payload())
            ->assertRedirect(route('admin.price-settings.index'))
            ->assertSessionHas('success');

        $price = PriceSetting::firstOrFail();

        $this->assertSame(5000, $price->amount);
        $this->assertSame('First', $price->semester);
        $this->assertSame($this->level->id, $price->level_id);
        $this->assertSame($this->session->id, $price->academic_session_id);
        $this->assertSame($admin->id, $price->updated_by);
    }

    public function test_same_price_name_is_allowed_for_a_different_semester_but_not_duplicated(): void
    {
        $admin = $this->admin();

        $this->actingAs($admin)->post(route('admin.price-settings.store'), $this->payload());

        $this->actingAs($admin)
            ->post(route('admin.price-settings.store'), $this->payload(['semester' => 'Second']))
            ->assertSessionHasNoErrors();

        $this->actingAs($admin)
            ->post(route('admin.price-settings.store'), $this->payload())
            ->assertSessionHasErrors('name');

        $this->assertSame(2, PriceSetting::count());
    }

    public function test_admin_can_update_and_delete_price(): void
    {
        $admin = $this->admin();
        $price = PriceSetting::create($this->payload());

        $this->actingAs($admin)
            ->put(route('admin.price-settings.update', $price), $this->payload(['amount' => 7500]))
            ->assertRedirect(route('admin.price-settings.index'));

        $this->assertSame(7500, $price->fresh()->amount);

        $this->actingAs($admin)
            ->delete(route('admin.price-settings.destroy', $price))
            ->assertSessionHas('success');

        $this->assertDatabaseMissing('price_settings', ['id' => $price->id]);
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
            'name' => 'Association Dues',
            'amount' => 5000,
            'academic_session_id' => $this->session->id,
            'level_id' => $this->level->id,
            'semester' => 'First',
            'is_active' => 'Yes',
            ...$overrides,
        ];
    }
}
