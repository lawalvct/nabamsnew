<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AcademicSession;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class AcademicSessionController extends Controller
{
    public function index(Request $request): View
    {
        $this->authorizeAdmin($request);

        return view('admin.academic-sessions.index', [
            'user' => $request->user(),
            'academicSessions' => AcademicSession::query()
                ->orderByRaw("is_current = 'Yes' desc")
                ->orderByDesc('starts_at_year')
                ->paginate(10),
            'currentSession' => AcademicSession::current()->first(),
        ]);
    }

    public function create(Request $request): View
    {
        $this->authorizeAdmin($request);

        return view('admin.academic-sessions.create', [
            'user' => $request->user(),
            'academicSession' => new AcademicSession(),
            'hasCurrentSession' => AcademicSession::current()->exists(),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $this->authorizeAdmin($request);

        $data = $this->validatedData($request);
        $data['is_current'] = $request->boolean('is_current') || AcademicSession::query()->doesntExist() ? 'Yes' : 'No';

        if ($data['is_current'] === 'Yes') {
            $data['is_active'] = 'Yes';
        }

        DB::transaction(function () use ($data): void {
            if ($data['is_current'] === 'Yes') {
                AcademicSession::query()->update(['is_current' => 'No']);
            }

            AcademicSession::create($data);
        });

        return redirect()
            ->route('admin.academic-sessions.index')
            ->with('success', 'Academic session created successfully.');
    }

    public function edit(Request $request, AcademicSession $academicSession): View
    {
        $this->authorizeAdmin($request);

        return view('admin.academic-sessions.edit', [
            'user' => $request->user(),
            'academicSession' => $academicSession,
            'hasCurrentSession' => AcademicSession::current()->where('id', '!=', $academicSession->id)->exists(),
        ]);
    }

    public function update(Request $request, AcademicSession $academicSession): RedirectResponse
    {
        $this->authorizeAdmin($request);

        $data = $this->validatedData($request, $academicSession);
        $hasAnotherCurrentSession = AcademicSession::current()->where('id', '!=', $academicSession->id)->exists();
        $data['is_current'] = $request->boolean('is_current') || ! $hasAnotherCurrentSession ? 'Yes' : 'No';

        if ($data['is_current'] === 'Yes') {
            $data['is_active'] = 'Yes';
        }

        DB::transaction(function () use ($academicSession, $data): void {
            if ($data['is_current'] === 'Yes') {
                AcademicSession::query()
                    ->where('id', '!=', $academicSession->id)
                    ->update(['is_current' => 'No']);
            }

            $academicSession->update($data);
        });

        return redirect()
            ->route('admin.academic-sessions.index')
            ->with('success', 'Academic session updated successfully.');
    }

    public function makeCurrent(Request $request, AcademicSession $academicSession): RedirectResponse
    {
        $this->authorizeAdmin($request);

        DB::transaction(function () use ($academicSession): void {
            AcademicSession::query()->where('id', '!=', $academicSession->id)->update(['is_current' => 'No']);
            $academicSession->update([
                'is_current' => 'Yes',
                'is_active' => 'Yes',
            ]);
        });

        return back()->with('success', 'Current academic session updated successfully.');
    }

    public function destroy(Request $request, AcademicSession $academicSession): RedirectResponse
    {
        $this->authorizeAdmin($request);

        if ($academicSession->is_current === 'Yes') {
            return back()->with('error', 'You cannot delete the current active session. Make another session current first.');
        }

        $academicSession->delete();

        return back()->with('success', 'Academic session deleted successfully.');
    }

    private function authorizeAdmin(Request $request): void
    {
        abort_unless(strtolower((string) $request->user()?->role) === 'admin', 403);
    }

    private function validatedData(Request $request, ?AcademicSession $academicSession = null): array
    {
        return $request->validate([
            'name' => [
                'required',
                'string',
                'max:20',
                Rule::unique('academic_sessions', 'name')->ignore($academicSession),
            ],
            'starts_at_year' => ['required', 'integer', 'digits:4', 'min:2000'],
            'ends_at_year' => ['required', 'integer', 'digits:4', 'gte:starts_at_year'],
            'is_active' => ['required', Rule::in(['Yes', 'No'])],
        ]);
    }
}
