<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Support\MemberWallOrder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;

class MemberDirectoryController extends Controller
{
    public function index(Request $request): View|JsonResponse
    {
        $seed = (int) $request->query('seed');
        $previewSeed = (int) $request->query('preview_seed');

        if ($seed < 1) {
            $seed = random_int(1, 999999);
        }

        if ($previewSeed < 1 || $previewSeed === $seed) {
            $previewSeed = random_int(1, 999999);
        }

        $wallCandidates = $this->memberImageQuery()->get(['id', 'created_at']);

        $previewIds = array_slice(MemberWallOrder::ids($wallCandidates, $previewSeed), 0, 6);
        $previewMembers = $this->membersInOrder($previewIds);

        $wallIds = array_values(array_diff(MemberWallOrder::ids($wallCandidates, $seed), $previewIds));
        $perPage = 96;
        $page = LengthAwarePaginator::resolveCurrentPage();

        $members = (new LengthAwarePaginator(
            $this->membersInOrder(array_slice($wallIds, ($page - 1) * $perPage, $perPage)),
            count($wallIds),
            $perPage,
            $page,
            ['path' => $request->url()],
        ))->appends([
            'seed' => $seed,
            'preview_seed' => $previewSeed,
        ]);

        if ($request->expectsJson()) {
            $offset = ($members->currentPage() - 1) * $members->perPage();
            $html = $members->getCollection()
                ->map(fn (User $member, int $index) => view('partials.site.member-card', [
                    'member' => $member,
                    'gradientIndex' => $offset + $index,
                ])->render())
                ->implode('');

            return response()->json([
                'html' => $html,
                'next_page_url' => $members->nextPageUrl(),
            ]);
        }

        return view('members', [
            'members' => $members,
            'previewMembers' => $previewMembers,
            'seed' => $seed,
            'previewSeed' => $previewSeed,
            'totalMembers' => $wallCandidates->count(),
            'newMemberJoinTimes' => $wallCandidates
                ->filter(fn (User $member) => MemberWallOrder::isNew($member->created_at))
                ->map(fn (User $member) => $member->created_at->getTimestamp())
                ->values(),
        ]);
    }

    public function photo(string $directory, string $filename)
    {
        abort_unless(in_array($directory, ['profile_photos', 'passport_photographs'], true), 404);

        $path = $directory.'/'.basename($filename);

        abort_unless(
            User::query()
                ->where('role', 'Member')
                ->where('is_active', 'Yes')
                ->where('is_ban', 'No')
                ->where('image', $path)
                ->exists(),
            404
        );

        abort_unless(Storage::disk('public')->exists($path), 404);

        return response()->file(Storage::disk('public')->path($path), [
            'Cache-Control' => 'public, max-age=604800',
        ]);
    }

    /**
     * @param  array<int, int>  $ids
     */
    private function membersInOrder(array $ids): Collection
    {
        if ($ids === []) {
            return new Collection();
        }

        $positions = array_flip($ids);

        return User::query()
            ->whereIn('id', $ids)
            ->get()
            ->sortBy(fn (User $member) => $positions[$member->id])
            ->values();
    }

    private function memberImageQuery()
    {
        return User::query()
            ->where('role', 'Member')
            ->where('is_active', 'Yes')
            ->where('is_ban', 'No')
            ->whereNotNull('image')
            ->where('image', '!=', '');
    }
}
