<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rules\Password;
use Illuminate\View\View;

class RegisteredUserController extends Controller
{
    public function create(): View
    {
        return view('auth.register');
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'firstname' => ['required', 'string', 'max:50'],
            'lastname' => ['nullable', 'string', 'max:50'],
            'nickname' => ['nullable', 'string', 'max:50'],
            'email' => ['required', 'string', 'email', 'max:60', 'unique:users,email'],
            'password' => ['required', 'confirmed', Password::min(8)],
            'matno' => ['nullable', 'string', 'max:30', 'unique:users,matno'],
            'phone' => ['required', 'string', 'max:30'],
            'level_id' => ['nullable', 'integer', 'min:1', 'max:10'],
            'member_type' => ['required', 'string', 'in:Regular,Alumni,Part-time'],
            'expectation_msg' => ['nullable', 'string', 'max:2000'],
            'session_start' => ['nullable', 'string', 'max:15'],
            'session_end' => ['nullable', 'string', 'max:15'],
        ]);

        $user = User::create([
            ...$validated,
            'level_id' => $validated['level_id'] ?? 1,
            'email' => strtolower($validated['email']),
            'password' => $validated['password'],
            'is_active' => 'Yes',
            'is_ban' => 'No',
            'fee_paid' => 'No',
            'role' => 'Member',
            'user_role_id' => 2,
        ]);

        Auth::login($user);
        $request->session()->regenerate();

        return redirect()->route('dashboard');
    }
}
