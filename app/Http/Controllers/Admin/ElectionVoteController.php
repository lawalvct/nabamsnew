<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AcademicSession;
use App\Models\ElectionPosition;
use App\Models\ElectionVote;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ElectionVoteController extends Controller
{
    public function index(Request $request): View
    {
        abort_unless(strtolower((string) $request->user()?->role) === 'admin', 403);

        $sessionId = $request->integer('academic_session_id') ?: AcademicSession::current()->value('id');

        return view('admin.elections.votes.index', [
            'academicSessions' => AcademicSession::query()->orderByDesc('starts_at_year')->get(),
            'selectedSessionId' => $sessionId,
            'positions' => ElectionPosition::query()
                ->with(['academicSession', 'aspirants' => fn ($query) => $query->withCount('votes')])
                ->withCount('votes')
                ->when($sessionId, fn ($query) => $query->where('academic_session_id', $sessionId))
                ->orderBy('sort_order')
                ->get(),
            'recentVotes' => ElectionVote::query()
                ->with(['voter', 'position', 'aspirant'])
                ->when($sessionId, fn ($query) => $query->where('academic_session_id', $sessionId))
                ->latest()
                ->paginate(20)
                ->withQueryString(),
        ]);
    }
}
