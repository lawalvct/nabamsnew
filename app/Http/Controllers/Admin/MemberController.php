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
            'matno' => ['required', 'string', 'max:30', Rule::unique('users', 'matno')->ignore($member->id)],
            'phone' => ['required', 'string', 'max:30'],
            'whatsapp_number' => ['nullable', 'string', 'max:30'],
            'academic_level' => ['required', Rule::in(['ND1', 'ND2', 'ND3', 'HND1', 'HND2', 'HND3', 'GRADUATE'])],
            'member_type' => ['required', Rule::in(['Regular', 'Part-time', 'Alumni'])],
            'is_active' => ['required', Rule::in(['Yes', 'No'])],
            'is_ban' => ['required', Rule::in(['Yes', 'No'])],
            'fee_paid' => ['required', Rule::in(['Yes', 'No'])],
            'home_address' => ['nullable', 'string', 'max:1000'],
            'bio' => ['nullable', 'string', 'max:1500'],
        ]);

        $validated['email'] = strtolower($validated['email']);
        $validated['matno'] = strtoupper($validated['matno']);
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
