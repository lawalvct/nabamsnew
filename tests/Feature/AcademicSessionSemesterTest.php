<?php

namespace Tests\Feature;

use App\Models\AcademicSession;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AcademicSessionSemesterTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_can_create_session_with_current_semester(): void
    {
        $this->actingAs($this->admin())
            ->post(route('admin.academic-sessions.store'), [
                'name' => '2025-2026',
                'starts_at_year' => 2025,
                'ends_at_year' => 2026,
                'current_semester' => 'Second',
                'is_active' => 'Yes',
            ])
            ->assertRedirect(route('admin.academic-sessions.index'));

        $this->assertSame('Second', AcademicSession::firstOrFail()->current_semester);
    }

    public function test_admin_can_switch_current_semester(): void
    {
        $session = AcademicSession::create([
            'name' => '2025-2026',
            'starts_at_year' => 2025,
            'ends_at_year' => 2026,
            'current_semester' => 'First',
            'is_current' => 'Yes',
            'is_active' => 'Yes',
        ]);

        $this->actingAs($this->admin())
            ->patch(route('admin.academic-sessions.set-semester', $session), ['current_semester' => 'Second'])
            ->assertSessionHas('success');

        $this->assertSame('Second', $session->fresh()->current_semester);

        $this->actingAs($this->admin('ADMIN002'))
            ->patch(route('admin.academic-sessions.set-semester', $session), ['current_semester' => 'Third'])
            ->assertSessionHasErrors('current_semester');
    }

    public function test_member_cannot_switch_current_semester(): void
    {
        $session = AcademicSession::create([
            'name' => '2025-2026',
            'starts_at_year' => 2025,
            'ends_at_year' => 2026,
        ]);
        $member = User::factory()->create(['role' => 'Member', 'matno' => 'MEM001']);

        $this->actingAs($member)
            ->patch(route('admin.academic-sessions.set-semester', $session), ['current_semester' => 'Second'])
            ->assertForbidden();
    }

    private function admin(string $matno = 'ADMIN001'): User
    {
        return User::factory()->create([
            'role' => 'Admin',
            'user_role_id' => 1,
            'matno' => $matno,
        ]);
    }
}
