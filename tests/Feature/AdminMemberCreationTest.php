<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class AdminMemberCreationTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_can_create_member_without_matric_number(): void
    {
        $admin = User::factory()->create([
            'role' => 'Admin',
            'user_role_id' => 1,
            'matno' => 'ADMIN001',
        ]);

        $response = $this->actingAs($admin)->post(route('admin.members.store'), [
            'full_name' => 'Ada Lovelace',
            'gender' => 'Female',
            'email' => 'ada@example.com',
            'phone' => '08012345678',
            'matno' => '',
        ]);

        $member = User::where('email', 'ada@example.com')->firstOrFail();

        $response
            ->assertRedirect(route('admin.members.show', $member))
            ->assertSessionHas('success');

        $this->assertSame('Ada', $member->firstname);
        $this->assertSame('Lovelace', $member->lastname);
        $this->assertNull($member->matno);
        $this->assertSame('Member', $member->role);
        $this->assertTrue(Hash::check('12345678', $member->password));
    }
}
