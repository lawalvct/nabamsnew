<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AcademicSession;
use App\Models\ElectionPosition;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class ElectionPositionController extends Controller
{
    public function index(Request $request): View
    {
        $this->authorizeAdmin($request);

        return view('admin.elections.positions.index', [
            'positions' => ElectionPosition::query()
                ->with(['academicSession', 'admin'])
                ->withCount(['aspirants', 'votes'])
                ->orderByDesc('academic_session_id')
                ->orderBy('sort_order')
                ->paginate(12),
        ]);
    }

    public function create(Request $request): View
    {
        $this->authorizeAdmin($request);

        return view('admin.elections.positions.create', [
            'position' => new ElectionPosition(),
            'academicSessions' => AcademicSession::active()->orderByDesc('starts_at_year')->get(),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $this->authorizeAdmin($request);

        $data = $this->validatedData($request);
        $data['admin_id'] = $request->user()->id;

        ElectionPosition::create($data);

        return redirect()
            ->route('admin.election.positions.index')
            ->with('success', 'Election position created successfully.');
    }

    public function edit(Request $request, ElectionPosition $position): View
    {
        $this->authorizeAdmin($request);

        return view('admin.elections.positions.edit', [
            'position' => $position,
            'academicSessions' => AcademicSession::active()->orderByDesc('starts_at_year')->get(),
        ]);
    }

    public function update(Request $request, ElectionPosition $position): RedirectResponse
    {
        $this->authorizeAdmin($request);

        $position->update($this->validatedData($request, $position));

        return redirect()
            ->route('admin.election.positions.index')
            ->with('success', 'Election position updated successfully.');
    }

    public function destroy(Request $request, ElectionPosition $position): RedirectResponse
    {
        $this->authorizeAdmin($request);

        $position->delete();

        return back()->with('success', 'Election position deleted successfully.');
    }

    private function authorizeAdmin(Request $request): void
    {
        abort_unless(strtolower((string) $request->user()?->role) === 'admin', 403);
    }

    private function validatedData(Request $request, ?ElectionPosition $position = null): array
    {
        return $request->validate([
            'academic_session_id' => ['required', 'integer', 'exists:academic_sessions,id'],
            'name' => [
                'required',
                'string',
                'max:100',
                Rule::unique('election_positions', 'name')
                    ->where('academic_session_id', $request->integer('academic_session_id'))
                    ->ignore($position),
            ],
            'form_amount' => ['required', 'integer', 'min:0'],
            'sort_order' => ['required', 'integer', 'min:1'],
            'is_active' => ['required', Rule::in(['Yes', 'No'])],
        ]);
    }
}
