@extends('layouts.dashboard', ['pageTitle' => 'My Transactions'])

@section('content')
    <section class="rounded-lg bg-[#0A2A6B] p-6 text-white shadow-xl sm:p-8">
        <div class="flex flex-col gap-5 lg:flex-row lg:items-end lg:justify-between">
            <div>
                <p class="text-sm font-black uppercase tracking-wide text-[#F5B400]">Transactions</p>
                <h1 class="mt-3 text-3xl font-black leading-tight sm:text-4xl">My payments</h1>
                <p class="mt-4 max-w-3xl text-sm leading-7 text-[#F2F2F2]/80">Every dues payment you have submitted, with receipts for approved payments.</p>
            </div>
            @unless ($requirement->satisfied)
                <a href="{{ route('payments.create') }}" class="inline-flex justify-center rounded-lg bg-[#F5B400] px-5 py-3 text-sm font-black text-[#0A2A6B] transition hover:bg-[#ffd15c]">
                    {{ $requirement->isPending() ? 'View Pending Payment' : 'Pay for '.$requirement->periodLabel() }}
                </a>
            @endunless
        </div>
    </section>

    <section class="mt-6 overflow-hidden rounded-lg bg-white shadow-sm ring-1 ring-[#0A2A6B]/10">
        <div class="hidden overflow-x-auto lg:block">
            <table class="min-w-full divide-y divide-[#0A2A6B]/10">
                <thead class="bg-[#F2F2F2]">
                    <tr>
                        @foreach (['Reference', 'Period', 'Amount', 'Submitted', 'Status', ''] as $heading)
                            <th class="px-5 py-3 text-left text-xs font-black uppercase tracking-wide text-[#2E2E2E]/65">{{ $heading }}</th>
                        @endforeach
                    </tr>
                </thead>
                <tbody class="divide-y divide-[#0A2A6B]/10">
                    @forelse ($payments as $payment)
                        <tr>
                            <td class="px-5 py-4 font-mono font-black tracking-wider text-[#0A2A6B]">{{ $payment->reference }}</td>
                            <td class="px-5 py-4 text-sm font-semibold text-[#2E2E2E]/75">{{ $payment->periodLabel() }}</td>
                            <td class="px-5 py-4 font-black text-[#0A2A6B]">&#8358;{{ number_format($payment->amount_paid ?? $payment->amount_due) }}</td>
                            <td class="px-5 py-4 text-sm text-[#2E2E2E]/70">{{ $payment->submitted_at?->format('M j, Y') }}</td>
                            <td class="px-5 py-4">
                                <span class="rounded-full px-3 py-1 text-xs font-black {{ $payment->statusClasses() }}">{{ $payment->statusLabel() }}</span>
                                @if ($payment->status === 'rejected')
                                    <p class="mt-1 max-w-xs text-xs text-red-700">{{ $payment->rejection_reason }}</p>
                                @endif
                            </td>
                            <td class="px-5 py-4 text-right">
                                @if ($payment->isApproved())
                                    <a href="{{ route('payments.receipt', $payment) }}" class="rounded-lg bg-[#1FA774] px-3 py-2 text-xs font-black text-white transition hover:bg-[#198b61]">Download Receipt</a>
                                @elseif ($payment->status === 'rejected')
                                    <a href="{{ route('payments.create') }}" class="rounded-lg border border-[#0A2A6B]/20 px-3 py-2 text-xs font-black text-[#0A2A6B]">Resubmit</a>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="6" class="px-5 py-10 text-center text-sm font-semibold text-[#2E2E2E]/65">You have not made any payment yet.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="grid gap-4 p-4 lg:hidden">
            @forelse ($payments as $payment)
                <article class="rounded-lg border border-[#0A2A6B]/10 bg-[#F2F2F2] p-4">
                    <div class="flex items-start justify-between gap-3">
                        <div>
                            <p class="font-mono text-lg font-black tracking-wider text-[#0A2A6B]">{{ $payment->reference }}</p>
                            <p class="text-sm font-semibold text-[#2E2E2E]/65">{{ $payment->periodLabel() }}</p>
                        </div>
                        <span class="rounded-full px-3 py-1 text-xs font-black {{ $payment->statusClasses() }}">{{ $payment->statusLabel() }}</span>
                    </div>
                    <p class="mt-3 text-xl font-black text-[#0A2A6B]">&#8358;{{ number_format($payment->amount_paid ?? $payment->amount_due) }}</p>
                    @if ($payment->isApproved())
                        <a href="{{ route('payments.receipt', $payment) }}" class="mt-3 inline-flex rounded-lg bg-[#1FA774] px-3 py-2 text-xs font-black text-white">Download Receipt</a>
                    @endif
                </article>
            @empty
                <p class="rounded-lg bg-[#F2F2F2] p-6 text-center text-sm font-semibold text-[#2E2E2E]/65">You have not made any payment yet.</p>
            @endforelse
        </div>

        @if ($payments->hasPages())
            <div class="border-t border-[#0A2A6B]/10 px-5 py-4">{{ $payments->links() }}</div>
        @endif
    </section>
@endsection
