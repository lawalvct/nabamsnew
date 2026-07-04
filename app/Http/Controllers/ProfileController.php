<?php

namespace App\Http\Controllers;

use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Password;
use Illuminate\View\View;

class ProfileController extends Controller
{
    public function photo(string $directory, string $filename)
    {
        abort_unless(in_array($directory, ['profile_photos', 'passport_photographs'], true), 404);

        $path = $directory.'/'.basename($filename);

        abort_unless(Storage::disk('public')->exists($path), 404);

        return response()->file(Storage::disk('public')->path($path), [
            'Cache-Control' => 'private, max-age=604800',
        ]);
    }

    public function edit(Request $request): View
    {
        $user = $request->user();
        $displayName = trim($user->firstname.' '.$user->lastname) ?: 'Member';
        $initials = strtoupper(
            substr($user->firstname ?? '', 0, 1).substr($user->lastname ?? '', 0, 1)
        ) ?: 'NM';

        return view('profile.edit', compact('user', 'displayName', 'initials'));
    }

    public function update(Request $request): RedirectResponse
    {
        $user = $request->user();

        $validated = $request->validate([
            'firstname' => ['required', 'string', 'max:50'],
            'lastname' => ['nullable', 'string', 'max:50'],
            'nickname' => ['nullable', 'string', 'max:50'],
            'gender' => ['nullable', Rule::in(['Male', 'Female', 'Other'])],
            'dob' => ['nullable', 'date', 'before:today'],
            'email' => ['required', 'string', 'email', 'max:60', Rule::unique('users', 'email')->ignore($user->id)],
            'phone' => ['required', 'string', 'max:30'],
            'whatsapp_number' => ['nullable', 'string', 'max:30'],
            'matno' => ['required', 'string', 'max:30', 'regex:/^(HBAF|NBAF)\/(2[1-5][A-Z]?)\/[0-9]{4}$/i', Rule::unique('users', 'matno')->ignore($user->id)],
            'academic_level' => ['required', Rule::in(['ND1', 'ND2', 'ND3', 'HND1', 'HND2', 'HND3', 'GRADUATE'])],
            'member_type' => ['required', Rule::in(['Regular', 'Part-time', 'Alumni'])],
            'home_address' => ['nullable', 'string', 'max:1000'],
            'bio' => ['nullable', 'string', 'max:1500'],
            'facebook_link' => ['nullable', 'url', 'max:100'],
            'x_link' => ['nullable', 'url', 'max:100'],
            'linkedin_link' => ['nullable', 'url', 'max:100'],
            'profile_photo' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:2048', 'dimensions:max_width=2400,max_height=2400'],
        ], [
            'matno.regex' => 'Matric number must be in the format HBAF/YY/0000 or NBAF/YY/0000, with an optional year letter (year 21-25).',
            'profile_photo.dimensions' => 'Profile photo must not be larger than 2400px by 2400px.',
        ]);

        if ($request->hasFile('profile_photo')) {
            $this->deleteStoredProfilePhoto($user->image);
            $validated['image'] = $request->file('profile_photo')->store('profile_photos', 'public');
        }

        unset($validated['profile_photo']);
        $validated['email'] = strtolower($validated['email']);
        $validated['matno'] = strtoupper($validated['matno']);
        $validated['department'] = 'Business Administration & Management';
        $validated['level_id'] = $this->levelIdFromAcademicLevel($validated['academic_level']);

        $user->update($validated);

        return back()->with('success', 'Profile updated successfully.');
    }

    public function updatePassword(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'current_password' => ['required', 'string'],
            'password' => ['required', 'confirmed', Password::min(8)->letters()->numbers()],
        ]);

        if (! Hash::check($validated['current_password'], $request->user()->password)) {
            return back()
                ->withErrors(['current_password' => 'The current password is incorrect.'])
                ->onlyInput('current_password');
        }

        $request->user()->update([
            'password' => $validated['password'],
        ]);

        return back()->with('password_success', 'Password updated successfully.');
    }

    private function deleteStoredProfilePhoto(?string $path): void
    {
        if (! $path || str_starts_with($path, 'uploads/') || str_starts_with($path, 'storage/') || str_starts_with($path, 'http')) {
            return;
        }

        if (str_starts_with($path, 'profile_photos/') || str_starts_with($path, 'passport_photographs/')) {
            Storage::disk('public')->delete($path);
        }
    }

    private function levelIdFromAcademicLevel(string $academicLevel): int
    {
        return [
            'ND1' => 1,
            'ND2' => 2,
            'ND3' => 3,
            'HND1' => 4,
            'HND2' => 5,
            'HND3' => 6,
            'GRADUATE' => 7,
        ][$academicLevel];
    }
}
