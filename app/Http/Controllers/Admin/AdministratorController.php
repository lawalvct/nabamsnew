<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Password;
use Illuminate\View\View;

class AdministratorController extends Controller
{
    public function index(Request $request): View
    {
        $this->authorizeAdmin($request);

        $search = trim((string) $request->query('search'));
        $status = $request->query('is_active');

        $admins = User::query()
            ->where('role', 'Admin')
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
            ->latest()
            ->paginate(15)
            ->withQueryString();

        return view('admin.admins.index', [
            'admins' => $admins,
            'search' => $search,
            'status' => $status,
            'counts' => [
                'total' => User::where('role', 'Admin')->count(),
                'active' => User::where('role', 'Admin')->where('is_active', 'Yes')->count(),
                'banned' => User::where('role', 'Admin')->where('is_ban', 'Yes')->count(),
            ],
        ]);
    }

    public function create(Request $request): View
    {
        $this->authorizeAdmin($request);

        return view('admin.admins.create', [
            'admin' => new User([
                'is_active' => 'Yes',
                'is_ban' => 'No',
            ]),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $this->authorizeAdmin($request);

        $data = $request->validate([
            'firstname' => ['required', 'string', 'max:50'],
            'lastname' => ['nullable', 'string', 'max:50'],
            'nickname' => ['nullable', 'string', 'max:50'],
            'email' => ['required', 'string', 'email', 'max:60', Rule::unique('users', 'email')],
            'matno' => ['required', 'string', 'max:30', Rule::unique('users', 'matno')],
            'phone' => ['required', 'string', 'max:30'],
            'is_active' => ['required', Rule::in(['Yes', 'No'])],
            'is_ban' => ['required', Rule::in(['Yes', 'No'])],
            'password' => ['required', 'confirmed', Password::defaults()],
        ]);

        $data['email'] = strtolower($data['email']);
        $data['matno'] = strtoupper($data['matno']);
        $data['role'] = 'Admin';
        $data['user_role_id'] = 1;
        $data['department'] = 'Business Administration & Management';
        $data['member_type'] = 'Regular';
        $data['fee_paid'] = 'Yes';

        User::create($data);

        return redirect()
            ->route('admin.admins.index')
            ->with('success', 'Admin account created successfully.');
    }

    public function edit(Request $request, User $admin): View
    {
        $this->authorizeAdmin($request);
        $this->authorizeAdminRecord($admin);

        return view('admin.admins.edit', [
            'admin' => $admin,
        ]);
    }

    public function update(Request $request, User $admin): RedirectResponse
    {
        $this->authorizeAdmin($request);
        $this->authorizeAdminRecord($admin);

        $data = $request->validate([
            'firstname' => ['required', 'string', 'max:50'],
            'lastname' => ['nullable', 'string', 'max:50'],
            'nickname' => ['nullable', 'string', 'max:50'],
            'email' => ['required', 'string', 'email', 'max:60', Rule::unique('users', 'email')->ignore($admin->id)],
            'matno' => ['required', 'string', 'max:30', Rule::unique('users', 'matno')->ignore($admin->id)],
            'phone' => ['required', 'string', 'max:30'],
            'is_active' => ['required', Rule::in(['Yes', 'No'])],
            'is_ban' => ['required', Rule::in(['Yes', 'No'])],
            'password' => ['nullable', 'confirmed', Password::defaults()],
        ]);

        if ($admin->is($request->user()) && ($data['is_active'] === 'No' || $data['is_ban'] === 'Yes')) {
            return back()
                ->withInput()
                ->with('error', 'You cannot deactivate or ban your own admin account.');
        }

        $data['email'] = strtolower($data['email']);
        $data['matno'] = strtoupper($data['matno']);
        $data['role'] = 'Admin';
        $data['user_role_id'] = 1;

        if (blank($data['password'])) {
            unset($data['password']);
        }

        $admin->update($data);

        return redirect()
            ->route('admin.admins.index')
            ->with('success', 'Admin account updated successfully.');
    }

    public function destroy(Request $request, User $admin): RedirectResponse
    {
        $this->authorizeAdmin($request);
        $this->authorizeAdminRecord($admin);

        if ($admin->is($request->user())) {
            return back()->with('error', 'You cannot delete your own admin account while logged in.');
        }

        if (User::where('role', 'Admin')->count() <= 1) {
            return back()->with('error', 'At least one admin account must remain.');
        }

        if ($this->hasProtectedRecords($admin)) {
            return back()->with('error', 'This admin has management records. Deactivate the account instead of deleting it.');
        }

        $admin->delete();

        return redirect()
            ->route('admin.admins.index')
            ->with('success', 'Admin account deleted successfully.');
    }

    private function authorizeAdmin(Request $request): void
    {
        abort_unless(strtolower((string) $request->user()?->role) === 'admin', 403);
    }

    private function authorizeAdminRecord(User $admin): void
    {
        abort_unless($admin->role === 'Admin', 404);
    }

    private function hasProtectedRecords(User $admin): bool
    {
        return (Schema::hasTable('election_positions') && DB::table('election_positions')->where('admin_id', $admin->id)->exists())
            || (Schema::hasTable('price_settings') && DB::table('price_settings')->where('updated_by', $admin->id)->exists());
    }
}
