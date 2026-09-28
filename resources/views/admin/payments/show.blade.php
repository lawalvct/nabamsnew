@extends('layouts.dashboard', ['pageTitle' => 'Payment '.$payment->reference])

@php
    $member = $payment->user;
    $inputClass = 'rounded-lg border border-[#0A2A6B]/15 bg-white px-4 py-3 font-normal text-[#2E2E2E] outline-none transition focus:border-[#F5B400] focus:ring-4 focus:ring-[#F5B400]/20';
    $canApprove = in_array($payment->status, ['pending', 'rejected'], true) && $payment->evidence_path;
    $canReject = $payment->status === 'pending';
@endphp

@section('content')
    <a href="{{ route('admin.payments.index') }}" class="text-sm font-black text-[#0A2A6B] hover:text-[#1FA774]">&larr; Back to transactions</a>

    <section class="mt-4 rounded-lg bg-[#0A2A6B] p-6 text-white shadow-xl sm:p-8">
        <div class="flex flex-col gap-4 sm:flex-row sm:items-end sm:justify-between">
            <div>
                <p class="text-sm font-black uppercase tracking-wide text-[#F5B400]">Payment Reference</p>
                <h1 class="mt-2 font-mono text-4xl font-black tracking-[0.25em]">{{ $payment->reference }}</h1>
                <p class="mt-2 text-sm text-[#F2F2F2]/80">{{ $payment->periodLabel() }}</p>
            </div>
            <span class="self-start rounded-full bg-white px-4 py-2 text-sm font-black sm:self-auto {{ $payment->statusClasses() }}">{{ $payment->statusLabel() }}</span>
        </div>
    </section>

    @foreach (['success' => 'border-[#1FA774]/20 bg-[#1FA774]/10', 'error' => 'border-red-200 bg-red-50'] as $key => $tone)
        @if (session($key))
            <div class="mt-6 rounded-lg border {{ $tone }} px-5 py-4 text-sm font-bold text-[#0A2A6B]">{{ session($key) }}</div>
        @endif
    @endforeach

    <section class="mt-6 grid gap-6 xl:grid-cols-[1.1fr_0.9fr]">
        <div class="rounded-lg bg-white p-6 shadow-sm ring-1 ring-[#0A2A6B]/10">
            <div class="flex items-center justify-between gap-3">
                <h2 class="text-xl font-black text-[#0A2A6B]">Evidence</h2>
                @if ($payment->evidence_path)
                    <a href="{{ route('payments.evidence', $payment) }}" target="_blank" class="rounded-lg border border-[#0A2A6B]/20 px-3 py-2 text-xs font-black text-[#0A2A6B] transition hover:bg-[#0A2A6B] hover:text-white">Open in new tab</a>
                @endif
            </div>
            <div class="mt-4 overflow-hidden rounded-lg bg-[#F2F2F2]">
                @if (! $payment->evidence_path)
                    <p class="p-10 text-center text-sm font-semibold text-[#2E2E2E]/60">The member has not uploaded evidence yet.</p>
                @elseif ($payment->evidenceIsImage())
                    <a href="{{ route('payments.evidence', $payment) }}" target="_blank">
                        <img src="{{ route('payments.evidence', $payment) }}" alt="Payment evidence" class="mx-auto max-h-[640px] w-full object-contain">
                    </a>
                @else
                    <iframe src="{{ route('payments.evidence', $payment) }}" title="Payment evidence" class="h-[640px] w-full"></iframe>
                @endif
            </div>
        </div>

        <div class="space-y-6">
            <div class="rounded-lg bg-white p-6 shadow-sm ring-1 ring-[#0A2A6B]/10">
                <h2 class="text-xl font-black text-[#0A2A6B]">Member</h2>
                <dl class="mt-4 grid gap-3 text-sm sm:grid-cols-2">
                    <div><dt class="font-bold text-[#2E2E2E]/60">Name</dt><dd class="font-black text-[#0A2A6B]">{{ $member?->name }}</dd></div>
                    <div><dt class="font-bold text-[#2E2E2E]/60">Matric No</dt><dd class="font-black text-[#0A2A6B]">{{ $member?->matno ?: '-' }}</dd></div>
                    <div><dt class="font-bold text-[#2E2E2E]/60">Level</dt><dd class="font-black text-[#0A2A6B]">{{ $payment->level_name }} &middot; {{ $member?->member_type }}</dd></div>
                    <div><dt class="font-bold text-[#2E2E2E]/60">Phone</dt><dd class="font-black text-[#0A2A6B]">{{ $member?->phone }}</dd></div>
                </dl>
                @if ($member)
                    <a href="{{ route('admin.members.show', $member) }}" class="mt-4 inline-flex text-xs font-black text-[#1FA774] underline">View member profile</a>
                @endif
            </div>

            <div class="rounded-lg bg-white p-6 shadow-sm ring-1 ring-[#0A2A6B]/10">
                <h2 class="text-xl font-black text-[#0A2A6B]">Payment</h2>
                <table class="mt-4 w-full text-sm">
                    <tbody class="divide-y divide-[#0A2A6B]/10">
                        @foreach ($payment->items ?? [] as $item)
                            <tr><td class="py-2 text-[#2E2E2E]/75">{{ $item['name'] }}@if (! empty($item['semester']) && ! $payment->semester) ({{ $item['semester'] }})@endif</td><td class="py-2 text-right font-semibold">&#8358;{{ number_format($item['amount']) }}</td></tr>
                        @endforeach
                        <tr><td class="py-2 font-black text-[#0A2A6B]">Amount due</td><td class="py-2 text-right font-black text-[#0A2A6B]">&#8358;{{ number_format($payment->amount_due) }}</td></tr>
                        <tr>
                            <td class="py-2 font-black text-[#0A2A6B]">Amount declared by member</td>
                            <td class="py-2 text-right font-black {{ ($payment->amount_paid ?? 0) < $payment->amount_due ? 'text-red-600' : 'text-[#1FA774]' }}">{{ $payment->amount_paid !== null ? '₦'.number_format($payment->amount_paid) : '-' }}</td>
                        </tr>
                    </tbody>
                </table>
                <dl class="mt-4 grid gap-3 text-sm sm:grid-cols-2">
                    <div><dt class="font-bold text-[#2E2E2E]/60">Method / Paid into</dt><dd class="font-black text-[#0A2A6B]">{{ $payment->methodLabel() }}@if ($payment->bank_snapshot)<br>{{ $payment->bank_snapshot['bank_name'] }} <span class="font-mono">{{ $payment->bank_snapshot['account_number'] }}</span>@endif</dd></div>
                    <div><dt class="font-bold text-[#2E2E2E]/60">Payer name</dt><dd class="font-black text-[#0A2A6B]">{{ $payment->payer_name ?: '-' }}</dd></div>
                    <div><dt class="font-bold text-[#2E2E2E]/60">Date of payment</dt><dd class="font-black text-[#0A2A6B]">{{ $payment->paid_at?->format('M j, Y') ?? '-' }}</dd></div>
                    <div><dt class="font-bold text-[#2E2E2E]/60">Submitted</dt><dd class="font-black text-[#0A2A6B]">{{ $payment->submitted_at?->format('M j, Y g:i A') ?? '-' }}</dd></div>
                </dl>

                @if ($payment->admin_note)
                    <p class="mt-4 rounded-lg bg-[#0A2A6B]/5 px-4 py-3 text-sm"><strong class="text-[#0A2A6B]">Admin note:</strong> {{ $payment->admin_note }}</p>
                @endif

                @if ($payment->reviewer_name)
                    <p class="mt-4 rounded-lg bg-[#F2F2F2] px-4 py-3 text-sm">
                        {{ $payment->isApproved() ? 'Approved' : 'Reviewed' }} by <strong class="text-[#0A2A6B]">{{ $payment->reviewer_name }}</strong> on {{ $payment->reviewed_at?->format('M j, Y g:i A') }}
                        @if ($payment->rejection_reason)
                            <span class="mt-1 block text-red-700">Reason: {{ $payment->rejection_reason }}</span>
                        @endif
                    </p>
                @endif

                @if ($payment->isApproved())
                    <a href="{{ route('payments.receipt', $payment) }}" class="mt-4 inline-flex rounded-lg bg-[#1FA774] px-4 py-2 text-sm font-black text-white transition hover:bg-[#198b61]">Download Receipt</a>
                @endif
            </div>

            @if ($canApprove || $canReject)
                <div class="rounded-lg bg-white p-6 shadow-sm ring-1 ring-[#0A2A6B]/10">
                    <h2 class="text-xl font-black text-[#0A2A6B]">Decision</h2>

                    @if ($canApprove)
                        <form action="{{ route('admin.payments.approve', $payment) }}" method="POST" class="mt-4 grid gap-3" onsubmit="return confirm('Approve this payment and unlock the member\'s dashboard?');">
                            @csrf
                            @method('PATCH')
                            <label class="grid gap-2 text-sm font-bold text-[#0A2A6B]">
                                Amount confirmed in bank (&#8358;)
                                <input name="amount_paid" type="number" min="0" required value="{{ old('amount_paid', $payment->amount_paid ?? $payment->amount_due) }}" class="{{ $inputClass }}">
                                <span class="text-xs font-normal text-[#2E2E2E]/60">This amount is printed on the member's receipt.</span>
                                @error('amount_paid')<span class="text-sm font-bold text-red-600">{{ $message }}</span>@enderror
                            </label>
                            <button type="submit" class="rounded-lg bg-[#1FA774] px-5 py-3 text-sm font-black text-white transition hover:bg-[#198b61]">Approve Payment</button>
                        </form>
                    @endif

                    @if ($canReject)
                        <form action="{{ route('admin.payments.reject', $payment) }}" method="POST" class="mt-6 grid gap-3 border-t border-[#0A2A6B]/10 pt-6">
                            @csrf
                            @method('PATCH')
                            <label class="grid gap-2 text-sm font-bold text-[#0A2A6B]">
                                Reason for rejection (shown to the member)
                                <input name="rejection_reason" required maxlength="255" value="{{ old('rejection_reason') }}" placeholder="e.g. Transfer not found in account statement" class="{{ $inputClass }}">
                                @error('rejection_reason')<span class="text-sm font-bold text-red-600">{{ $message }}</span>@enderror
                            </label>
                            <button type="submit" class="rounded-lg border border-red-300 px-5 py-3 text-sm font-black text-red-700 transition hover:bg-red-50">Reject Payment</button>
                        </form>
                    @endif
                </div>
            @endif

            @if ($history->isNotEmpty())
                <div class="rounded-lg bg-white p-6 shadow-sm ring-1 ring-[#0A2A6B]/10">
                    <h2 class="text-xl font-black text-[#0A2A6B]">Member's other payments</h2>
                    <ul class="mt-4 divide-y divide-[#0A2A6B]/10 text-sm">
                        @foreach ($history as $other)
                            <li class="flex items-center justify-between gap-3 py-2">
                                <a href="{{ route('admin.payments.show', $other) }}" class="font-mono font-black text-[#0A2A6B] hover:text-[#1FA774]">{{ $other->reference }}</a>
                                <span class="text-[#2E2E2E]/70">{{ $other->periodLabel() }}</span>
                                <span class="rounded-full px-2 py-0.5 text-xs font-black {{ $other->statusClasses() }}">{{ $other->statusLabel() }}</span>
                            </li>
                        @endforeach
                    </ul>
                </div>
            @endif
        </div>
    </section>
@endsection
