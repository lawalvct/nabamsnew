{{-- Shared checkout for any payment (dues or resource purchase): pending status, or amount + bank details + evidence form. --}}
@php
    $inputClass = 'rounded-lg border border-[#0A2A6B]/15 bg-white px-4 py-3 font-normal text-[#2E2E2E] outline-none transition focus:border-[#F5B400] focus:ring-4 focus:ring-[#F5B400]/20';
@endphp

@if ($payment->status === 'pending')
    <section class="mt-6 grid gap-6 lg:grid-cols-[1.2fr_0.8fr]">
        <div class="rounded-lg bg-white p-6 shadow-sm ring-1 ring-[#0A2A6B]/10 sm:p-8">
            <div class="flex items-center gap-4">
                <div class="grid h-14 w-14 shrink-0 place-items-center rounded-full bg-[#F5B400]/20 text-[#0A2A6B]">
                    <svg class="h-7 w-7 animate-pulse" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="M12 8v4l3 2m6-2a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z" /></svg>
                </div>
                <div>
                    <h2 class="text-2xl font-black text-[#0A2A6B]">Thank you! We're verifying your payment.</h2>
                    <p class="mt-1 text-sm text-[#2E2E2E]/70">Please allow up to <strong>24 hours</strong>. {{ $payment->isResourcePurchase() ? 'Your download will unlock' : 'Your dashboard will open automatically' }} once an admin approves it, and we'll email you.</p>
                </div>
            </div>
            <dl class="mt-6 grid gap-4 text-sm sm:grid-cols-2">
                <div class="rounded-lg bg-[#F2F2F2] p-4"><dt class="font-bold text-[#2E2E2E]/60">Reference</dt><dd class="mt-1 font-mono text-lg font-black tracking-widest text-[#0A2A6B]">{{ $payment->reference }}</dd></div>
                <div class="rounded-lg bg-[#F2F2F2] p-4"><dt class="font-bold text-[#2E2E2E]/60">Amount Paid</dt><dd class="mt-1 text-lg font-black text-[#0A2A6B]">&#8358;{{ number_format($payment->amount_paid) }}</dd></div>
                <div class="rounded-lg bg-[#F2F2F2] p-4"><dt class="font-bold text-[#2E2E2E]/60">Paid Into</dt><dd class="mt-1 font-black text-[#0A2A6B]">{{ $payment->bank_snapshot['bank_name'] ?? '-' }}</dd></div>
                <div class="rounded-lg bg-[#F2F2F2] p-4"><dt class="font-bold text-[#2E2E2E]/60">Submitted</dt><dd class="mt-1 font-black text-[#0A2A6B]">{{ $payment->submitted_at?->format('M j, Y g:i A') }}</dd></div>
            </dl>
            <a href="{{ route('payments.evidence', $payment) }}" target="_blank" class="mt-5 inline-flex rounded-lg border border-[#0A2A6B]/20 px-4 py-2 text-sm font-black text-[#0A2A6B] transition hover:bg-[#F2F2F2]">View Uploaded Evidence</a>
        </div>
        <div class="rounded-lg bg-white p-6 shadow-sm ring-1 ring-[#0A2A6B]/10 sm:p-8">
            <p class="text-sm font-black uppercase tracking-wide text-[#F5B400]">While you wait</p>
            <ul class="mt-4 space-y-3 text-sm leading-6 text-[#2E2E2E]/80">
                <li>You can still view your <a href="{{ route('payments.index') }}" class="font-black text-[#1FA774] underline">past transactions</a> and download old receipts.</li>
                <li>You can update your <a href="{{ route('profile.edit') }}" class="font-black text-[#1FA774] underline">profile</a>.</li>
                <li>If your payment is rejected, you'll see the reason here and can upload new evidence.</li>
            </ul>
        </div>
    </section>
