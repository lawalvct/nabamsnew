@php
    $sliderPath = public_path('uploads/sliders');
    $sliderImages = \Illuminate\Support\Facades\File::isDirectory($sliderPath)
        ? collect(\Illuminate\Support\Facades\File::files($sliderPath))
            ->filter(fn ($file) => in_array(strtolower($file->getExtension()), ['jpg', 'jpeg', 'png', 'webp', 'gif']))
            ->map(fn ($file) => asset('uploads/sliders/'.$file->getFilename()))
            ->values()
        : collect();

    $initialSliderImage = $sliderImages->isNotEmpty()
        ? $sliderImages->random()
        : 'https://images.unsplash.com/photo-1523580846011-d3a5bc25702b?auto=format&fit=crop&w=1800&q=80';

    $heroSlideCount = max($sliderImages->count(), 3);
@endphp

<section class="relative overflow-hidden bg-[#0A2A6B] text-white">
    <div class="absolute inset-0 opacity-100">
        <img
            id="hero-image"
            src="{{ $initialSliderImage }}"
            alt="NABAMS campus slider image"
            class="h-full w-full object-cover transition-opacity duration-700"
            data-hero-images='@json($sliderImages)'
        >
    </div>
    <div class="absolute inset-0 bg-[linear-gradient(115deg,rgba(10,42,107,0.75),rgba(10,42,107,0.55),rgba(31,167,116,0.35))]"></div>

    <div class="relative mx-auto grid min-h-[640px] max-w-7xl items-center gap-10 px-4 py-20 sm:px-6 lg:grid-cols-[1.1fr_0.9fr] lg:px-8 lg:py-24">
        <div class="max-w-3xl">
            <p id="hero-label" class="mb-5 inline-flex rounded-full border border-white/20 bg-white/10 px-4 py-2 text-sm font-bold uppercase tracking-wide text-[#F5B400]">National Association</p>
            <h1 id="hero-title" class="text-4xl font-black leading-tight text-white sm:text-5xl lg:text-7xl">Welcome to NABAMS</h1>
            <p id="hero-subtitle" class="mt-6 max-w-2xl text-lg font-semibold leading-8 text-white sm:text-2xl">National Association of Business Administration and Management Students (NABAMS)</p>
            <p id="hero-note" class="mt-5 max-w-2xl text-base leading-7 text-[#F2F2F2] sm:text-lg">Building capable business leaders through service, learning, and student representation.</p>

            <div class="mt-9 flex flex-col gap-3 sm:flex-row">
                <a href="{{ url('/about') }}" class="inline-flex items-center justify-center rounded-lg bg-[#F5B400] px-6 py-3 text-sm font-black text-[#0A2A6B] transition hover:bg-[#ffd15c]">Read More</a>
                <a href="#contact" class="inline-flex items-center justify-center rounded-lg border border-white/30 px-6 py-3 text-sm font-black text-white transition hover:bg-white/10">Contact NABAMS</a>
            </div>

            <div class="mt-10 flex gap-3" aria-label="Hero slide controls">
                @for ($dotIndex = 0; $dotIndex < $heroSlideCount; $dotIndex++)
                    <button
                        type="button"
                        class="h-2.5 {{ $dotIndex === 0 ? 'w-10 bg-[#F5B400]' : 'w-2.5 bg-white/40 hover:bg-white/70' }} rounded-full transition-all"
                        data-hero-dot="{{ $dotIndex }}"
                        aria-label="Show slide {{ $dotIndex + 1 }}"
                    ></button>
                @endfor
            </div>
        </div>

        <div class="grid gap-4 rounded-lg border border-white/15 bg-white/10 p-5 shadow-2xl backdrop-blur md:grid-cols-2 lg:grid-cols-1 xl:grid-cols-2">
            <div class="rounded-lg bg-white p-5 text-[#0A2A6B]">
                <p class="text-sm font-bold uppercase tracking-wide text-[#F5B400]">Session</p>
                <p class="mt-3 text-3xl font-black">2025-2026</p>
                <p class="mt-2 text-sm leading-6 text-[#2E2E2E]/75">Current academic session for NABAMS activities and student programmes.</p>
            </div>
            <div class="rounded-lg bg-[#F5B400] p-5 text-[#0A2A6B]">
                <p class="text-sm font-bold uppercase tracking-wide">Motto</p>
                <p class="mt-3 text-3xl font-black">Leads</p>
                <p class="mt-2 text-sm font-semibold leading-6">Others Follow</p>
            </div>
        </div>
    </div>
</section>
