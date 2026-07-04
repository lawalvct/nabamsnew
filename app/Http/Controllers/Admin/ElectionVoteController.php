<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AcademicSession;
use App\Models\ElectionPosition;
use App\Models\ElectionVote;
use App\Models\ElectionVoteAdjustment;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\View\View;

class ElectionVoteController extends Controller
{
    public function index(Request $request): View
    {
        abort_unless(strtolower((string) $request->user()?->role) === 'admin', 403);

        $sessionId = $request->integer('academic_session_id') ?: AcademicSession::current()->value('id');

        return view('admin.elections.votes.index', [
            ...$this->resultData($sessionId),
            ...$this->recentData($sessionId),
        ]);
    }

    public function live(Request $request): JsonResponse
    {
        abort_unless(strtolower((string) $request->user()?->role) === 'admin', 403);

        $sessionId = $request->integer('academic_session_id') ?: AcademicSession::current()->value('id');
        $data = [
            ...$this->resultData($sessionId),
            ...$this->recentData($sessionId),
        ];

        return response()->json([
            'html' => view('admin.elections.votes.partials.live', $data)->render(),
            'updated_at' => now()->format('h:i:s A'),
        ]);
    }

    public function downloadPdf(Request $request)
    {
        abort_unless(strtolower((string) $request->user()?->role) === 'admin', 403);

        $sessionId = $request->integer('academic_session_id') ?: AcademicSession::current()->value('id');
        $data = $this->resultData($sessionId);
        $selectedSession = $data['selectedSession'];

        $pdf = Pdf::loadView('admin.elections.votes.pdf', [
            ...$data,
            'allAdjustments' => ElectionVoteAdjustment::query()
                ->with(['position', 'aspirant', 'admin'])
                ->when($sessionId, fn ($query) => $query->where('academic_session_id', $sessionId))
                ->latest()
                ->get(),
            'generatedBy' => $request->user(),
            'generatedAt' => now(),
        ])->setPaper('a4');

        $filename = 'nabams-election-results-'.Str::slug($selectedSession?->name ?? 'session').'.pdf';

        return $pdf->download($filename);
    }

    private function resultData(?int $sessionId): array
    {
        return [
            'academicSessions' => AcademicSession::query()->orderByDesc('starts_at_year')->get(),
            'selectedSessionId' => $sessionId,
            'selectedSession' => $sessionId ? AcademicSession::find($sessionId) : null,
            'positions' => ElectionPosition::query()
                ->with(['academicSession', 'aspirants'])
                ->withCount('votes')
                ->when($sessionId, fn ($query) => $query->where('academic_session_id', $sessionId))
                ->orderBy('sort_order')
                ->get(),
            'voteTotals' => ElectionVote::query()
                ->select('position_id', 'aspirant_id', DB::raw('COUNT(*) as vote_total'))
                ->when($sessionId, fn ($query) => $query->where('academic_session_id', $sessionId))
                ->groupBy('position_id', 'aspirant_id')
                ->get()
                ->keyBy(fn ($row) => $row->position_id.':'.$row->aspirant_id),
            'positionAdjustmentTotals' => ElectionVoteAdjustment::query()
                ->select('position_id', DB::raw('COALESCE(SUM(adjustment), 0) as adjustment_total'))
                ->when($sessionId, fn ($query) => $query->where('academic_session_id', $sessionId))
                ->groupBy('position_id')
                ->get()
                ->keyBy('position_id'),
            'adjustmentTotals' => ElectionVoteAdjustment::query()
                ->select('position_id', 'aspirant_id', DB::raw('COALESCE(SUM(adjustment), 0) as adjustment_total'))
                ->when($sessionId, fn ($query) => $query->where('academic_session_id', $sessionId))
                ->groupBy('position_id', 'aspirant_id')
                ->get()
                ->keyBy(fn ($row) => $row->position_id.':'.$row->aspirant_id),
        ];
    }

    private function recentData(?int $sessionId): array
    {
        return [
            'recentVotes' => ElectionVote::query()
                ->with(['voter', 'position', 'aspirant'])
                ->when($sessionId, fn ($query) => $query->where('academic_session_id', $sessionId))
                ->latest()
                ->paginate(20)
                ->withQueryString(),
            'recentAdjustments' => ElectionVoteAdjustment::query()
                ->with(['position', 'aspirant', 'admin'])
                ->when($sessionId, fn ($query) => $query->where('academic_session_id', $sessionId))
                ->latest()
                ->limit(10)
                ->get(),
        ];
    }
}
