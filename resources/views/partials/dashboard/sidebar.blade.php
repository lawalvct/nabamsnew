<aside class="fixed inset-y-0 left-0 z-40 hidden w-[280px] bg-[#0A2A6B] text-white lg:flex lg:flex-col">
    <div class="flex items-center gap-3 border-b border-white/10 px-5 py-5">
        <a href="{{ url('/') }}" class="grid h-12 w-12 place-items-center overflow-hidden rounded-lg bg-white p-1">
            <img src="{{ asset('logo.png') }}" alt="NABAMS logo" class="h-full w-full object-contain">
        </a>
        <div>
            <p class="text-lg font-black leading-none">NABAMS</p>
            <p class="mt-1 text-xs font-bold uppercase tracking-wide text-[#F5B400]">{{ $isAdmin ? 'Admin Console' : 'Member App' }}</p>
        </div>
    </div>

    <nav class="flex-1 overflow-y-auto px-4 py-5" aria-label="Dashboard navigation">
        <div class="grid gap-1">
            @foreach ($menus as $index => $menu)
                <a
                    href="{{ $index === 0 ? route('dashboard') : $menu['href'] }}"
                    class="group flex items-center gap-3 rounded-lg px-3 py-3 text-sm font-bold text-white/78 transition hover:bg-white/10 hover:text-white {{ $index === 0 ? 'bg-white text-[#0A2A6B] hover:bg-white hover:text-[#0A2A6B]' : '' }}"
                >
                    <span class="grid h-9 w-9 shrink-0 place-items-center rounded-lg {{ $index === 0 ? 'bg-[#F5B400] text-[#0A2A6B]' : 'bg-white/10 text-[#F5B400] group-hover:bg-[#F5B400] group-hover:text-[#0A2A6B]' }} text-xs font-black">
                        {{ $menu['icon'] }}
                    </span>
                    <span>{{ $menu['label'] }}</span>
                </a>
            @endforeach
        </div>
    </nav>

    <div class="border-t border-white/10 p-4">
        <form action="{{ route('logout') }}" method="POST">
            @csrf
            <button type="submit" class="w-full rounded-lg bg-[#F5B400] px-4 py-3 text-sm font-black text-[#0A2A6B] transition hover:bg-[#ffd15c]">Logout</button>
        </form>
    </div>
</aside>
