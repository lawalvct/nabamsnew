<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Controllers\PaymentController as MemberPaymentController;
use App\Models\AcademicSession;
use App\Models\AppSetting;
use App\Models\BankAccount;
use App\Models\Level;
use App\Models\Payment;
use App\Models\PriceSetting;
use App\Models\User;
use App\Notifications\PaymentStatusChanged;
use App\Support\PaymentRequirement;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\StreamedResponse;

class PaymentController extends Controller
{
    public function index(Request $request): View
    {
        $this->authorizeAdmin($request);

        $filters = $this->validatedFilters($request);

        $payments = $this->filteredQuery($filters)
            ->with(['user', 'academicSession'])
            ->paginate(20)
            ->withQueryString();

        return view('admin.payments.index', [
            'user' => $request->user(),
            'payments' => $payments,
            'filters' => $filters,
            'statusCounts' => Payment::query()->selectRaw('status, count(*) as total')->groupBy('status')->pluck('total', 'status'),
            'approvedTotal' => (int) Payment::approved()->sum('amount_paid'),
            'academicSessions' => AcademicSession::query()->orderByDesc('starts_at_year')->get(),
            'levels' => Level::query()->orderBy('sort_order')->get(),
        ]);
    }

    public function export(Request $request): StreamedResponse
    {
        $this->authorizeAdmin($request);

        $query = $this->filteredQuery($this->validatedFilters($request))->with(['user', 'academicSession']);

        return response()->streamDownload(function () use ($query): void {
            $handle = fopen('php://output', 'w');

            fputcsv($handle, ['Reference', 'Member', 'Matric No', 'Email', 'Level', 'Session', 'Semester', 'Amount Due', 'Amount Paid', 'Method', 'Paid Into', 'Payer Name', 'Date Paid', 'Submitted', 'Status', 'Reviewed By', 'Reviewed At', 'Rejection Reason', 'Admin Note']);

            $query->chunk(500, function ($payments) use ($handle): void {
                foreach ($payments as $payment) {
                    fputcsv($handle, [
                        $payment->reference,
                        $payment->user?->name,
                        $payment->user?->matno,
                        $payment->user?->email,
                        $payment->level_name,
                        $payment->academicSession?->name,
                        $payment->semester ?? 'Full Session',
                        $payment->amount_due,
                        $payment->amount_paid,
                        $payment->methodLabel(),
                        $payment->bank_snapshot ? $payment->paidToLabel() : '',
                        $payment->payer_name,
                        $payment->paid_at?->toDateString(),
                        $payment->submitted_at?->toDateTimeString(),
                        $payment->statusLabel(),
                        $payment->reviewer_name,
                        $payment->reviewed_at?->toDateTimeString(),
                        $payment->rejection_reason,
                        $payment->admin_note,
                    ]);
                }
            });

            fclose($handle);
        }, 'nabams-transactions-'.now()->format('Y-m-d-His').'.csv', ['Content-Type' => 'text/csv']);
    }

