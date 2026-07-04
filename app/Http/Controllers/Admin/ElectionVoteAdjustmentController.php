<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AcademicSession;
use App\Models\ElectionAspirant;
use App\Models\ElectionPosition;
use App\Models\ElectionVote;
use App\Models\ElectionVoteAdjustment;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class ElectionVoteAdjustmentController extends Controller
{
    public function create(Request $request): View
    {
        $this->authorizeAdmin($request);

        $sessionId = $request->integer('academic_session_id') ?: AcademicSession::current()->value('id');

        $positions = ElectionPosition::query()
            ->with(['academicSession', 'aspirants' => fn ($query) => $query->orderBy('name')])
            ->when($sessionId, fn ($query) => $query->where('academic_session_id', $sessionId))
            ->orderBy('sort_order')
            ->get();

        $adjustmentTotals = ElectionVoteAdjustment::query()
            ->select('position_id', 'aspirant_id', DB::raw('COALESCE(SUM(adjustment), 0) as adjustment_total'))
            ->when($sessionId, fn ($query) => $query->where('academic_session_id', $sessionId))
            ->groupBy('position_id', 'aspirant_id')
            ->get()
            ->keyBy(fn ($row) => $row->position_id.':'.$row->aspirant_id);

        $voteTotals = ElectionVote::query()
            ->select('position_id', 'aspirant_id', DB::raw('COUNT(*) as vote_total'))
            ->when($sessionId, fn ($query) => $query->where('academic_session_id', $sessionId))
            ->groupBy('position_id', 'aspirant_id')
            ->get()
            ->keyBy(fn ($row) => $row->position_id.':'.$row->aspirant_id);

        return view('admin.elections.vote-adjustments.create', [
            'academicSessions' => AcademicSession::query()->orderByDesc('starts_at_year')->get(),
            'selectedSessionId' => $sessionId,
            'positions' => $positions,
            'voteTotals' => $voteTotals,
            'adjustmentTotals' => $adjustmentTotals,
            'recentAdjustments' => ElectionVoteAdjustment::query()
                ->with(['academicSession', 'position', 'aspirant', 'admin'])
                ->when($sessionId, fn ($query) => $query->where('academic_session_id', $sessionId))
                ->latest()
                ->limit(20)
                ->get(),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $this->authorizeAdmin($request);

        $data = $request->validate([
            'academic_session_id' => ['required', 'integer', Rule::exists('academic_sessions', 'id')],
            'position_id' => ['required', 'integer', Rule::exists('election_positions', 'id')],
            'aspirant_id' => ['required', 'integer', Rule::exists('election_aspirants', 'id')],
            'adjustment' => ['required', 'integer', 'min:-100000', 'max:100000', 'not_in:0'],
            'reason' => ['required', 'string', 'min:10', 'max:1000'],
        ]);

        $position = ElectionPosition::query()
            ->whereKey($data['position_id'])
            ->where('academic_session_id', $data['academic_session_id'])
            ->firstOrFail();

        $aspirant = ElectionAspirant::query()
            ->whereKey($data['aspirant_id'])
            ->where('academic_session_id', $data['academic_session_id'])
            ->whereHas('positions', fn ($query) => $query->where('election_positions.id', $position->id))
            ->firstOrFail();

        DB::transaction(function () use ($request, $data, $position, $aspirant): void {
            $memberVotes = ElectionVote::query()
                ->where('academic_session_id', $data['academic_session_id'])
                ->where('position_id', $position->id)
                ->where('aspirant_id', $aspirant->id)
                ->count();

            $existingAdjustments = (int) ElectionVoteAdjustment::query()
                ->where('academic_session_id', $data['academic_session_id'])
                ->where('position_id', $position->id)
                ->where('aspirant_id', $aspirant->id)
                ->sum('adjustment');

            $beforeTotal = $memberVotes + $existingAdjustments;
            $afterTotal = $beforeTotal + (int) $data['adjustment'];

            if ($afterTotal < 0) {
                throw ValidationException::withMessages([
                    'adjustment' => 'Adjustment cannot make the aspirant total less than zero.',
                ]);
            }

            ElectionVoteAdjustment::create([
                'academic_session_id' => $data['academic_session_id'],
                'position_id' => $position->id,
                'aspirant_id' => $aspirant->id,
                'admin_id' => $request->user()->id,
                'adjustment' => (int) $data['adjustment'],
                'before_total' => $beforeTotal,
                'after_total' => $afterTotal,
                'reason' => $data['reason'],
                'ip_address' => $request->ip(),
            ]);
        });

        return back()->with('success', 'Election vote adjustment recorded with audit history.');
    }

    private function authorizeAdmin(Request $request): void
    {
        abort_unless(strtolower((string) $request->user()?->role) === 'admin', 403);
    }
}
