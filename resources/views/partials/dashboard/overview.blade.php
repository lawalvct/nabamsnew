<section id="dashboard" class="grid gap-6 xl:grid-cols-[1.3fr_0.7fr]">
    <div class="overflow-hidden rounded-lg bg-[#0A2A6B] text-white shadow-xl">
        <div class="grid gap-8 p-6 sm:p-8 lg:grid-cols-[1fr_0.8fr] lg:items-end">
            <div>
                <p class="text-sm font-black uppercase tracking-wide text-[#F5B400]">{{ $isAdmin ? 'Control Center' : 'Account Overview' }}</p>
                <h2 class="mt-4 text-3xl font-black leading-tight text-white sm:text-4xl">
                    {{ $isAdmin ? 'Manage NABAMS operations from one workspace.' : 'Your NABAMS member dashboard is ready.' }}
                </h2>
                <p class="mt-4 max-w-2xl text-sm leading-7 text-[#F2F2F2]/80">
                    {{ $isAdmin ? 'Track sessions, members, resources, elections, contests, payments, and content from a focused administrative layout.' : 'Access your transactions, resources, elections, contests, fees, project records, and profile information from a mobile-friendly workspace.' }}
                </p>
            </div>
            <div class="rounded-lg border border-white/10 bg-white/10 p-5">
                <p class="text-sm font-black uppercase tracking-wide text-[#F5B400]">Current Session</p>
                <p class="mt-3 text-3xl font-black">2025-2026</p>
                <p class="mt-2 text-sm leading-6 text-[#F2F2F2]/75">NABAMS active academic session.</p>
            </div>
        </div>
    </div>

    <div id="profile" class="rounded-lg bg-white p-6 shadow-sm ring-1 ring-[#0A2A6B]/10">
        <div class="flex items-center gap-4">
            <div class="grid h-16 w-16 shrink-0 place-items-center overflow-hidden rounded-lg bg-[#0A2A6B] text-lg font-black text-white">
                @if ($user->image_url)
                    <img src="{{ $user->image_url }}" alt="{{ $displayName }}" class="h-full w-full object-cover">
                @else
                    {{ $initials ?: 'NM' }}
                @endif
            </div>
            <div class="min-w-0">
                <p class="truncate text-lg font-black text-[#0A2A6B]">{{ $displayName }}</p>
                <p class="truncate text-sm font-semibold text-[#2E2E2E]/65">{{ $user->email }}</p>
            </div>
        </div>
        <dl class="mt-6 grid gap-4 text-sm">
            <div class="flex items-center justify-between gap-4 border-b border-[#0A2A6B]/10 pb-3">
                <dt class="font-bold text-[#2E2E2E]/60">Role</dt>
                <dd class="font-black text-[#0A2A6B]">{{ $user->role }}</dd>
            </div>
            <div class="flex items-center justify-between gap-4 border-b border-[#0A2A6B]/10 pb-3">
                <dt class="font-bold text-[#2E2E2E]/60">Matric No.</dt>
                <dd class="font-black text-[#0A2A6B]">{{ $user->matno ?? 'Not set' }}</dd>
            </div>
            <div class="flex items-center justify-between gap-4">
                <dt class="font-bold text-[#2E2E2E]/60">Active</dt>
                <dd class="font-black {{ $user->is_active === 'Yes' ? 'text-[#1FA774]' : 'text-[#0A2A6B]' }}">{{ $user->is_active }}</dd>
            </div>
        </dl>
    </div>
</section>
