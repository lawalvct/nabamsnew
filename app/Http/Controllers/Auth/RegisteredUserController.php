<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\AppSetting;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rules\Password;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class RegisteredUserController extends Controller
{
    public function create(Request $request): View
    {
        if (! AppSetting::registrationEnabled()) {
            return view('auth.registration-closed');
        }

        if (! $request->session()->has('registration_security_answer')) {
            $this->setSecurityQuestion($request);
        }

        return view('auth.register', [
            'securityQuestion' => $request->session()->get('registration_security_question'),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        if (! AppSetting::registrationEnabled()) {
            return redirect()
                ->route('register')
                ->with('registration_disabled', 'Member registration is currently disabled. Please contact the admin for assistance.');
        }

        $validated = $request->validate([
            'full_name' => ['required', 'string', 'max:120'],
            'gender' => ['required', 'string', 'in:Male,Female,Other'],
            'dob' => ['required', 'date', 'before:today'],
            'phone' => ['required', 'string', 'max:30'],
            'email' => ['required', 'string', 'email', 'max:60', 'unique:users,email'],
            'matno' => ['required', 'string', 'max:30', 'unique:users,matno', 'regex:/^(HBAF|NBAF)\/(2[1-5][A-Z]?)\/[0-9]{4}$/i'],
            'department' => ['required', 'string', 'in:Business Administration & Management'],
            'academic_level' => ['required', 'string', 'in:ND1,ND2,ND3,HND1,HND2,HND3,GRADUATE'],
            'member_type' => ['required', 'string', 'in:Regular,Part-time,Alumni'],
            'security_answer' => ['required', 'regex:/^\d{1,2}$/'],
            'password' => ['required', 'confirmed', Password::min(8)],
            'passport_photograph' => ['nullable', 'image', 'max:2048'],
            'whatsapp_number' => ['nullable', 'string', 'max:30'],
            'home_address' => ['nullable', 'string', 'max:1000'],
            'website' => ['nullable', 'size:0'],
        ], [
            'matno.regex'           => 'Matric number must be in the format HBAF/YY/0000 or NBAF/YY/0000, with an optional year letter (year 21-25).',
            'security_answer.regex' => 'The security answer must be a number with at most 2 digits.',
        ]);

        if ((int) $validated['security_answer'] !== (int) $request->session()->get('registration_security_answer')) {
            $this->setSecurityQuestion($request);

            throw ValidationException::withMessages([
                'security_answer' => 'Incorrect security answer.',
            ]);
        }

        [$firstname, $lastname] = $this->splitFullName($validated['full_name']);
        $imagePath = $request->file('passport_photograph')?->store('passport_photographs', 'public');

        $user = User::create([
            'firstname' => $firstname,
            'lastname' => $lastname,
            'gender' => $validated['gender'],
            'dob' => $validated['dob'],
            'phone' => $validated['phone'],
            'whatsapp_number' => $validated['whatsapp_number'] ?? null,
            'home_address' => $validated['home_address'] ?? null,
            'email' => strtolower($validated['email']),
            'matno' => strtoupper($validated['matno']),
            'department' => $validated['department'],
            'academic_level' => $validated['academic_level'],
            'level_id' => $this->levelIdFromAcademicLevel($validated['academic_level']),
            'member_type' => $validated['member_type'],
            'image' => $imagePath,
            'password' => $validated['password'],
            'is_active' => 'Yes',
            'is_ban' => 'No',
            'fee_paid' => 'No',
            'role' => 'Member',
            'user_role_id' => 2,
        ]);

        $request->session()->forget(['registration_security_question', 'registration_security_answer']);

        Auth::login($user);
        $request->session()->regenerate();

        return redirect()->route('dashboard');
    }

    /**
     * @return array{0: string, 1: string|null}
     */
    private function splitFullName(string $fullName): array
    {
        $parts = preg_split('/\s+/', trim($fullName), 2);

        return [$parts[0], $parts[1] ?? null];
    }

    private function setSecurityQuestion(Request $request): void
    {
        $firstNumber = random_int(1, 9);
        $secondNumber = random_int(1, 9);

        $request->session()->put('registration_security_question', "{$firstNumber} + {$secondNumber} = ?");
        $request->session()->put('registration_security_answer', $firstNumber + $secondNumber);
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
