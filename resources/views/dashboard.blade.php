<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">

        <title>Dashboard - NABAMS</title>

        @fonts

        @if (file_exists(public_path('build/manifest.json')) || file_exists(public_path('hot')))
            @vite(['resources/css/app.css', 'resources/js/app.js'])
        @else
            <script src="https://cdn.jsdelivr.net/npm/@tailwindcss/browser@4"></script>
        @endif
    </head>
    <body class="min-h-screen bg-[#F2F2F2] font-sans text-[#2E2E2E] antialiased">
        <header class="bg-[#0A2A6B] text-white">
            <div class="mx-auto flex max-w-7xl flex-col gap-4 px-4 py-5 sm:px-6 md:flex-row md:items-center md:justify-between lg:px-8">
                <a href="{{ url('/') }}" class="flex items-center gap-3">
                    <span class="grid h-11 w-11 place-items-center rounded-lg bg-white text-base font-black text-[#0A2A6B]">N</span>
                    <span>
                        <span class="block text-lg font-black leading-none">NABAMS</span>
                        <span class="block text-xs font-semibold uppercase tracking-wide text-[#F5B400]">Dashboard</span>
                    </span>
                </a>

                <form action="{{ route('logout') }}" method="POST">
                    @csrf
                    <button type="submit" class="rounded-lg bg-[#F5B400] px-4 py-2.5 text-sm font-black text-[#0A2A6B] transition hover:bg-[#ffd15c]">Logout</button>
                </form>
            </div>
        </header>

        <main class="mx-auto max-w-7xl px-4 py-10 sm:px-6 lg:px-8">
            <section class="rounded-lg bg-white p-6 shadow-xl ring-1 ring-[#0A2A6B]/10 sm:p-8">
                <p class="text-sm font-black uppercase tracking-wide text-[#F5B400]">Welcome</p>
                <h1 class="mt-3 text-3xl font-black text-[#0A2A6B] sm:text-4xl">{{ $user->name ?: $user->firstname }}</h1>
                <p class="mt-4 max-w-3xl text-base leading-8 text-[#2E2E2E]/75">Your NABAMS dashboard is ready. This area can later hold profile editing, payment status, resources, contests, and member updates.</p>
            </section>

            <section class="mt-8 grid gap-5 md:grid-cols-3">
                <div class="rounded-lg bg-white p-6 shadow-sm ring-1 ring-[#0A2A6B]/10">
                    <p class="text-sm font-black uppercase tracking-wide text-[#F5B400]">Role</p>
                    <p class="mt-3 text-2xl font-black text-[#0A2A6B]">{{ $user->role }}</p>
                </div>

                <div class="rounded-lg bg-white p-6 shadow-sm ring-1 ring-[#0A2A6B]/10">
                    <p class="text-sm font-black uppercase tracking-wide text-[#F5B400]">Membership</p>
                    <p class="mt-3 text-2xl font-black text-[#0A2A6B]">{{ $user->member_type }}</p>
                </div>

                <div class="rounded-lg bg-white p-6 shadow-sm ring-1 ring-[#0A2A6B]/10">
                    <p class="text-sm font-black uppercase tracking-wide text-[#F5B400]">Fee Paid</p>
                    <p class="mt-3 text-2xl font-black {{ $user->fee_paid === 'Yes' ? 'text-[#1FA774]' : 'text-[#0A2A6B]' }}">{{ $user->fee_paid }}</p>
                </div>
            </section>

            <section class="mt-8 grid gap-6 lg:grid-cols-[0.8fr_1.2fr]">
                <div class="rounded-lg bg-[#0A2A6B] p-6 text-white shadow-xl">
                    <h2 class="text-xl font-black">Account Status</h2>
                    <div class="mt-5 grid gap-3 text-sm">
                        <div class="flex items-center justify-between gap-4 border-b border-white/10 pb-3">
                            <span class="text-[#F2F2F2]/75">Active</span>
                            <span class="font-black text-[#F5B400]">{{ $user->is_active }}</span>
                        </div>
                        <div class="flex items-center justify-between gap-4 border-b border-white/10 pb-3">
                            <span class="text-[#F2F2F2]/75">Banned</span>
                            <span class="font-black text-[#F5B400]">{{ $user->is_ban }}</span>
                        </div>
                        <div class="flex items-center justify-between gap-4">
                            <span class="text-[#F2F2F2]/75">Level ID</span>
                            <span class="font-black text-[#F5B400]">{{ $user->level_id }}</span>
                        </div>
                    </div>
                </div>

                <div class="rounded-lg bg-white p-6 shadow-sm ring-1 ring-[#0A2A6B]/10">
                    <h2 class="text-xl font-black text-[#0A2A6B]">Profile Information</h2>
                    <dl class="mt-5 grid gap-4 sm:grid-cols-2">
                        <div>
                            <dt class="text-sm font-bold text-[#2E2E2E]/60">Email</dt>
                            <dd class="mt-1 break-words font-semibold text-[#2E2E2E]">{{ $user->email }}</dd>
                        </div>
                        <div>
                            <dt class="text-sm font-bold text-[#2E2E2E]/60">Phone</dt>
                            <dd class="mt-1 font-semibold text-[#2E2E2E]">{{ $user->phone }}</dd>
                        </div>
                        <div>
                            <dt class="text-sm font-bold text-[#2E2E2E]/60">Matric Number</dt>
                            <dd class="mt-1 font-semibold text-[#2E2E2E]">{{ $user->matno ?? 'Not provided' }}</dd>
                        </div>
                        <div>
                            <dt class="text-sm font-bold text-[#2E2E2E]/60">Session</dt>
                            <dd class="mt-1 font-semibold text-[#2E2E2E]">{{ $user->session_start ?? '----' }} - {{ $user->session_end ?? '----' }}</dd>
                        </div>
                    </dl>
                </div>
            </section>
        </main>
    </body>
</html>
