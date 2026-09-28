@extends('layouts.dashboard', ['pageTitle' => $resource->title])

@section('content')
    <a href="{{ route('resources.index') }}" class="text-sm font-black text-[#0A2A6B] hover:text-[#1FA774]">&larr; All resources</a>

    @foreach (['success' => 'border-[#1FA774]/20 bg-[#1FA774]/10', 'error' => 'border-red-200 bg-red-50'] as $key => $tone)
        @if (session($key))
            <div class="mt-4 rounded-lg border {{ $tone }} px-5 py-4 text-sm font-bold text-[#0A2A6B]">{{ session($key) }}</div>
        @endif
    @endforeach

    <section class="mt-4 grid gap-6 lg:grid-cols-[1.3fr_0.7fr]">
        <article class="rounded-lg bg-white p-6 shadow-sm ring-1 ring-[#0A2A6B]/10 sm:p-8">
            <div class="flex items-start gap-4">
                @include('resources._file-badge', ['extension' => $resource->file_extension, 'size' => 'h-16 w-16'])
                <div>
                    <p class="text-xs font-black uppercase tracking-wide text-[#F5B400]">{{ $resource->category ?? 'General' }}</p>
                    <h1 class="mt-1 text-2xl font-black text-[#0A2A6B] sm:text-3xl">{{ $resource->title }}</h1>
                    <p class="mt-2 text-sm font-semibold text-[#2E2E2E]/60">{{ $resource->level?->name ?? 'All levels' }} &middot; {{ strtoupper($resource->file_extension) }} &middot; {{ $resource->humanFileSize() }}</p>
                </div>
            </div>

            @if ($resource->description)
                <div class="mt-6 whitespace-pre-line text-sm leading-7 text-[#2E2E2E]/80">{{ $resource->description }}</div>
            @endif

            <div class="mt-6 flex flex-wrap gap-6 border-t border-[#0A2A6B]/10 pt-5 text-sm">
                <p><span class="text-2xl font-black text-[#0A2A6B]">{{ number_format($resource->view_count) }}</span> <span class="text-[#2E2E2E]/60">views</span></p>
                <p><span class="text-2xl font-black text-[#0A2A6B]">{{ number_format($resource->download_count) }}</span> <span class="text-[#2E2E2E]/60">downloads</span></p>
            </div>
        </article>

        <aside class="h-fit rounded-lg bg-white p-6 shadow-sm ring-1 ring-[#0A2A6B]/10">
            @if ($canDownload)
                <p class="text-sm font-black uppercase tracking-wide text-[#1FA774]">{{ $resource->isFree() ? 'Free resource' : 'You own this' }}</p>
                <a href="{{ route('resources.download', $resource) }}" class="mt-4 flex w-full justify-center rounded-lg bg-[#1FA774] px-5 py-4 text-base font-black text-white transition hover:bg-[#198b61]">Download</a>
                @if ($payment?->isApproved())
                    <a href="{{ route('payments.receipt', $payment) }}" class="mt-3 flex w-full justify-center rounded-lg border border-[#0A2A6B]/20 px-5 py-3 text-sm font-black text-[#0A2A6B] transition hover:bg-[#F2F2F2]">Receipt ({{ $payment->reference }})</a>
                @endif
            @elseif ($payment?->status === 'pending')
                <p class="text-sm font-black uppercase tracking-wide text-[#F5B400]">Awaiting verification</p>
                <p class="mt-2 text-sm leading-6 text-[#2E2E2E]/70">We're verifying your payment ({{ $payment->reference }}). Your download unlocks within 24 hours of approval.</p>
                <a href="{{ route('payments.show', $payment) }}" class="mt-4 flex w-full justify-center rounded-lg border border-[#0A2A6B]/20 px-5 py-3 text-sm font-black text-[#0A2A6B] transition hover:bg-[#F2F2F2]">View Payment</a>
            @else
                <p class="text-sm font-black uppercase tracking-wide text-[#F5B400]">Paid resource</p>
                <p class="mt-2 text-4xl font-black text-[#0A2A6B]">&#8358;{{ number_format($resource->price) }}</p>
                <p class="mt-2 text-sm leading-6 text-[#2E2E2E]/70">Pay by bank transfer, upload your evidence, and download once an admin verifies it (within 24 hours). Buy once, download any time.</p>
                @if ($payment?->status === 'rejected')
                    <p class="mt-3 rounded-lg bg-red-50 px-3 py-2 text-xs text-red-700">Previous payment rejected: {{ $payment->rejection_reason }}</p>
                @endif
                <form action="{{ route('resources.purchase', $resource) }}" method="POST" class="mt-4">
                    @csrf
                    <button type="submit" class="w-full rounded-lg bg-[#F5B400] px-5 py-4 text-base font-black text-[#0A2A6B] transition hover:bg-[#ffd15c]">
                        {{ $payment && in_array($payment->status, ['awaiting_payment', 'rejected'], true) ? 'Continue Payment' : 'Buy Resource' }}
                    </button>
                </form>
            @endif
        </aside>
    </section>
@endsection