    public function create(Request $request): View
    {
        $this->authorizeAdmin($request);

        $currentSession = AcademicSession::current()->first();

        return view('admin.payments.create', [
            'user' => $request->user(),
            'member' => $request->filled('member') ? $this->findMember((string) $request->query('member')) : null,
            'academicSessions' => AcademicSession::query()->orderByDesc('starts_at_year')->get(),
            'currentSession' => $currentSession,
            'defaultPeriod' => AppSetting::paymentRequirementMode() === AppSetting::PAYMENT_SESSION
                ? 'Full'
                : ($currentSession?->current_semester ?? 'First'),
            'bankAccounts' => BankAccount::query()->ordered()->get(),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $this->authorizeAdmin($request);

        $validated = $request->validate([
            'member' => ['required', 'string', 'max:150'],
            'academic_session_id' => ['required', 'integer', Rule::exists('academic_sessions', 'id')],
            'period' => ['required', Rule::in([...PriceSetting::SEMESTERS, 'Full'])],
            'payment_method' => ['required', Rule::in(array_keys(Payment::METHODS))],
            'bank_account_id' => ['nullable', 'required_if:payment_method,transfer', 'integer', Rule::exists('bank_accounts', 'id')],
            'amount_paid' => ['required', 'integer', 'min:0', 'max:100000000'],
            'paid_at' => ['required', 'date', 'before_or_equal:today'],
            'admin_note' => ['nullable', 'string', 'max:255'],
            'evidence' => ['nullable', 'file', 'mimes:jpg,jpeg,png,webp,pdf', 'max:5120'],
        ], [
            'bank_account_id.required_if' => 'Select the account the transfer was made into.',
        ]);

        $member = $this->findMember($validated['member']);

        if (! $member) {
            return back()->withInput()->withErrors(['member' => 'No member found with that matric number or email.']);
        }

        $semester = $validated['period'] === 'Full' ? null : $validated['period'];

        $alreadyApproved = Payment::approved()
            ->where('user_id', $member->id)
            ->where('academic_session_id', $validated['academic_session_id'])
            ->where(fn ($query) => $query->whereNull('semester')->when($semester, fn ($query) => $query->orWhere('semester', $semester)))
            ->first();

        if ($alreadyApproved) {
            return back()->withInput()->withErrors(['period' => "{$member->name} already has an approved payment ({$alreadyApproved->reference}) covering this period."]);
        }

        $items = PaymentRequirement::priceItems($member->level_id, (int) $validated['academic_session_id'], $semester);
        $bankAccount = ($validated['bank_account_id'] ?? null) ? BankAccount::find($validated['bank_account_id']) : null;
        $admin = $request->user();

        // Reuse the member's open draft/pending/rejected payment for this period so its reference is kept.
        $payment = Payment::query()
            ->where('user_id', $member->id)
            ->where('academic_session_id', $validated['academic_session_id'])
            ->where(fn ($query) => $semester ? $query->where('semester', $semester) : $query->whereNull('semester'))
            ->where('status', '!=', Payment::STATUS_APPROVED)
            ->latest('id')
            ->first() ?? new Payment([
                'user_id' => $member->id,
                'academic_session_id' => $validated['academic_session_id'],
                'semester' => $semester,
            ]);

        $previousEvidence = $payment->evidence_path;

        $payment->fill([
            'level_id' => $member->level_id,
            'level_name' => Level::query()->whereKey($member->level_id)->value('name') ?? $member->academic_level,
            'items' => $items->all(),
            'amount_due' => (int) $items->sum('amount'),
            'amount_paid' => $validated['amount_paid'],
            'payment_method' => $validated['payment_method'],
            'bank_account_id' => $bankAccount?->id,
            'bank_snapshot' => $bankAccount?->only(['bank_name', 'account_name', 'account_number']),
            'payer_name' => $payment->payer_name ?: $member->name,
            'paid_at' => $validated['paid_at'],
            'status' => Payment::STATUS_APPROVED,
            'submitted_at' => $payment->submitted_at ?? now(),
            'reviewed_at' => now(),
            'reviewed_by' => $admin->id,
            'reviewer_name' => $admin->name ?: $admin->email,
            'rejection_reason' => null,
            'admin_note' => $validated['admin_note'] ?? null,
        ]);

        if ($request->hasFile('evidence')) {
            $payment->evidence_path = $request->file('evidence')->store('payment_evidence', MemberPaymentController::EVIDENCE_DISK);
        }

        $payment->save();

        if ($previousEvidence && $previousEvidence !== $payment->evidence_path) {
            Storage::disk(MemberPaymentController::EVIDENCE_DISK)->delete($previousEvidence);
        }

        $this->syncFeePaid($payment);
        PaymentStatusChanged::sendTo($payment);

        return redirect()
            ->route('admin.payments.show', $payment)
            ->with('success', "Payment {$payment->reference} recorded and approved for {$member->name}.");
    }

    public function show(Request $request, Payment $payment): View
    {
        $this->authorizeAdmin($request);

        $payment->load(['user', 'academicSession', 'reviewer']);

        return view('admin.payments.show', [
            'user' => $request->user(),
            'payment' => $payment,
            'history' => Payment::query()
                ->with('academicSession')
                ->where('user_id', $payment->user_id)
                ->whereKeyNot($payment->id)
                ->where('status', '!=', Payment::STATUS_AWAITING)
                ->latest('id')
                ->limit(10)
                ->get(),
        ]);
    }

    public function approve(Request $request, Payment $payment): RedirectResponse
    {
        $this->authorizeAdmin($request);

        if (! in_array($payment->status, [Payment::STATUS_PENDING, Payment::STATUS_REJECTED], true) || ! $payment->evidence_path) {
            return back()->with('error', 'Only submitted payments with evidence can be approved.');
        }

        $validated = $request->validate([
            'amount_paid' => ['required', 'integer', 'min:0', 'max:100000000'],
        ]);

        $admin = $request->user();

        $payment->update([
            'amount_paid' => $validated['amount_paid'],
            'status' => Payment::STATUS_APPROVED,
            'reviewed_at' => now(),
            'reviewed_by' => $admin->id,
            'reviewer_name' => $admin->name ?: $admin->email,
            'rejection_reason' => null,
        ]);

        $this->syncFeePaid($payment);
        PaymentStatusChanged::sendTo($payment);

        return back()->with('success', "Payment {$payment->reference} approved.");
    }

    public function reject(Request $request, Payment $payment): RedirectResponse
    {
        $this->authorizeAdmin($request);

        if ($payment->status !== Payment::STATUS_PENDING) {
            return back()->with('error', 'Only payments awaiting verification can be rejected.');
        }

        $validated = $request->validate([
            'rejection_reason' => ['required', 'string', 'max:255'],
        ]);

        $admin = $request->user();

        $payment->update([
            'status' => Payment::STATUS_REJECTED,
            'reviewed_at' => now(),
            'reviewed_by' => $admin->id,
            'reviewer_name' => $admin->name ?: $admin->email,
            'rejection_reason' => $validated['rejection_reason'],
        ]);

        $this->syncFeePaid($payment);
        PaymentStatusChanged::sendTo($payment);

        return back()->with('success', "Payment {$payment->reference} rejected. The member has been asked to resubmit.");
    }

    private function validatedFilters(Request $request): array
    {
        return $request->validate([
            'status' => ['nullable', Rule::in(Payment::STATUSES)],
            'academic_session_id' => ['nullable', 'integer'],
            'semester' => ['nullable', Rule::in([...PriceSetting::SEMESTERS, 'Full'])],
            'level_id' => ['nullable', 'integer'],
            'q' => ['nullable', 'string', 'max:100'],
        ]);
    }

    private function filteredQuery(array $filters): Builder
    {
        $status = $filters['status'] ?? null;

        // Unpaid drafts only matter to the member; admins see them only when asked for.
        return Payment::query()
            ->when($status, fn ($query) => $query->where('status', $status), fn ($query) => $query->where('status', '!=', Payment::STATUS_AWAITING))
            ->when($filters['academic_session_id'] ?? null, fn ($query, $id) => $query->where('academic_session_id', $id))
            ->when($filters['semester'] ?? null, fn ($query, $semester) => $semester === 'Full' ? $query->whereNull('semester') : $query->where('semester', $semester))
            ->when($filters['level_id'] ?? null, fn ($query, $id) => $query->where('level_id', $id))
            ->when($filters['q'] ?? null, function ($query, $search) {
                $query->where(function ($query) use ($search) {
                    $query->where('reference', strtoupper($search))
                        ->orWhereHas('user', function ($query) use ($search) {
                            $query->where('matno', 'like', "%{$search}%")
                                ->orWhere('email', 'like', "%{$search}%")
                                ->orWhere('firstname', 'like', "%{$search}%")
                                ->orWhere('lastname', 'like', "%{$search}%");
                        });
                });
            })
            ->orderByRaw("case when status = 'pending' then 0 else 1 end")
            ->latest('submitted_at')
            ->latest('id');
    }

    private function findMember(string $identifier): ?User
    {
        $identifier = trim($identifier);

        return User::query()
            ->where('role', 'Member')
            ->where(fn ($query) => $query->where('matno', strtoupper($identifier))->orWhere('email', strtolower($identifier)))
            ->first();
    }

    private function syncFeePaid(Payment $payment): void
    {
        $member = $payment->user;
        $requirement = PaymentRequirement::for($member);

        if ($requirement->session) {
            $member->forceFill(['fee_paid' => $requirement->satisfied ? 'Yes' : 'No'])->saveQuietly();
        }
    }

    private function authorizeAdmin(Request $request): void
    {
        abort_unless(strtolower((string) $request->user()?->role) === 'admin', 403);
    }
}
