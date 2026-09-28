<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\BankAccount;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class BankAccountController extends Controller
{
    public function index(Request $request): View
    {
        $this->authorizeAdmin($request);

        return view('admin.bank-accounts.index', [
            'user' => $request->user(),
            'bankAccounts' => BankAccount::query()->with('updatedBy')->ordered()->paginate(15),
            'activeTotal' => BankAccount::active()->count(),
        ]);
    }

    public function create(Request $request): View
    {
        $this->authorizeAdmin($request);

        return view('admin.bank-accounts.create', [
            'user' => $request->user(),
            'bankAccount' => new BankAccount(['is_active' => 'Yes', 'sort_order' => 0]),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $this->authorizeAdmin($request);

        BankAccount::create([
            ...$this->validatedData($request),
            'updated_by' => $request->user()->id,
        ]);

        return redirect()
            ->route('admin.bank-accounts.index')
            ->with('success', 'Bank account added successfully.');
    }

    public function edit(Request $request, BankAccount $bankAccount): View
    {
        $this->authorizeAdmin($request);

        return view('admin.bank-accounts.edit', [
            'user' => $request->user(),
            'bankAccount' => $bankAccount,
        ]);
    }

    public function update(Request $request, BankAccount $bankAccount): RedirectResponse
    {
        $this->authorizeAdmin($request);

        $bankAccount->update([
            ...$this->validatedData($request, $bankAccount),
            'updated_by' => $request->user()->id,
        ]);

        return redirect()
            ->route('admin.bank-accounts.index')
            ->with('success', 'Bank account updated successfully.');
    }

    public function destroy(Request $request, BankAccount $bankAccount): RedirectResponse
    {
        $this->authorizeAdmin($request);

        $bankAccount->delete();

        return back()->with('success', 'Bank account deleted successfully.');
    }

    private function authorizeAdmin(Request $request): void
    {
        abort_unless(strtolower((string) $request->user()?->role) === 'admin', 403);
    }

    private function validatedData(Request $request, ?BankAccount $bankAccount = null): array
    {
        $request->merge([
            'account_number' => preg_replace('/\s+/', '', (string) $request->input('account_number')),
        ]);

        $data = $request->validate([
            'bank_name' => ['required', 'string', 'max:100'],
            'account_name' => ['required', 'string', 'max:150'],
            'account_number' => [
                'required',
                'digits_between:10,20',
                Rule::unique('bank_accounts', 'account_number')
                    ->where('bank_name', $request->input('bank_name'))
                    ->ignore($bankAccount),
            ],
            'instructions' => ['nullable', 'string', 'max:255'],
            'sort_order' => ['nullable', 'integer', 'min:0', 'max:999'],
            'is_active' => ['required', Rule::in(['Yes', 'No'])],
        ], [
            'account_number.unique' => 'This account number is already saved for the selected bank.',
            'account_number.digits_between' => 'The account number must contain only digits (10 to 20).',
        ]);

        $data['sort_order'] = (int) ($data['sort_order'] ?? 0);

        return $data;
    }
}
