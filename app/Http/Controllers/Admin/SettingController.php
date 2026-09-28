<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AppSetting;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class SettingController extends Controller
{
    public function edit(Request $request): View
    {
        $this->authorizeAdmin($request);

        return view('admin.settings.edit', [
            'electionSetting' => AppSetting::election(),
            'registrationSetting' => AppSetting::registration(),
            'paymentSetting' => AppSetting::paymentRequirement(),
        ]);
    }

    public function update(Request $request): RedirectResponse
    {
        $this->authorizeAdmin($request);

        $validated = $request->validate([
            'election' => ['required', Rule::in(['On', 'Off'])],
            'registration' => ['required', Rule::in(['On', 'Off'])],
            'payment_requirement' => ['sometimes', 'required', Rule::in(AppSetting::PAYMENT_REQUIREMENTS)],
        ]);

        AppSetting::election()->update([
            'value' => $validated['election'],
            'active' => 'Yes',
        ]);

        AppSetting::registration()->update([
            'value' => $validated['registration'],
            'active' => 'Yes',
        ]);

        if (isset($validated['payment_requirement'])) {
            AppSetting::paymentRequirement()->update([
                'value' => $validated['payment_requirement'],
                'active' => 'Yes',
            ]);
        }

        return back()->with('success', 'Settings updated successfully.');
    }

    private function authorizeAdmin(Request $request): void
    {
        abort_unless(strtolower((string) $request->user()?->role) === 'admin', 403);
    }
}
