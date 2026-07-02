<section class="mt-6 grid gap-5 md:grid-cols-3">
    @foreach ($quickStats as $stat)
        <div class="rounded-lg bg-white p-6 shadow-sm ring-1 ring-[#0A2A6B]/10">
            <p class="text-sm font-black uppercase tracking-wide text-[#F5B400]">{{ $stat['label'] }}</p>
            <p class="mt-3 text-2xl font-black {{ $stat['tone'] === 'green' ? 'text-[#1FA774]' : 'text-[#0A2A6B]' }}">{{ $stat['value'] }}</p>
            <p class="mt-2 text-sm leading-6 text-[#2E2E2E]/65">{{ $stat['hint'] }}</p>
        </div>
    @endforeach
</section>
