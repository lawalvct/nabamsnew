<nav class="fixed inset-x-0 bottom-0 z-50 border-t border-[#0A2A6B]/10 bg-white px-2 pb-3 pt-2 shadow-[0_-12px_30px_rgba(10,42,107,0.12)] lg:hidden" aria-label="Mobile dashboard navigation">
    <div class="mx-auto grid max-w-md grid-cols-5 gap-1">
        @foreach ($mobileMenus as $index => $menu)
            <a href="{{ $index === 0 ? route('dashboard') : $menu['href'] }}" class="flex min-w-0 flex-col items-center justify-center gap-1 rounded-lg px-2 py-2 text-center {{ $index === 0 ? 'bg-[#0A2A6B] text-white' : 'text-[#0A2A6B]' }}">
                <span class="grid h-8 w-8 place-items-center rounded-lg {{ $index === 0 ? 'bg-[#F5B400] text-[#0A2A6B]' : 'bg-[#F2F2F2] text-[#0A2A6B]' }} text-[11px] font-black">{{ $menu['icon'] }}</span>
                <span class="w-full truncate text-[11px] font-black leading-tight">{{ $menu['label'] }}</span>
            </a>
        @endforeach
    </div>
</nav>
