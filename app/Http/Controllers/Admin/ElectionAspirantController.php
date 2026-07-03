<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AcademicSession;
use App\Models\ElectionAspirant;
use App\Models\ElectionPosition;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class ElectionAspirantController extends Controller
{
    public function index(Request $request): View
    {
        $this->authorizeAdmin($request);

        return view('admin.elections.aspirants.index', [
            'aspirants' => ElectionAspirant::query()
                ->with(['user', 'academicSession', 'positions'])
                ->withCount('votes')
                ->orderByDesc('academic_session_id')
                ->latest()
                ->paginate(12),
        ]);
    }

    public function create(Request $request): View
    {
        $this->authorizeAdmin($request);

        return view('admin.elections.aspirants.create', $this->formData(new ElectionAspirant()));
    }

    public function store(Request $request): RedirectResponse
    {
        $this->authorizeAdmin($request);

        $data = $this->validatedData($request);
        $positions = $data['positions'];
        $paymentStatus = $data['payment_status'];
        unset($data['positions'], $data['payment_status']);

        $user = User::findOrFail($data['user_id']);
        $data['name'] = $data['name'] ?: $user->name;

        $aspirant = ElectionAspirant::create($data);
        $this->syncPositions($aspirant, $positions, $paymentStatus);

        return redirect()
            ->route('admin.election.aspirants.index')
            ->with('success', 'Election aspirant created successfully.');
    }

    public function edit(Request $request, ElectionAspirant $aspirant): View
    {
        $this->authorizeAdmin($request);

        return view('admin.elections.aspirants.edit', $this->formData($aspirant));
    }

    public function update(Request $request, ElectionAspirant $aspirant): RedirectResponse
    {
        $this->authorizeAdmin($request);

        $data = $this->validatedData($request, $aspirant);
        $positions = $data['positions'];
        $paymentStatus = $data['payment_status'];
        unset($data['positions'], $data['payment_status']);

        $user = User::findOrFail($data['user_id']);
        $data['name'] = $data['name'] ?: $user->name;

        $aspirant->update($data);
        $this->syncPositions($aspirant, $positions, $paymentStatus);

        return redirect()
            ->route('admin.election.aspirants.index')
            ->with('success', 'Election aspirant updated successfully.');
    }

    public function destroy(Request $request, ElectionAspirant $aspirant): RedirectResponse
    {
        $this->authorizeAdmin($request);

        $aspirant->delete();

        return back()->with('success', 'Election aspirant deleted successfully.');
    }

    private function formData(ElectionAspirant $aspirant): array
    {
        return [
            'aspirant' => $aspirant->loadMissing('positions'),
            'academicSessions' => AcademicSession::active()->orderByDesc('starts_at_year')->get(),
            'positions' => ElectionPosition::query()->where('is_active', 'Yes')->orderBy('sort_order')->get(),
            'members' => User::query()->orderBy('firstname')->orderBy('lastname')->get(['id', 'firstname', 'lastname', 'email', 'matno']),
        ];
    }

    private function authorizeAdmin(Request $request): void
    {
        abort_unless(strtolower((string) $request->user()?->role) === 'admin', 403);
    }

    private function validatedData(Request $request, ?ElectionAspirant $aspirant = null): array
    {
        return $request->validate([
            'academic_session_id' => ['required', 'integer', 'exists:academic_sessions,id'],
            'user_id' => [
                'required',
                'integer',
                'exists:users,id',
                Rule::unique('election_aspirants', 'user_id')
                    ->where('academic_session_id', $request->integer('academic_session_id'))
                    ->ignore($aspirant),
            ],
            'name' => ['nullable', 'string', 'max:100'],
            'manifesto' => ['nullable', 'string'],
            'screening_status' => ['required', Rule::in(['pending', 'approved', 'rejected'])],
            'payment_status' => ['required', Rule::in(['pending', 'approved'])],
            'positions' => ['required', 'array', 'min:1'],
            'positions.*' => [
                'required',
                'integer',
                Rule::exists('election_positions', 'id')
                    ->where('academic_session_id', $request->integer('academic_session_id')),
            ],
        ]);
    }

    private function syncPositions(ElectionAspirant $aspirant, array $positions, string $paymentStatus): void
    {
        $syncData = collect($positions)
            ->unique()
            ->mapWithKeys(fn ($positionId) => [
                $positionId => [
                    'payment_status' => $paymentStatus,
                    'result_status' => 'pending',
                ],
            ])
            ->all();

        $aspirant->positions()->sync($syncData);
    }
}
