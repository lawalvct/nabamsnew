<section class="mt-6 grid gap-6 xl:grid-cols-[0.85fr_1.15fr]">
    <div class="rounded-lg bg-white p-6 shadow-sm ring-1 ring-[#0A2A6B]/10">
        <div class="flex items-center justify-between gap-4">
            <div>
                <p class="text-sm font-black uppercase tracking-wide text-[#F5B400]">Quick Actions</p>
                <h2 class="mt-2 text-2xl font-black text-[#0A2A6B]">{{ $isAdmin ? 'Admin shortcuts' : 'Member shortcuts' }}</h2>
            </div>
            <span class="rounded-lg bg-[#1FA774]/10 px-3 py-2 text-xs font-black uppercase tracking-wide text-[#1FA774]">{{ count($menus) }} Menus</span>
        </div>

        <div class="mt-6 grid gap-3 sm:grid-cols-2">
            @foreach (collect($menus)->take(6) as $menu)
                <a href="{{ $menu['href'] }}" class="flex items-center gap-3 rounded-lg border border-[#0A2A6B]/10 bg-[#F2F2F2] p-4 transition hover:border-[#1FA774] hover:bg-white">
                    <span class="grid h-10 w-10 shrink-0 place-items-center rounded-lg bg-[#0A2A6B] text-[#F5B400]">
                        @include('partials.dashboard.menu-icon', ['name' => $menu['icon'], 'class' => 'h-5 w-5'])
                    </span>
                    <span class="font-black text-[#0A2A6B]">{{ $menu['label'] }}</span>
                </a>
            @endforeach
        </div>
    </div>

    <div class="rounded-lg bg-white p-6 shadow-sm ring-1 ring-[#0A2A6B]/10">
        <p class="text-sm font-black uppercase tracking-wide text-[#F5B400]">Details</p>
        <h2 class="mt-2 text-2xl font-black text-[#0A2A6B]">{{ $isAdmin ? 'Operational snapshot' : 'Profile information' }}</h2>

        <dl class="mt-6 grid gap-5 sm:grid-cols-2">
            <div class="rounded-lg bg-[#F2F2F2] p-4">
                <dt class="text-sm font-bold text-[#2E2E2E]/60">Phone</dt>
                <dd class="mt-1 break-words font-black text-[#0A2A6B]">{{ $user->phone ?? 'Not provided' }}</dd>
            </div>
            <div class="rounded-lg bg-[#F2F2F2] p-4">
                <dt class="text-sm font-bold text-[#2E2E2E]/60">Department</dt>
                <dd class="mt-1 break-words font-black text-[#0A2A6B]">{{ $user->department ?? 'Business Administration & Management' }}</dd>
            </div>
            <div class="rounded-lg bg-[#F2F2F2] p-4">
                <dt class="text-sm font-bold text-[#2E2E2E]/60">Level</dt>
                <dd class="mt-1 font-black text-[#0A2A6B]">{{ $user->academic_level ?? $user->level_id ?? 'Not set' }}</dd>
            </div>
            <div class="rounded-lg bg-[#F2F2F2] p-4">
                <dt class="text-sm font-bold text-[#2E2E2E]/60">Session</dt>
                <dd class="mt-1 font-black text-[#0A2A6B]">{{ $user->session_start ?? '----' }} - {{ $user->session_end ?? '----' }}</dd>
            </div>
        </dl>
    </div>
</section>
