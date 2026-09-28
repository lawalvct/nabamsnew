<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AcademicSession;
use App\Models\AppSetting;
use App\Models\Level;
use App\Models\PriceSetting;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class PriceSettingController extends Controller
{
    public function index(Request $request): View
    {
        $this->authorizeAdmin($request);

        $filters = $request->validate([
            'academic_session_id' => ['nullable', 'integer'],
            'level_id' => ['nullable', 'integer'],
            'semester' => ['nullable', Rule::in(PriceSetting::SEMESTERS)],
        ]);

        $priceSettings = PriceSetting::query()
            ->with(['academicSession', 'level', 'updatedBy'])
            ->when($filters['academic_session_id'] ?? null, fn ($query, $id) => $query->where('academic_session_id', $id))
            ->when($filters['level_id'] ?? null, fn ($query, $id) => $query->where('level_id', $id))
            ->when($filters['semester'] ?? null, fn ($query, $semester) => $query->where('semester', $semester))
            ->orderByDesc('academic_session_id')
            ->orderBy(Level::select('sort_order')->whereColumn('levels.id', 'price_settings.level_id'))
            ->orderBy('semester')
            ->orderBy('name')
            ->paginate(15)
            ->withQueryString();

        return view('admin.price-settings.index', [
            'user' => $request->user(),
            'priceSettings' => $priceSettings,
            'filters' => $filters,
            'academicSessions' => AcademicSession::query()->orderByDesc('starts_at_year')->get(),
            'levels' => Level::query()->orderBy('sort_order')->get(),
            'semesters' => PriceSetting::SEMESTERS,
            'currentSession' => AcademicSession::current()->first(),
            'activeTotal' => PriceSetting::active()->count(),
            'levelsWithoutPrice' => $this->levelsWithoutPrice(),
        ]);
    }

    /**
     * Levels that currently have nothing to pay, so their members are never locked out.
     */
    private function levelsWithoutPrice()
    {
        $session = AcademicSession::current()->first();

        if (! $session || AppSetting::paymentRequirementMode() === AppSetting::PAYMENT_OFF) {
            return collect();
        }

        $semester = AppSetting::paymentRequirementMode() === AppSetting::PAYMENT_SESSION_SEMESTER
            ? $session->current_semester
            : null;

        $pricedLevelIds = PriceSetting::active()
            ->where('academic_session_id', $session->id)
            ->when($semester, fn ($query) => $query->where('semester', $semester))
            ->pluck('level_id');

        return Level::active()->whereNotIn('id', $pricedLevelIds)->orderBy('sort_order')->pluck('name');
    }

    public function create(Request $request): View
    {
        $this->authorizeAdmin($request);

        $currentSession = AcademicSession::current()->first();

        return view('admin.price-settings.create', $this->formData($request, new PriceSetting([
            'academic_session_id' => $currentSession?->id,
            'semester' => $currentSession?->current_semester ?? 'First',
            'is_active' => 'Yes',
        ])));
    }

    public function store(Request $request): RedirectResponse
    {
        $this->authorizeAdmin($request);

        PriceSetting::create([
            ...$this->validatedData($request),
            'updated_by' => $request->user()->id,
        ]);

        return redirect()
            ->route('admin.price-settings.index')
            ->with('success', 'Price setting created successfully.');
    }

    public function edit(Request $request, PriceSetting $priceSetting): View
    {
        $this->authorizeAdmin($request);

        return view('admin.price-settings.edit', $this->formData($request, $priceSetting));
    }

    public function update(Request $request, PriceSetting $priceSetting): RedirectResponse
    {
        $this->authorizeAdmin($request);

        $priceSetting->update([
            ...$this->validatedData($request, $priceSetting),
            'updated_by' => $request->user()->id,
        ]);

        return redirect()
            ->route('admin.price-settings.index')
            ->with('success', 'Price setting updated successfully.');
    }

    public function destroy(Request $request, PriceSetting $priceSetting): RedirectResponse
    {
        $this->authorizeAdmin($request);

        $priceSetting->delete();

        return back()->with('success', 'Price setting deleted successfully.');
    }

    private function authorizeAdmin(Request $request): void
    {
        abort_unless(strtolower((string) $request->user()?->role) === 'admin', 403);
    }

    private function formData(Request $request, PriceSetting $priceSetting): array
    {
        return [
            'user' => $request->user(),
            'priceSetting' => $priceSetting,
            'academicSessions' => AcademicSession::query()->orderByDesc('starts_at_year')->get(),
            'levels' => Level::query()->orderBy('sort_order')->get(),
            'semesters' => PriceSetting::SEMESTERS,
        ];
    }

    private function validatedData(Request $request, ?PriceSetting $priceSetting = null): array
    {
        return $request->validate([
            'name' => [
                'required',
                'string',
                'max:100',
                Rule::unique('price_settings', 'name')
                    ->where('academic_session_id', $request->input('academic_session_id'))
                    ->where('level_id', $request->input('level_id'))
                    ->where('semester', $request->input('semester'))
                    ->ignore($priceSetting),
            ],
            'amount' => ['required', 'integer', 'min:0', 'max:100000000'],
            'academic_session_id' => ['required', 'integer', Rule::exists('academic_sessions', 'id')],
            'level_id' => ['required', 'integer', Rule::exists('levels', 'id')],
            'semester' => ['required', Rule::in(PriceSetting::SEMESTERS)],
            'is_active' => ['required', Rule::in(['Yes', 'No'])],
        ], [
            'name.unique' => 'A price with this name already exists for the selected session, level and semester.',
        ]);
    }
}
