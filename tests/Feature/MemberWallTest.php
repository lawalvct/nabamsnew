<?php

namespace Tests\Feature;

use App\Models\User;
use App\Support\MemberWallOrder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class MemberWallTest extends TestCase
{
    use RefreshDatabase;

    public function test_same_seed_gives_same_order(): void
    {
        $members = collect(range(1, 20))->map(fn (int $id) => (object) [
            'id' => $id,
            'created_at' => now()->subDays($id * 10),
        ]);

        $this->assertSame(MemberWallOrder::ids($members, 42), MemberWallOrder::ids($members->shuffle(), 42));
        $this->assertNotSame(MemberWallOrder::ids($members, 42), MemberWallOrder::ids($members, 43));
    }

    public function test_new_members_surface_near_the_top_more_often_than_old_ones(): void
    {
        $members = collect(range(1, 10))->map(fn (int $id) => (object) [
            'id' => $id,
            'created_at' => $id === 1 ? now()->subDays(2) : now()->subYears(2),
        ]);

        $newMemberFirst = 0;

        foreach (range(1, 500) as $seed) {
            if (MemberWallOrder::ids($members, $seed)[0] === 1) {
                $newMemberFirst++;
            }
        }

        // Plain random would put member 1 first ~10% of the time (~50 runs);
        // with the ~7.5x boost it should be ~45% (~225 runs).
        $this->assertGreaterThan(150, $newMemberFirst);
        $this->assertLessThan(500, $newMemberFirst);
    }

    public function test_weight_decays_back_to_normal(): void
    {
        $this->assertEqualsWithDelta(8.0, MemberWallOrder::weight(now()), 0.01);
        $this->assertLessThan(1.1, MemberWallOrder::weight(now()->subDays(150)));
        $this->assertSame(1.0, MemberWallOrder::weight(null));
    }

    public function test_member_wall_shows_new_badge_and_paginates_without_duplicates(): void
    {
        User::factory()->count(110)->create(['image' => 'uploads/old.jpg', 'created_at' => now()->subYear()]);
        User::factory()->create(['image' => 'uploads/new.jpg', 'created_at' => now()->subDay()]);
        User::factory()->create(['image' => null]);

        $response = $this->get(route('members.index', ['seed' => 7, 'preview_seed' => 9]))
            ->assertOk()
            ->assertViewHas('totalMembers', 111)
            ->assertSee('Spotlight a Member');

        $this->assertCount(1, $response->viewData('newMemberJoinTimes'));

        $firstPage = $response->viewData('members');
        $previewIds = $response->viewData('previewMembers')->pluck('id');

        $secondPage = $this->getJson(route('members.index', ['seed' => 7, 'preview_seed' => 9, 'page' => 2]))
            ->assertOk()
            ->assertJsonPath('next_page_url', null);

        $this->assertCount(96, $firstPage);
        $this->assertSame(111 - 6 - 96, substr_count($secondPage->json('html'), 'data-new='));
        $this->assertEmpty($firstPage->pluck('id')->intersect($previewIds));
    }
}
