@extends('layouts.dashboard', ['pageTitle' => 'Pay Dues'])

@php
    $status = $payment?->status;
@endphp

@section('content')
    <section class="rounded-lg bg-[#0A2A6B] p-6 text-white shadow-xl sm:p-8">
        <div class="flex flex-col gap-5 lg:flex-row lg:items-end lg:justify-between">
            <div>
                <p class="text-sm font-black uppercase tracking-wide text-[#F5B400]">Dues Payment</p>
                <h1 class="mt-3 text-3xl font-black leading-tight sm:text-4xl">
                    @if ($requirement->satisfied)
                        You're all set
                    @elseif ($status === 'pending')
                        Payment awaiting verification
                    @else
                        Pay your NABAMS dues
                    @endif
                </h1>
                <p class="mt-4 max-w-3xl text-sm leading-7 text-[#F2F2F2]/80">
                    @if ($requirement->periodLabel())
                        {{ $requirement->periodLabel() }}
                    @else
                        No active academic session has been set yet.
                    @endif
                </p>
            </div>
            <a href="{{ route('payments.index') }}" class="inline-flex justify-center rounded-lg border border-white/20 px-5 py-3 text-sm font-black text-white transition hover:bg-white/10">My Transactions</a>
        </div>
    </section>

    @foreach (['payment_notice' => 'border-[#F5B400]/40 bg-[#F5B400]/15', 'success' => 'border-[#1FA774]/20 bg-[#1FA774]/10', 'error' => 'border-red-200 bg-red-50'] as $key => $tone)
        @if (session($key))
            <div class="mt-6 rounded-lg border {{ $tone }} px-5 py-4 text-sm font-bold text-[#0A2A6B]">{{ session($key) }}</div>
        @endif
    @endforeach

    @if ($requirement->satisfied)
        <section class="mt-6 rounded-lg bg-white p-8 text-center shadow-sm ring-1 ring-[#0A2A6B]/10">
            <div class="mx-auto grid h-14 w-14 place-items-center rounded-full bg-[#1FA774]/10 text-[#1FA774]">
                <svg class="h-7 w-7" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="m5 13 4 4L19 7" /></svg>
            </div>
            @if ($requirement->items->isNotEmpty())
                <h2 class="mt-4 text-2xl font-black text-[#0A2A6B]">Your dues for this period are paid.</h2>
                <p class="mt-2 text-sm text-[#2E2E2E]/70">Your dashboard is fully open. You can download receipts from your transactions.</p>
                @if ($payment?->isApproved())
                    <a href="{{ route('payments.receipt', $payment) }}" class="mt-5 inline-flex rounded-lg bg-[#1FA774] px-5 py-3 text-sm font-black text-white transition hover:bg-[#198b61]">Download Receipt ({{ $payment->reference }})</a>
                @endif
            @else
                <h2 class="mt-4 text-2xl font-black text-[#0A2A6B]">No payment is required right now.</h2>
                <p class="mt-2 text-sm text-[#2E2E2E]/70">There are no dues set for your level in the current period.</p>
            @endif
            <a href="{{ route('dashboard') }}" class="mt-5 ml-2 inline-flex rounded-lg border border-[#0A2A6B]/20 px-5 py-3 text-sm font-black text-[#0A2A6B] transition hover:bg-[#F2F2F2]">Go to Dashboard</a>
        </section>
    @elseif ($payment)
        @include('payments._checkout')
    @endif
@endsection
