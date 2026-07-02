<header class="sticky top-0 z-30 border-b border-[#0A2A6B]/10 bg-white/90 backdrop-blur">
    <div class="flex items-center justify-between gap-4 px-4 py-4 sm:px-6 lg:px-8">
        <div class="flex min-w-0 items-center gap-3">
            <a href="{{ url('/') }}" class="grid h-11 w-11 place-items-center overflow-hidden rounded-lg bg-[#0A2A6B] p-1 lg:hidden">
                <img src="{{ asset('logo.png') }}" alt="NABAMS logo" class="h-full w-full object-contain">
            </a>
            <div class="min-w-0">
                <p class="truncate text-xs font-black uppercase tracking-wide text-[#F5B400]">{{ $isAdmin ? 'Admin Dashboard' : 'Member Dashboard' }}</p>
                <h1 class="truncate text-xl font-black text-[#0A2A6B] sm:text-2xl">Welcome, {{ $displayName }}</h1>
            </div>
        </div>

        <div class="flex items-center gap-3">
            <a href="#profile" class="hidden rounded-lg border border-[#0A2A6B]/10 px-4 py-2 text-sm font-black text-[#0A2A6B] transition hover:border-[#1FA774] hover:text-[#1FA774] sm:inline-flex">Profile</a>
            <form action="{{ route('logout') }}" method="POST" class="hidden sm:block lg:hidden">
                @csrf
                <button type="submit" class="rounded-lg bg-[#F5B400] px-4 py-2 text-sm font-black text-[#0A2A6B] transition hover:bg-[#ffd15c]">Logout</button>
            </form>
            <div class="grid h-11 w-11 shrink-0 place-items-center overflow-hidden rounded-lg bg-[#0A2A6B] text-sm font-black text-white">
                @if ($user->image)
                    <img src="{{ asset($user->image) }}" alt="{{ $displayName }}" class="h-full w-full object-cover">
                @else
                    {{ $initials ?: 'NM' }}
                @endif
            </div>
        </div>
    </div>
</header>
