@extends('layouts.dashboard', ['pageTitle' => 'Checkout'])

@section('content')
    <a href="{{ $payment->resource ? route('resources.show', $payment->resource) : route('resources.index') }}" class="text-sm font-black text-[#0A2A6B] hover:text-[#1FA774]">&larr; Back to resource</a>

    <section class="mt-4 rounded-lg bg-[#0A2A6B] p-6 text-white shadow-xl sm:p-8">
        <div class="flex flex-col gap-5 lg:flex-row lg:items-end lg:justify-between">
            <div>
                <p class="text-sm font-black uppercase tracking-wide text-[#F5B400]">Resource Purchase</p>
                <h1 class="mt-3 text-3xl font-black leading-tight sm:text-4xl">{{ $payment->resource?->title ?? $payment->items[0]['name'] ?? 'Resource' }}</h1>
                <p class="mt-4 text-sm text-[#F2F2F2]/80">
                    {{ $payment->isApproved() ? 'Purchase approved.' : ($payment->status === 'pending' ? 'Payment awaiting verification.' : 'Pay once, download any time after approval.') }}
                </p>
            </div>
            <a href="{{ route('payments.index') }}" class="inline-flex justify-center rounded-lg border border-white/20 px-5 py-3 text-sm font-black text-white transition hover:bg-white/10">My Transactions</a>
        </div>
    </section>

    @foreach (['success' => 'border-[#1FA774]/20 bg-[#1FA774]/10', 'error' => 'border-red-200 bg-red-50'] as $key => $tone)
        @if (session($key))
            <div class="mt-6 rounded-lg border {{ $tone }} px-5 py-4 text-sm font-bold text-[#0A2A6B]">{{ session($key) }}</div>
        @endif
    @endforeach

    @if ($payment->isApproved())
        <section class="mt-6 rounded-lg bg-white p-8 text-center shadow-sm ring-1 ring-[#0A2A6B]/10">
            <h2 class="text-2xl font-black text-[#0A2A6B]">You own this resource.</h2>
            <div class="mt-5 flex flex-wrap justify-center gap-3">
                @if ($payment->resource)
                    <a href="{{ route('resources.download', $payment->resource) }}" class="inline-flex rounded-lg bg-[#1FA774] px-5 py-3 text-sm font-black text-white transition hover:bg-[#198b61]">Download</a>
                @endif
                <a href="{{ route('payments.receipt', $payment) }}" class="inline-flex rounded-lg border border-[#0A2A6B]/20 px-5 py-3 text-sm font-black text-[#0A2A6B] transition hover:bg-[#F2F2F2]">Receipt ({{ $payment->reference }})</a>
            </div>
        </section>
    @else
        @include('payments._checkout')
    @endif
@endsection
