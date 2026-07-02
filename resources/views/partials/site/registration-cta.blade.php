<section class="bg-white py-16 sm:py-20" id="registration">
    <div class="mx-auto grid max-w-7xl gap-10 px-4 sm:px-6 lg:grid-cols-[0.9fr_1.1fr] lg:items-center lg:px-8">
        <div>
            <p class="text-sm font-black uppercase tracking-wide text-[#F5B400]">Membership Registration</p>
            <h2 class="mt-3 text-3xl font-black text-[#0A2A6B] sm:text-4xl">Become a registered NABAMS member</h2>
            <p class="mt-5 max-w-xl text-base leading-8 text-[#2E2E2E]/75">Register once with your personal and academic details, complete the simple security check, and get access to your member dashboard immediately.</p>

            <div class="mt-7 flex flex-col gap-3 sm:flex-row">
                <a href="{{ route('register') }}" class="inline-flex items-center justify-center rounded-lg bg-[#1FA774] px-6 py-3 text-sm font-black text-white transition hover:bg-[#198b61]">Create Account</a>
                <a href="{{ route('login') }}" class="inline-flex items-center justify-center rounded-lg border border-[#0A2A6B]/20 px-6 py-3 text-sm font-black text-[#0A2A6B] transition hover:border-[#1FA774] hover:text-[#1FA774]">Login to Dashboard</a>
            </div>
        </div>

        <div class="rounded-lg bg-[#0A2A6B] p-5 text-white shadow-xl sm:p-7">
            <div class="grid gap-4 sm:grid-cols-2">
                <div class="rounded-lg border border-white/10 bg-white/10 p-5">
                    <p class="text-sm font-black text-[#F5B400]">Personal Info</p>
                    <p class="mt-2 text-sm leading-6 text-[#F2F2F2]/80">Full name, gender, date of birth, phone, and email.</p>
                </div>
                <div class="rounded-lg border border-white/10 bg-white/10 p-5">
                    <p class="text-sm font-black text-[#F5B400]">Academic Info</p>
                    <p class="mt-2 text-sm leading-6 text-[#F2F2F2]/80">Matric number, department, level, and member type.</p>
                </div>
                <div class="rounded-lg border border-white/10 bg-white/10 p-5">
                    <p class="text-sm font-black text-[#F5B400]">Security Check</p>
                    <p class="mt-2 text-sm leading-6 text-[#F2F2F2]/80">Simple math question plus protected form submission.</p>
                </div>
                <div class="rounded-lg border border-white/10 bg-white/10 p-5">
                    <p class="text-sm font-black text-[#F5B400]">Optional Details</p>
                    <p class="mt-2 text-sm leading-6 text-[#F2F2F2]/80">Passport photo, WhatsApp number, and home address.</p>
                </div>
            </div>
        </div>
    </div>
</section>
