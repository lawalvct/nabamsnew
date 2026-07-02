<header class="sticky top-0 z-50 border-b border-white/10 bg-[#0A2A6B]/95 text-white backdrop-blur">
    <nav class="mx-auto flex max-w-7xl items-center justify-between px-4 py-4 sm:px-6 lg:px-8" aria-label="Main navigation">
        <a href="{{ url('/') }}" class="flex items-center gap-3">
            <span class="grid h-11 w-11 place-items-center overflow-hidden rounded-lg bg-white p-1">
                <img src="{{ asset('logo.png') }}" alt="NABAMS logo" class="h-full w-full object-contain">
            </span>
            <span>
                <span class="block text-lg font-black leading-none text-white">NABAMS</span>
                <span class="block text-xs font-semibold uppercase tracking-wide text-[#F5B400]">Leads</span>
            </span>
        </a>

        <button
            id="nav-toggle"
            type="button"
            class="inline-flex h-11 w-11 items-center justify-center rounded-lg border border-white/20 text-white lg:hidden"
            aria-label="Toggle navigation"
            aria-expanded="false"
            aria-controls="mobile-menu"
        >
            <svg data-menu-open-icon xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M4 6h16M4 12h16M4 18h16" />
            </svg>
            <svg data-menu-close-icon xmlns="http://www.w3.org/2000/svg" class="hidden h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M6 18 18 6M6 6l12 12" />
            </svg>
        </button>

        <div class="hidden items-center gap-7 text-sm font-semibold text-white lg:flex">
            <a class="transition hover:text-[#F5B400]" href="{{ url('/') }}">Home</a>
            <a class="transition hover:text-[#F5B400]" href="{{ url('/about') }}">About</a>
            <a class="transition hover:text-[#F5B400]" href="{{ route('excos') }}">Excos</a>
            <a class="transition hover:text-[#F5B400]" href="#{{ url('/membership') }}">Our Member</a>
            <a class="transition hover:text-[#F5B400]" href="{{ route('register') }}">Register</a>
            <a class="transition hover:text-[#F5B400]" href="#{{ url('/resources') }}">Resources</a>
            <a class="transition hover:text-[#F5B400]" href="#{{ url('/contest') }}">Contest</a>
           
            <a class="rounded-lg bg-[#F5B400] px-4 py-2.5 font-bold text-[#0A2A6B] transition hover:bg-[#ffd15c]" href="{{ url('/login') }}">Login to Dashboard</a>
        </div>
    </nav>

    <div id="mobile-menu" class="hidden border-t border-white/10 bg-[#0A2A6B] px-4 pb-5 lg:hidden">
        <div class="mx-auto grid max-w-7xl gap-1 py-3 text-sm font-semibold text-white">
            <a class="rounded-lg px-3 py-3 hover:bg-white/10 hover:text-[#F5B400]" href="{{ url('/') }}">Home</a>
            <a class="rounded-lg px-3 py-3 hover:bg-white/10 hover:text-[#F5B400]" href="{{ url('/about') }}">About</a>
            <a class="rounded-lg px-3 py-3 hover:bg-white/10 hover:text-[#F5B400]" href="{{ route('excos') }}">Excos</a>
            <a class="rounded-lg px-3 py-3 hover:bg-white/10 hover:text-[#F5B400]" href="{{ url('/membership') }}">Membership</a>
            <a class="rounded-lg px-3 py-3 hover:bg-white/10 hover:text-[#F5B400]" href="{{ route('register') }}">Register</a>
            <a class="rounded-lg px-3 py-3 hover:bg-white/10 hover:text-[#F5B400]" href="{{ url('/resources') }}">Resources</a>
            <a class="rounded-lg px-3 py-3 hover:bg-white/10 hover:text-[#F5B400]" href="{{ url('/contest') }}">Contest</a>
            <a class="rounded-lg px-3 py-3 hover:bg-white/10 hover:text-[#F5B400]" href="{{ url('/support') }}">Support</a>
            <a class="mt-2 rounded-lg bg-[#F5B400] px-3 py-3 text-center font-bold text-[#0A2A6B]" href="{{ url('/login') }}">Login to Dashboard</a>
        </div>
    </div>
</header>

<script>
    (() => {
        const navToggle = document.getElementById('nav-toggle');
        const mobileMenu = document.getElementById('mobile-menu');
        const openIcon = document.querySelector('[data-menu-open-icon]');
        const closeIcon = document.querySelector('[data-menu-close-icon]');

        if (!navToggle || !mobileMenu) {
            return;
        }

        navToggle.addEventListener('click', () => {
            const isOpen = mobileMenu.classList.toggle('hidden') === false;

            navToggle.setAttribute('aria-expanded', isOpen.toString());
            openIcon?.classList.toggle('hidden', isOpen);
            closeIcon?.classList.toggle('hidden', !isOpen);
        });
    })();
</script>
