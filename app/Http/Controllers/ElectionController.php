<?php

namespace App\Http\Controllers;

use App\Models\AcademicSession;
use App\Models\AppSetting;
use App\Models\ElectionAspirant;
use App\Models\ElectionPosition;
use App\Models\ElectionVote;
use Illuminate\Database\QueryException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ElectionController extends Controller
{
    public function index(Request $request): View
    {
        $session = AcademicSession::current()->first();
        $electionEnabled = AppSetting::electionEnabled();
        $myVotes = $session
            ? ElectionVote::query()
                ->where('academic_session_id', $session->id)
                ->where('user_id', $request->user()->id)
                ->with(['position', 'aspirant'])
                ->latest()
                ->get()
            : collect();

        return view('elections.index', [
            'currentSession' => $session,
            'electionEnabled' => $electionEnabled,
            'positions' => $session && $electionEnabled
                ? ElectionPosition::query()
                    ->where('academic_session_id', $session->id)
                    ->where('is_active', 'Yes')
                    ->with(['aspirants' => fn ($query) => $query
                        ->with('user')
                        ->where('screening_status', 'approved')
                        ->wherePivot('payment_status', 'approved')])
                    ->orderBy('sort_order')
                    ->get()
                : collect(),
            'votedPositionIds' => $session
                ? ElectionVote::query()
                    ->where('academic_session_id', $session->id)
                    ->where('user_id', $request->user()->id)
                    ->pluck('position_id')
                    ->all()
                : [],
            'myVotes' => $myVotes,
        ]);
    }

    public function vote(Request $request): RedirectResponse|JsonResponse
    {
        if (! AppSetting::electionEnabled()) {
            if ($request->expectsJson()) {
                return response()->json([
                    'message' => 'Election voting is currently disabled.',
                ], 403);
            }

            return back()->with('error', 'Election voting is currently disabled.');
        }

        $session = AcademicSession::current()->first();

        if (! $session) {
            if ($request->expectsJson()) {
                return response()->json([
                    'message' => 'No current academic session is available for voting.',
                ], 422);
            }

            return back()->with('error', 'No current academic session is available for voting.');
        }

        $data = $request->validate([
            'position_id' => ['required', 'integer', 'exists:election_positions,id'],
            'aspirant_id' => ['required', 'integer', 'exists:election_aspirants,id'],
        ]);

        $position = ElectionPosition::query()
            ->whereKey($data['position_id'])
            ->where('academic_session_id', $session->id)
            ->where('is_active', 'Yes')
            ->firstOrFail();

        $aspirant = ElectionAspirant::query()
            ->whereKey($data['aspirant_id'])
            ->where('academic_session_id', $session->id)
            ->where('screening_status', 'approved')
            ->whereHas('positions', fn ($query) => $query
                ->where('election_positions.id', $position->id)
                ->where('election_aspirant_position.payment_status', 'approved'))
            ->firstOrFail();

        try {
            ElectionVote::create([
                'user_id' => $request->user()->id,
                'academic_session_id' => $session->id,
                'position_id' => $position->id,
                'aspirant_id' => $aspirant->id,
                'ip_address' => $request->ip(),
            ]);
        } catch (QueryException) {
            if ($request->expectsJson()) {
                return response()->json([
                    'message' => 'You have already voted for this position.',
                    'position_id' => $position->id,
                ], 409);
            }

            return back()->with('error', 'You have already voted for this position.');
        }

        if ($request->expectsJson()) {
            return response()->json([
                'message' => 'Your vote has been recorded successfully.',
                'position_id' => $position->id,
                'position_name' => $position->name,
                'aspirant_id' => $aspirant->id,
                'aspirant_name' => $aspirant->name,
            ]);
        }

        return back()->with('success', 'Your vote has been recorded successfully.');
    }
}
