<?php

namespace Tests\Feature;

use App\Models\AppSetting;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class RegistrationSettingTest extends TestCase
{
    use RefreshDatabase;

    public function test_disabled_registration_shows_closed_page_and_blocks_submission(): void
    {
        AppSetting::registration()->update([
            'value' => 'Off',
            'active' => 'Yes',
        ]);

        $this->get(route('register'))
            ->assertOk()
            ->assertSee('Registration is currently closed');

        $this->post(route('register'), [
            'email' => 'closed@example.com',
        ])->assertRedirect(route('register'));

        $this->assertDatabaseMissing('users', [
            'email' => 'closed@example.com',
        ]);
    }

    public function test_admin_can_toggle_registration_setting(): void
    {
        $admin = User::factory()->create([
            'role' => 'Admin',
            'user_role_id' => 1,
            'matno' => 'ADMIN002',
        ]);

        $this->actingAs($admin)->put(route('admin.settings.update'), [
            'registration' => 'Off',
            'election' => 'On',
        ])->assertRedirect();

        $this->assertDatabaseHas('app_settings', [
            'slug' => 'registration',
            'value' => 'Off',
        ]);

        $this->actingAs($admin)->put(route('admin.settings.update'), [
            'registration' => 'On',
            'election' => 'On',
        ])->assertRedirect();

        $this->assertDatabaseHas('app_settings', [
            'slug' => 'registration',
            'value' => 'On',
        ]);
    }
}
