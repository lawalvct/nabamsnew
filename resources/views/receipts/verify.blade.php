<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="robots" content="noindex">
        <meta name="description" content="Verify that a NABAMS payment receipt is genuine.">

        <title>Verify Receipt - NABAMS</title>
        <link rel="icon" type="image/png" href="{{ asset('favicon.png') }}">

        @fonts

        @if (file_exists(public_path('build/manifest.json')) || file_exists(public_path('hot')))
            @vite(['resources/css/app.css', 'resources/js/app.js'])
        @else
            <script src="https://cdn.jsdelivr.net/npm/@tailwindcss/browser@4"></script>
        @endif
    </head>
    <body class="min-h-screen bg-[#F2F2F2] font-sans text-[#2E2E2E] antialiased">
        @include('partials.site.topbar')
        @include('partials.site.navbar')

        <main class="mx-auto max-w-2xl px-4 py-12 sm:py-16">
            <div class="text-center">
                <p class="text-sm font-black uppercase tracking-wide text-[#F5B400]">Receipt Verification</p>
                <h1 class="mt-3 text-3xl font-black text-[#0A2A6B] sm:text-4xl">Is this NABAMS receipt genuine?</h1>
                <p class="mt-3 text-sm leading-7 text-[#2E2E2E]/70">Enter the 8-character reference printed on the receipt.</p>
            </div>

            <form method="GET" action="{{ route('receipts.verify') }}" class="mt-8 flex flex-col gap-3 sm:flex-row">
                <input name="reference" value="{{ $reference }}" maxlength="12" required autocomplete="off" placeholder="e.g. K7QM4XR2" class="flex-1 rounded-lg border border-[#0A2A6B]/15 bg-white px-4 py-3 text-center font-mono text-xl font-black uppercase tracking-[0.3em] text-[#0A2A6B] outline-none transition focus:border-[#F5B400] focus:ring-4 focus:ring-[#F5B400]/20 sm:text-left">
                <button type="submit" class="rounded-lg bg-[#0A2A6B] px-6 py-3 text-sm font-black text-white transition hover:bg-[#123982]">Verify</button>
            </form>

            @if ($payment)
                <section class="mt-8 overflow-hidden rounded-lg bg-white shadow-sm ring-2 ring-[#1FA774]">
                    <div class="flex items-center gap-3 bg-[#1FA774] px-6 py-4 text-white">
                        <svg class="h-7 w-7 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="m5 13 4 4L19 7" /></svg>
                        <p class="text-lg font-black">Valid receipt &middot; {{ $payment->reference }}</p>
                    </div>
                    <dl class="grid gap-4 p-6 text-sm sm:grid-cols-2">
                        <div><dt class="font-bold text-[#2E2E2E]/60">Member</dt><dd class="mt-1 font-black text-[#0A2A6B]">{{ $payment->user?->name }}</dd></div>
                        <div><dt class="font-bold text-[#2E2E2E]/60">Matric Number</dt><dd class="mt-1 font-mono font-black text-[#0A2A6B]">{{ \App\Http\Controllers\ReceiptVerificationController::maskMatno($payment->user?->matno) }}</dd></div>
                        <div><dt class="font-bold text-[#2E2E2E]/60">Level</dt><dd class="mt-1 font-black text-[#0A2A6B]">{{ $payment->level_name }}</dd></div>
                        <div><dt class="font-bold text-[#2E2E2E]/60">Period</dt><dd class="mt-1 font-black text-[#0A2A6B]">{{ $payment->periodLabel() }}</dd></div>
                        <div><dt class="font-bold text-[#2E2E2E]/60">Amount Paid</dt><dd class="mt-1 text-lg font-black text-[#1FA774]">&#8358;{{ number_format($payment->amount_paid) }}</dd></div>
                        <div><dt class="font-bold text-[#2E2E2E]/60">Approved</dt><dd class="mt-1 font-black text-[#0A2A6B]">{{ $payment->reviewed_at?->format('F j, Y') }} by {{ $payment->reviewer_name }}</dd></div>
                    </dl>
                    <p class="border-t border-[#0A2A6B]/10 px-6 py-3 text-xs text-[#2E2E2E]/60">Check that these details match the printed receipt. If anything differs, the receipt may have been altered.</p>
                </section>
            @elseif ($searched)
                <section class="mt-8 rounded-lg bg-white p-6 shadow-sm ring-2 ring-red-300">
                    <p class="text-lg font-black text-red-700">No approved payment found for "{{ $reference }}".</p>
                    <p class="mt-2 text-sm leading-6 text-[#2E2E2E]/70">Check the reference for typos. References use capital letters and numbers only (no O, 0, I or 1). If it still fails, the receipt is not valid or the payment has not been approved yet.</p>
                </section>
            @endif
        </main>

        @include('partials.site.footer')
    </body>
</html>
