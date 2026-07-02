<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="scroll-smooth">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="description" content="Meet the NABAMS executive team.">

        <title>Executives - NABAMS</title>
        <link rel="icon" type="image/png" href="{{ asset('favicon.png') }}">

        @fonts

        @if (file_exists(public_path('build/manifest.json')) || file_exists(public_path('hot')))
            @vite(['resources/css/app.css', 'resources/js/app.js'])
        @else
            <script src="https://cdn.jsdelivr.net/npm/@tailwindcss/browser@4"></script>
        @endif
    </head>
    <body class="min-h-screen bg-white font-sans text-[#2E2E2E] antialiased">
        @include('partials.site.topbar')
        @include('partials.site.navbar')

        @php
            $executives = [
                [
                    'name' => 'COMR. AWOYELU OLAYINKA',
                    'position' => 'President',
                    'image' => 'uploads/excos/exco1.JPG',
                    'summary' => 'Leading the association with a focus on excellence, representation, and purposeful student engagement.',
                ],
                [
                    'name' => 'Comr. Anjorin Gidion',
                    'position' => 'Vice President',
                    'image' => 'uploads/excos/exco2.jpg',
                    'summary' => 'Supporting the executive agenda and strengthening collaboration across the NABAMS community.',
                ],
                [
                    'name' => 'Comr. Nnaemeka Chidera',
                    'position' => 'General Secretary',
                    'image' => 'uploads/excos/exco3.jpg',
                    'summary' => 'Coordinating records, communication, and administrative flow for association activities.',
                ],
                [
                    'name' => 'Comr. Alesanmi Nurat',
                    'position' => 'Treasurer',
                    'image' => 'uploads/excos/exco4.jpg',
                    'summary' => 'Promoting transparent financial stewardship and responsible resource management.',
                ],
            ];
        @endphp

        <main>
            <section class="relative overflow-hidden bg-[#0A2A6B] text-white">
                <div class="absolute inset-0 bg-[linear-gradient(115deg,rgba(10,42,107,0.98),rgba(10,42,107,0.88),rgba(31,167,116,0.72))]"></div>
                <div class="relative mx-auto max-w-7xl px-4 py-16 sm:px-6 sm:py-20 lg:px-8">
                    <div class="max-w-3xl">
                        <p class="inline-flex rounded-full border border-white/20 bg-white/10 px-4 py-2 text-sm font-black uppercase tracking-wide text-[#F5B400]">NABAMS Executives</p>
                        <h1 class="mt-6 text-4xl font-black leading-tight text-white sm:text-5xl lg:text-6xl">Meet the student leaders serving NABAMS</h1>
                        <p class="mt-6 max-w-2xl text-base leading-8 text-[#F2F2F2] sm:text-lg">
                            Our executive team works to represent members, strengthen student welfare, and drive programmes that help Business Administration and Management students grow.
                        </p>
                    </div>
                </div>
            </section>

            <section class="bg-[#F2F2F2] py-16 sm:py-20">
                <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
                    <div class="flex flex-col gap-4 md:flex-row md:items-end md:justify-between">
                        <div>
                            <p class="text-sm font-black uppercase tracking-wide text-[#F5B400]">Leadership Team</p>
                            <h2 class="mt-3 text-3xl font-black text-[#0A2A6B] sm:text-4xl">Legacy 2024 Excos</h2>
                        </div>
                        <p class="max-w-2xl text-sm leading-7 text-[#2E2E2E]/75">
                            These officers serve with commitment, teamwork, and accountability, helping NABAMS remain a strong platform for academic and professional development.
                        </p>
                    </div>

                    <div class="mt-10 grid gap-6 sm:grid-cols-2 lg:grid-cols-4">
                        @foreach ($executives as $executive)
                            <article class="overflow-hidden rounded-lg bg-white shadow-sm ring-1 ring-[#0A2A6B]/10 transition hover:-translate-y-1 hover:shadow-xl">
                                <div class="bg-[#0A2A6B] p-3">
                                    <div class="overflow-hidden rounded-lg border-4 border-white bg-white">
                                        <img src="{{ asset($executive['image']) }}" alt="{{ $executive['name'] }}" class="aspect-[4/5] w-full object-cover object-top">
                                    </div>
                                </div>
                                <div class="p-5">
                                    <p class="inline-flex rounded-full bg-[#F5B400]/20 px-3 py-1 text-xs font-black uppercase tracking-wide text-[#0A2A6B]">{{ $executive['position'] }}</p>
                                    <h3 class="mt-4 text-xl font-black leading-snug text-[#0A2A6B]">{{ $executive['name'] }}</h3>
                                    <p class="mt-3 text-sm leading-6 text-[#2E2E2E]/75">{{ $executive['summary'] }}</p>
                                </div>
                            </article>
                        @endforeach
                    </div>
                </div>
            </section>

            <section class="bg-white py-16 sm:py-20">
                <div class="mx-auto grid max-w-7xl gap-8 px-4 sm:px-6 lg:grid-cols-[0.8fr_1.2fr] lg:items-center lg:px-8">
                    <div>
                        <p class="text-sm font-black uppercase tracking-wide text-[#F5B400]">Service And Accountability</p>
                        <h2 class="mt-3 text-3xl font-black text-[#0A2A6B] sm:text-4xl">Focused on student progress</h2>
                    </div>
                    <div class="grid gap-5 md:grid-cols-3">
                        <div class="rounded-lg border border-[#0A2A6B]/10 bg-[#F2F2F2] p-5">
                            <h3 class="font-black text-[#0A2A6B]">Representation</h3>
                            <p class="mt-3 text-sm leading-6 text-[#2E2E2E]/75">Listening to members and presenting student interests clearly.</p>
                        </div>
                        <div class="rounded-lg border border-[#0A2A6B]/10 bg-[#F2F2F2] p-5">
                            <h3 class="font-black text-[#0A2A6B]">Programmes</h3>
                            <p class="mt-3 text-sm leading-6 text-[#2E2E2E]/75">Supporting seminars, workshops, contests, and welfare activities.</p>
                        </div>
                        <div class="rounded-lg border border-[#0A2A6B]/10 bg-[#F2F2F2] p-5">
                            <h3 class="font-black text-[#0A2A6B]">Growth</h3>
                            <p class="mt-3 text-sm leading-6 text-[#2E2E2E]/75">Creating opportunities for leadership, confidence, and community.</p>
                        </div>
                    </div>
                </div>
            </section>

            @include('partials.site.registration-cta')
        </main>

        @include('partials.site.footer')
    </body>
</html>
