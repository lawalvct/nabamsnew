<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\Password;
use Tests\TestCase;

class PasswordResetTest extends TestCase
{
    use RefreshDatabase;

    public function test_forgot_password_screen_is_available(): void
    {
        $this->get(route('password.request'))
            ->assertOk()
            ->assertSee('Request reset link')
            ->assertSee('Send Reset Link');
    }

    public function test_reset_link_can_be_requested_and_password_can_be_reset(): void
    {
        Notification::fake();

        $user = User::factory()->create([
            'email' => 'member@example.com',
        ]);

        $this->post(route('password.email'), [
            'email' => 'member@example.com',
        ])->assertSessionHas('status');

        $token = Password::createToken($user);

        $this->get(route('password.reset', [
            'token' => $token,
            'email' => $user->email,
        ]))
            ->assertOk()
            ->assertSee('Set a new password');

        $this->post(route('password.update'), [
            'token' => $token,
            'email' => $user->email,
            'password' => 'Newpass123',
            'password_confirmation' => 'Newpass123',
        ])->assertRedirect(route('login'));

        $this->assertTrue(Hash::check('Newpass123', $user->fresh()->password));
    }
}
