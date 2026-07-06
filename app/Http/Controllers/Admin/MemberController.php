<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class MemberController extends Controller
{
    private const DEFAULT_MEMBER_PASSWORD = '12345678';

    public function index(Request $request): View
    {
        $this->authorizeAdmin($request);

        $search = trim((string) $request->query('search'));
        $status = $request->query('is_active');
        $feePaid = $request->query('fee_paid');

        $members = User::query()
            ->where('role', 'Member')
            ->when($search, function ($query) use ($search): void {
                $query->where(function ($query) use ($search): void {
                    $query->where('firstname', 'like', "%{$search}%")
                        ->orWhere('lastname', 'like', "%{$search}%")
                        ->orWhere('email', 'like', "%{$search}%")
                        ->orWhere('matno', 'like', "%{$search}%")
                        ->orWhere('phone', 'like', "%{$search}%");
                });
            })
            ->when(in_array($status, ['Yes', 'No'], true), fn ($query) => $query->where('is_active', $status))
            ->when(in_array($feePaid, ['Yes', 'No'], true), fn ($query) => $query->where('fee_paid', $feePaid))
            ->latest()
            ->paginate(15)
            ->withQueryString();

        return view('admin.members.index', [
            'members' => $members,
            'search' => $search,
            'status' => $status,
            'feePaid' => $feePaid,
            'counts' => [
                'total' => User::where('role', 'Member')->count(),
                'active' => User::where('role', 'Member')->where('is_active', 'Yes')->count(),
                'paid' => User::where('role', 'Member')->where('fee_paid', 'Yes')->count(),
                'banned' => User::where('role', 'Member')->where('is_ban', 'Yes')->count(),
            ],
        ]);
    }

    public function create(Request $request): View
    {
        $this->authorizeAdmin($request);

        return view('admin.members.create');
    }

    public function store(Request $request): RedirectResponse
    {
        $this->authorizeAdmin($request);

        $validated = $request->validate([
            'full_name' => ['required', 'string', 'max:120'],
            'gender' => ['required', Rule::in(['Male', 'Female', 'Other'])],
            'email' => ['required', 'string', 'email', 'max:60', Rule::unique('users', 'email')],
            'phone' => ['required', 'string', 'max:30'],
            'matno' => ['nullable', 'string', 'max:30', 'regex:/^(HBAF|NBAF)\/(2[1-5][A-Z]?)\/[0-9]{4}$/i', Rule::unique('users', 'matno')],
        ], [
            'matno.regex' => 'Matric number must be in the format HBAF/YY/0000 or NBAF/YY/0000, with an optional year letter (year 21-25).',
        ]);

        [$firstname, $lastname] = $this->splitFullName($validated['full_name']);

        $member = User::create([
            'firstname' => $firstname,
            'lastname' => $lastname,
            'gender' => $validated['gender'],
            'email' => strtolower($validated['email']),
            'phone' => $validated['phone'],
            'matno' => filled($validated['matno'] ?? null) ? strtoupper($validated['matno']) : null,
            'password' => self::DEFAULT_MEMBER_PASSWORD,
            'department' => 'Business Administration & Management',
            'academic_level' => 'ND1',
            'level_id' => 1,
            'member_type' => 'Regular',
            'is_active' => 'Yes',
            'is_ban' => 'No',
            'fee_paid' => 'No',
            'role' => 'Member',
            'user_role_id' => 2,
        ]);

        return redirect()
            ->route('admin.members.show', $member)
            ->with('success', 'Member created successfully. Advise the member to change their password after login. The default password is '.self::DEFAULT_MEMBER_PASSWORD.'.');
    }

    public function show(Request $request, User $member): View
    {
        $this->authorizeAdmin($request);
        $this->authorizeMemberRecord($member);

        return view('admin.members.show', [
            'member' => $member,
        ]);
    }

    public function edit(Request $request, User $member): View
    {
        $this->authorizeAdmin($request);
        $this->authorizeMemberRecord($member);

        return view('admin.members.edit', [
            'member' => $member,
        ]);
    }

    public function update(Request $request, User $member): RedirectResponse
    {
        $this->authorizeAdmin($request);
        $this->authorizeMemberRecord($member);

        $validated = $request->validate([
            'firstname' => ['required', 'string', 'max:50'],
            'lastname' => ['nullable', 'string', 'max:50'],
            'nickname' => ['nullable', 'string', 'max:50'],
            'gender' => ['nullable', Rule::in(['Male', 'Female', 'Other'])],
            'dob' => ['nullable', 'date', 'before:today'],
            'email' => ['required', 'string', 'email', 'max:60', Rule::unique('users', 'email')->ignore($member->id)],
            'matno' => ['nullable', 'string', 'max:30', 'regex:/^(HBAF|NBAF)\/(2[1-5][A-Z]?)\/[0-9]{4}$/i', Rule::unique('users', 'matno')->ignore($member->id)],
            'phone' => ['required', 'string', 'max:30'],
            'whatsapp_number' => ['nullable', 'string', 'max:30'],
            'academic_level' => ['required', Rule::in(['ND1', 'ND2', 'ND3', 'HND1', 'HND2', 'HND3', 'GRADUATE'])],
            'member_type' => ['required', Rule::in(['Regular', 'Part-time', 'Alumni'])],
            'is_active' => ['required', Rule::in(['Yes', 'No'])],
            'is_ban' => ['required', Rule::in(['Yes', 'No'])],
            'fee_paid' => ['required', Rule::in(['Yes', 'No'])],
            'home_address' => ['nullable', 'string', 'max:1000'],
            'bio' => ['nullable', 'string', 'max:1500'],
        ], [
            'matno.regex' => 'Matric number must be in the format HBAF/YY/0000 or NBAF/YY/0000, with an optional year letter (year 21-25).',
        ]);

        $validated['email'] = strtolower($validated['email']);
        $validated['matno'] = filled($validated['matno'] ?? null) ? strtoupper($validated['matno']) : null;
        $validated['department'] = 'Business Administration & Management';
        $validated['level_id'] = $this->levelIdFromAcademicLevel($validated['academic_level']);
        $validated['role'] = 'Member';
        $validated['user_role_id'] = 2;

        $member->update($validated);

        return redirect()
            ->route('admin.members.show', $member)
            ->with('success', 'Member profile updated successfully.');
    }

    private function authorizeAdmin(Request $request): void
    {
        abort_unless(strtolower((string) $request->user()?->role) === 'admin', 403);
    }

    private function authorizeMemberRecord(User $member): void
    {
        abort_unless($member->role === 'Member', 404);
    }

    /**
     * @return array{0: string, 1: string|null}
     */
    private function splitFullName(string $fullName): array
    {
        $parts = preg_split('/\s+/', trim($fullName), 2);

        return [$parts[0], $parts[1] ?? null];
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
