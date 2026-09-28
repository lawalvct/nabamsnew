<?php

namespace App\Http\Controllers;

use App\Models\BankAccount;
use App\Models\Payment;
use App\Notifications\PaymentStatusChanged;
use App\Support\PaymentRequirement;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\Response;

class PaymentController extends Controller
{
    public const EVIDENCE_DISK = 'local';

    public function index(Request $request): View
    {
        $user = $request->user();

        return view('payments.index', [
            'user' => $user,
            'requirement' => PaymentRequirement::for($user),
            'payments' => $user->payments()
                ->with('academicSession')
                ->where('status', '!=', Payment::STATUS_AWAITING)
                ->latest('id')
                ->paginate(15),
        ]);
    }

    public function create(Request $request): View
    {
        $user = $request->user();
        $requirement = PaymentRequirement::for($user);

        return view('payments.create', [
            'user' => $user,
            'requirement' => $requirement,
            'payment' => $requirement->openPayment($user),
            'bankAccounts' => BankAccount::active()->ordered()->get(),
        ]);
    }

    public function submit(Request $request, Payment $payment): RedirectResponse
    {
        abort_unless($payment->user_id === $request->user()->id, 403);

        if (! $payment->canBeSubmitted()) {
            return redirect()->route('payments.create')->with('error', 'This payment has already been submitted.');
        }

        $validated = $request->validate([
            'bank_account_id' => ['required', 'integer', Rule::exists('bank_accounts', 'id')->where('is_active', 'Yes')],
            'amount_paid' => ['required', 'integer', 'min:1', 'max:100000000'],
            'payer_name' => ['required', 'string', 'max:150'],
            'paid_at' => ['required', 'date', 'before_or_equal:today'],
            'evidence' => ['required', 'file', 'mimes:jpg,jpeg,png,webp,pdf', 'max:5120'],
        ], [
            'evidence.required' => 'Please upload your transfer receipt or evidence of payment.',
            'evidence.mimes' => 'Upload the evidence as an image (JPG, PNG, WEBP) or a PDF.',
            'evidence.max' => 'The evidence file must not be larger than 5MB.',
        ]);

        $bankAccount = BankAccount::findOrFail($validated['bank_account_id']);
        $previousEvidence = $payment->evidence_path;

        $payment->update([
            'bank_account_id' => $bankAccount->id,
            'bank_snapshot' => $bankAccount->only(['bank_name', 'account_name', 'account_number']),
            'amount_paid' => $validated['amount_paid'],
            'payer_name' => $validated['payer_name'],
            'paid_at' => $validated['paid_at'],
            'evidence_path' => $request->file('evidence')->store('payment_evidence', self::EVIDENCE_DISK),
            'status' => Payment::STATUS_PENDING,
            'submitted_at' => now(),
            'reviewed_at' => null,
            'reviewed_by' => null,
            'reviewer_name' => null,
            'rejection_reason' => null,
        ]);

        if ($previousEvidence && $previousEvidence !== $payment->evidence_path) {
            Storage::disk(self::EVIDENCE_DISK)->delete($previousEvidence);
        }

        PaymentStatusChanged::sendTo($payment);

        return redirect()
            ->route('payments.create')
            ->with('success', 'Payment evidence submitted. Please allow up to 24 hours for an admin to verify it.');
    }

    public function evidence(Request $request, Payment $payment): Response
    {
        $this->authorizeView($request, $payment);

        abort_unless($payment->evidence_path && Storage::disk(self::EVIDENCE_DISK)->exists($payment->evidence_path), 404);

        return response()->file(Storage::disk(self::EVIDENCE_DISK)->path($payment->evidence_path), [
            'Cache-Control' => 'private, max-age=0, no-store',
        ]);
    }

    public function receipt(Request $request, Payment $payment): Response
    {
        $this->authorizeView($request, $payment);

        abort_unless($payment->isApproved(), 404);

        $payment->loadMissing(['user', 'academicSession']);

        return Pdf::loadView('payments.receipt-pdf', ['payment' => $payment])
            ->setPaper('a4')
            ->download("nabams-receipt-{$payment->reference}.pdf");
    }

    private function authorizeView(Request $request, Payment $payment): void
    {
        $user = $request->user();

        abort_unless($payment->user_id === $user->id || strtolower((string) $user->role) === 'admin', 403);
    }
}