@else
    @if ($payment->status === 'rejected')
        <div class="mt-6 rounded-lg border border-red-200 bg-red-50 px-5 py-4 text-sm text-red-800">
            <p class="font-black">Your previous submission could not be verified.</p>
            <p class="mt-1">Reason: {{ $payment->rejection_reason }}</p>
            <p class="mt-1">Please check the details and upload the correct evidence below. Your reference stays the same.</p>
        </div>
    @endif

    <section class="mt-6 grid gap-6 lg:grid-cols-[0.9fr_1.1fr]">
        <div class="space-y-6">
            <div class="rounded-lg bg-white p-6 shadow-sm ring-1 ring-[#0A2A6B]/10">
                <p class="text-sm font-black uppercase tracking-wide text-[#F5B400]">Step 1 &middot; Amount</p>
                <table class="mt-4 w-full text-sm">
                    <tbody class="divide-y divide-[#0A2A6B]/10">
                        @foreach ($payment->items ?? [] as $item)
                            <tr>
                                <td class="py-2 font-semibold text-[#2E2E2E]/80">{{ $item['name'] }}@if (! empty($item['semester']) && ! $payment->semester) <span class="text-xs text-[#2E2E2E]/50">({{ $item['semester'] }} Sem.)</span>@endif</td>
                                <td class="py-2 text-right font-black text-[#0A2A6B]">&#8358;{{ number_format($item['amount']) }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                    <tfoot>
                        <tr class="border-t-2 border-[#0A2A6B]/20">
                            <td class="pt-3 font-black text-[#0A2A6B]">Total</td>
                            <td class="pt-3 text-right text-2xl font-black text-[#1FA774]">&#8358;{{ number_format($payment->amount_due) }}</td>
                        </tr>
                    </tfoot>
                </table>

                <div class="mt-5 rounded-lg bg-[#0A2A6B] p-4 text-white">
                    <p class="text-xs font-bold uppercase tracking-wide text-[#F5B400]">Your payment reference</p>
                    <div class="mt-1 flex items-center justify-between gap-3">
                        <p class="font-mono text-2xl font-black tracking-[0.3em]">{{ $payment->reference }}</p>
                        <button type="button" data-copy="{{ $payment->reference }}" class="rounded-lg bg-white/10 px-3 py-2 text-xs font-black transition hover:bg-white/20">Copy</button>
                    </div>
                    <p class="mt-2 text-xs text-white/70">Use this as your transfer narration/description so we can match your payment quickly.</p>
                </div>
            </div>

            <div class="rounded-lg bg-white p-6 shadow-sm ring-1 ring-[#0A2A6B]/10">
                <p class="text-sm font-black uppercase tracking-wide text-[#F5B400]">Step 2 &middot; Transfer to</p>
                <div class="mt-4 space-y-3">
                    @forelse ($bankAccounts as $account)
                        <div class="rounded-lg border border-[#0A2A6B]/10 bg-[#F2F2F2] p-4">
                            <p class="text-xs font-black uppercase tracking-wide text-[#2E2E2E]/60">{{ $account->bank_name }}</p>
                            <div class="mt-1 flex items-center justify-between gap-3">
                                <p class="font-mono text-xl font-black tracking-wider text-[#0A2A6B]">{{ $account->account_number }}</p>
                                <button type="button" data-copy="{{ $account->account_number }}" class="rounded-lg bg-white px-3 py-2 text-xs font-black text-[#0A2A6B] ring-1 ring-[#0A2A6B]/15 transition hover:bg-[#0A2A6B] hover:text-white">Copy</button>
                            </div>
                            <p class="text-sm font-bold text-[#2E2E2E]/80">{{ $account->account_name }}</p>
                            @if ($account->instructions)
                                <p class="mt-2 text-xs leading-5 text-[#2E2E2E]/65">{{ $account->instructions }}</p>
                            @endif
                        </div>
                    @empty
                        <p class="rounded-lg bg-[#F5B400]/15 p-4 text-sm font-bold text-[#0A2A6B]">Bank details have not been published yet. Please check back shortly or contact an executive.</p>
                    @endforelse
                </div>
            </div>
        </div>

        <div class="rounded-lg bg-white p-6 shadow-sm ring-1 ring-[#0A2A6B]/10 sm:p-8">
            <p class="text-sm font-black uppercase tracking-wide text-[#F5B400]">Step 3 &middot; I have paid</p>
            <h2 class="mt-2 text-2xl font-black text-[#0A2A6B]">Upload your evidence of payment</h2>
            <p class="mt-2 text-sm leading-6 text-[#2E2E2E]/70">After transferring, fill this form and upload your receipt or a screenshot of the successful transfer. An admin will verify it within 24 hours.</p>

            @if ($payment->canBeSubmitted() && $bankAccounts->isNotEmpty())
                <form action="{{ route('payments.submit', $payment) }}" method="POST" enctype="multipart/form-data" class="mt-6 grid gap-5 sm:grid-cols-2">
                    @csrf
                    <label class="grid gap-2 text-sm font-bold text-[#0A2A6B] sm:col-span-2">
                        Account you paid into
                        <select name="bank_account_id" required class="{{ $inputClass }}">
                            @foreach ($bankAccounts as $account)
                                <option value="{{ $account->id }}" @selected((string) old('bank_account_id', $payment->bank_account_id) === (string) $account->id)>{{ $account->bank_name }} - {{ $account->account_number }}</option>
                            @endforeach
                        </select>
                        @error('bank_account_id')<span class="text-sm font-bold text-red-600">{{ $message }}</span>@enderror
                    </label>

                    <label class="grid gap-2 text-sm font-bold text-[#0A2A6B]">
                        Amount paid (&#8358;)
                        <input name="amount_paid" type="number" min="1" required value="{{ old('amount_paid', $payment->amount_paid ?? $payment->amount_due) }}" class="{{ $inputClass }}">
                        @error('amount_paid')<span class="text-sm font-bold text-red-600">{{ $message }}</span>@enderror
                    </label>

                    <label class="grid gap-2 text-sm font-bold text-[#0A2A6B]">
                        Date of payment
                        <input name="paid_at" type="date" required max="{{ now()->toDateString() }}" value="{{ old('paid_at', $payment->paid_at?->toDateString() ?? now()->toDateString()) }}" class="{{ $inputClass }}">
                        @error('paid_at')<span class="text-sm font-bold text-red-600">{{ $message }}</span>@enderror
                    </label>

                    <label class="grid gap-2 text-sm font-bold text-[#0A2A6B] sm:col-span-2">
                        Name on the account you paid from
                        <input name="payer_name" required maxlength="150" value="{{ old('payer_name', $payment->payer_name ?? $user->name) }}" class="{{ $inputClass }}">
                        @error('payer_name')<span class="text-sm font-bold text-red-600">{{ $message }}</span>@enderror
                    </label>

                    <label class="grid gap-2 text-sm font-bold text-[#0A2A6B] sm:col-span-2">
                        Receipt / evidence (JPG, PNG, WEBP or PDF, max 5MB)
                        <input name="evidence" type="file" required accept=".jpg,.jpeg,.png,.webp,.pdf" class="rounded-lg border border-dashed border-[#0A2A6B]/30 bg-[#F2F2F2] px-4 py-6 text-sm font-normal file:mr-4 file:rounded-lg file:border-0 file:bg-[#0A2A6B] file:px-4 file:py-2 file:text-sm file:font-black file:text-white">
                        @error('evidence')<span class="text-sm font-bold text-red-600">{{ $message }}</span>@enderror
                    </label>

                    <div class="sm:col-span-2">
                        <button type="submit" class="w-full rounded-lg bg-[#1FA774] px-6 py-4 text-base font-black text-white transition hover:bg-[#198b61]">I Have Paid &middot; Submit for Verification</button>
                        <p class="mt-3 text-center text-xs text-[#2E2E2E]/60">Please wait up to 24 hours for verification after submitting.</p>
                    </div>
                </form>
            @endif
        </div>
    </section>
@endif

<script>
    document.querySelectorAll('[data-copy]').forEach((button) => {
        button.addEventListener('click', async () => {
            try {
                await navigator.clipboard.writeText(button.dataset.copy);
                const label = button.textContent;
                button.textContent = 'Copied!';
                setTimeout(() => (button.textContent = label), 1500);
            } catch (error) {
                // Clipboard may be unavailable; the value is still visible to copy manually.
            }
        });
    });
</script>
