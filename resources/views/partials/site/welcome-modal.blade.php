<div id="welcome-modal" class="fixed inset-0 z-[100] hidden items-center justify-center bg-[#0A2A6B]/80 px-4 py-6 backdrop-blur-sm" role="dialog" aria-modal="true" aria-labelledby="welcome-modal-title">
    <div class="w-full max-w-lg overflow-hidden rounded-lg bg-white shadow-2xl ring-1 ring-white/20">
        <div class="bg-[#0A2A6B] px-6 py-5 text-white">
            <div class="flex items-start justify-between gap-4">
                <div>
                    <p class="text-sm font-black uppercase tracking-wide text-[#F5B400]">Welcome to NABAMS</p>
                    <h2 id="welcome-modal-title" class="mt-2 text-2xl font-black">Hello, visitor</h2>
                </div>
                <button type="button" data-close-welcome-modal class="inline-flex h-10 w-10 shrink-0 items-center justify-center rounded-lg border border-white/20 text-white transition hover:bg-white/10" aria-label="Close welcome message">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M6 18 18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>
        </div>

        <div class="p-6 sm:p-7">
            <p class="text-base leading-8 text-[#2E2E2E]/75">
                Glad to have you here. Register as a NABAMS member to access your dashboard, membership profile, resources, and association updates.
            </p>

            <div class="mt-6 grid gap-3 sm:grid-cols-2">
                <a href="{{ route('register') }}" class="inline-flex items-center justify-center rounded-lg bg-[#1FA774] px-5 py-3 text-sm font-black text-white transition hover:bg-[#198b61]">Register Now</a>
                <button type="button" data-close-welcome-modal class="inline-flex items-center justify-center rounded-lg border border-[#0A2A6B]/20 px-5 py-3 text-sm font-black text-[#0A2A6B] transition hover:border-[#1FA774] hover:text-[#1FA774]">Maybe Later</button>
            </div>
        </div>
    </div>
</div>
