<section id="dashboard" class="grid gap-6 xl:grid-cols-[1.3fr_0.7fr]">
    <div class="overflow-hidden rounded-lg bg-[#0A2A6B] text-white shadow-xl">
        @if ($isAdmin)
            <div class="grid gap-8 p-6 sm:p-8 lg:grid-cols-[1fr_0.8fr] lg:items-end">
                <div>
                    <p class="text-sm font-black uppercase tracking-wide text-[#F5B400]">Control Center</p>
                    <h2 class="mt-4 text-3xl font-black leading-tight text-white sm:text-4xl">
                        Manage NABAMS operations from one workspace.
                    </h2>
                    <p class="mt-4 max-w-2xl text-sm leading-7 text-[#F2F2F2]/80">
                        Track sessions, members, resources, elections, contests, payments, and content from a focused administrative layout.
                    </p>
                </div>
                <div class="rounded-lg border border-white/10 bg-white/10 p-5">
                    <p class="text-sm font-black uppercase tracking-wide text-[#F5B400]">Current Session</p>
                    <p class="mt-3 text-3xl font-black">2025-2026</p>
                    <p class="mt-2 text-sm leading-6 text-[#F2F2F2]/75">NABAMS active academic session.</p>
                </div>
            </div>
        @else
            <div class="grid gap-7 p-6 sm:p-8 lg:grid-cols-[0.78fr_1.22fr] lg:items-center">
                <div class="mx-auto w-full max-w-xs">
                    <div class="rounded-lg border-4 border-[#F5B400] bg-white p-2 shadow-2xl">
                        <img src="{{ asset('uploads/president/nabams_president.png') }}" alt="NABAMS President" class="aspect-[4/5] w-full rounded-md object-cover">
                    </div>
                    <div class="mt-4 rounded-lg bg-white/10 px-4 py-3 text-center ring-1 ring-white/15">
                        <p class="text-sm font-black uppercase tracking-wide text-[#F5B400]">Office of the President</p>
                        <p class="mt-1 text-lg font-black text-white">NABAMS President</p>
                    </div>
                </div>

                <div>
                    <p class="text-sm font-black uppercase tracking-wide text-[#F5B400]">President's Welcome</p>
                    <h2 class="mt-4 text-3xl font-black leading-tight text-white sm:text-4xl">
                        Welcome to your NABAMS journey, {{ $user->firstname }}.
                    </h2>
                    <div class="mt-5 space-y-4 text-sm leading-7 text-[#F2F2F2]/85">
                        <p>
                            On behalf of NABAMS, I warmly welcome every new and returning student to a session of growth, service, excellence, and purposeful leadership.
                        </p>
                        <p>
                            You are part of a community built to help you learn, connect, lead, and prepare confidently for the business world. Stay active, use the resources available here, take your academics seriously, and never hesitate to contribute your ideas.
                        </p>
                        <p class="font-bold text-white">
                            NABAMS leads, others follow. Let us make this session productive together. <br /> Thanks.
                        </p>
                    </div>
                </div>
            </div>
        @endif
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
