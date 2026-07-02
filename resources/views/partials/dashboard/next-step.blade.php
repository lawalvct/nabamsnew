<section class="mt-6 rounded-lg bg-[#0A2A6B] p-6 text-white shadow-xl">
    <div class="flex flex-col gap-4 md:flex-row md:items-center md:justify-between">
        <div>
            <p class="text-sm font-black uppercase tracking-wide text-[#F5B400]">{{ $isAdmin ? 'Build Out Modules' : 'Next Step' }}</p>
            <h2 class="mt-2 text-2xl font-black">{{ $isAdmin ? 'Dashboard modules are ready for real pages.' : 'Complete your profile and keep your fee status updated.' }}</h2>
            <p class="mt-2 max-w-3xl text-sm leading-7 text-[#F2F2F2]/75">
                {{ $isAdmin ? 'Each sidebar item currently points to a section placeholder and can later be connected to a controller page.' : 'Your mobile bottom navigation keeps the most important member actions within thumb reach.' }}
            </p>
        </div>
        <a href="#profile" class="inline-flex justify-center rounded-lg bg-[#F5B400] px-5 py-3 text-sm font-black text-[#0A2A6B] transition hover:bg-[#ffd15c]">View Profile</a>
    </div>
</section>
