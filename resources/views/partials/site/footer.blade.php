<footer class="bg-[#0A2A6B] text-[#F2F2F2]">
    <div class="mx-auto grid max-w-7xl gap-10 px-4 py-12 sm:px-6 md:grid-cols-[1.1fr_1.5fr_1fr] lg:px-8">
        <div>
            <a href="{{ url('/') }}" class="inline-flex items-center gap-3 text-2xl font-black text-white">
                <span class="grid h-12 w-12 place-items-center overflow-hidden rounded-lg bg-white p-1">
                    <img src="{{ asset('logo.png') }}" alt="NABAMS logo" class="h-full w-full object-contain">
                </span>
                NABAMS
            </a>
            <p class="mt-4 max-w-sm text-sm leading-7 text-[#F2F2F2]/75">National Association of Business Administration and Management Students.</p>
            <a href="mailto:ogitechnabamsleads@gmail.com" class="mt-4 block break-words text-sm font-semibold text-[#F5B400]">ogitechnabamsleads@gmail.com</a>
        </div>

        <div class="grid gap-8 sm:grid-cols-2">
            <div>
                <h3 class="text-sm font-black uppercase tracking-wide text-white">Menu</h3>
                <div class="mt-4 grid gap-3 text-sm text-[#F2F2F2]/75">
                    <a class="hover:text-white" href="{{ url('/') }}">Home</a>
                    <a class="hover:text-white" href="{{ url('/about') }}">About us</a>
                    <a class="hover:text-white" href="{{ url('/resources') }}">Resources</a>
                    <a class="hover:text-white" href="{{ url('/membership') }}">Membership</a>
                    <a class="hover:text-white" href="{{ url('/events') }}">Events</a>
                </div>
            </div>
            <div>
                <h3 class="text-sm font-black uppercase tracking-wide text-white">Account</h3>
                <div class="mt-4 grid gap-3 text-sm text-[#F2F2F2]/75">
                    <a class="hover:text-white" href="{{ url('/login') }}">Login</a>
                    <a class="hover:text-white" href="{{ url('/register') }}">Registration</a>
                    <a class="hover:text-white" href="{{ url('/dashboard') }}">My Profile</a>
                    <a class="hover:text-white" href="{{ url('/contest') }}">Election Contests</a>
                </div>
            </div>
        </div>

        <div class="rounded-lg border border-white/10 bg-white/5 p-5">
            <h3 class="font-black text-white">Legacy 2024 Excos</h3>
            <p class="mt-3 text-sm leading-6 text-[#F2F2F2]/75">Honouring the leaders who served the association and strengthened the NABAMS community.</p>
        </div>
    </div>
    <div class="border-t border-white/10 px-4 py-5 text-center text-sm text-[#F2F2F2]/70">
        Copyright &copy; 2024 NABAMS
    </div>
</footer>
