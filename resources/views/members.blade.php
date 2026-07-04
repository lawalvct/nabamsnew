<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="scroll-smooth">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="description" content="Meet active NABAMS members through the official member portrait wall.">

        <title>Our Members - NABAMS</title>
        <link rel="icon" type="image/png" href="{{ asset('favicon.png') }}">

        @fonts

        @if (file_exists(public_path('build/manifest.json')) || file_exists(public_path('hot')))
            @vite(['resources/css/app.css', 'resources/js/app.js'])
        @else
            <script src="https://cdn.jsdelivr.net/npm/@tailwindcss/browser@4"></script>
        @endif

        <style>
            .member-card.is-revealed {
                box-shadow: 0 0 0 4px #F5B400, 0 20px 40px rgba(10, 42, 107, 0.18);
            }

            .member-card.is-revealed [data-member-overlay] {
                opacity: 1;
            }

            .member-card.is-revealed [data-member-details] {
                translate: 0 0;
                transform: translateY(0);
            }
        </style>
    </head>
    <body class="min-h-screen bg-white font-sans text-[#2E2E2E] antialiased">
        @include('partials.site.topbar')
        @include('partials.site.navbar')

        <main>
            <section class="relative overflow-hidden bg-[#0A2A6B] text-white">
                <div class="absolute inset-0 bg-[linear-gradient(115deg,rgba(10,42,107,0.98),rgba(10,42,107,0.9),rgba(31,167,116,0.74))]"></div>
                <div class="relative mx-auto grid max-w-7xl gap-8 px-4 py-16 sm:px-6 sm:py-20 lg:grid-cols-[1fr_0.7fr] lg:items-end lg:px-8">
                    <div>
                        <p class="inline-flex rounded-full border border-white/20 bg-white/10 px-4 py-2 text-sm font-black uppercase tracking-wide text-[#F5B400]">Our Members</p>
                        <h1 class="mt-6 max-w-4xl text-4xl font-black leading-tight text-white sm:text-5xl lg:text-6xl">The faces that make NABAMS alive</h1>
                        <p class="mt-6 max-w-2xl text-base leading-8 text-[#F2F2F2] sm:text-lg">
                            A living wall of active Business Administration and Management students building friendships, confidence, leadership, and academic excellence together.
                        </p>
                    </div>

                    <div class="rounded-lg border border-white/15 bg-white/10 p-5 shadow-2xl backdrop-blur">
                        <div class="grid grid-cols-3 gap-3">
                            @foreach ($members->take(6) as $member)
                                @php
                                    $previewGradients = [
                                        'from-[#F5B400] via-white to-[#1FA774]',
                                        'from-[#1FA774] via-white to-[#0A2A6B]',
                                        'from-[#0A2A6B] via-white to-[#F5B400]',
                                    ];
                                @endphp
                                <div class="rounded-lg bg-gradient-to-br {{ $previewGradients[$loop->index % count($previewGradients)] }} p-1">
                                    <img src="{{ $member->public_image_url }}" alt="NABAMS member portrait" class="aspect-square w-full rounded-md object-cover">
                                </div>
                            @endforeach
                        </div>
                        <p class="mt-4 text-center text-sm font-bold text-[#F2F2F2]">{{ $members->count() }} featured members on this visit</p>
                    </div>
                </div>
            </section>

            <section class="bg-[#F2F2F2] py-12 sm:py-16">
                <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
                    <div class="flex flex-col gap-5 sm:flex-row sm:items-end sm:justify-between">
                        <div>
                            <p class="text-sm font-black uppercase tracking-wide text-[#F5B400]">Member Wall</p>
                            <h2 class="mt-3 text-3xl font-black text-[#0A2A6B] sm:text-4xl">Tap a portrait. Meet a member.</h2>
                        </div>
                        <div class="flex flex-wrap gap-3">
                            <button type="button" id="shuffle-members" class="rounded-lg bg-[#0A2A6B] px-5 py-3 text-sm font-black text-white transition hover:bg-[#123982]">Shuffle Wall</button>
                            <button type="button" id="hide-member-names" class="rounded-lg border border-[#0A2A6B]/20 bg-white px-5 py-3 text-sm font-black text-[#0A2A6B] transition hover:border-[#1FA774] hover:text-[#1FA774]">Hide Names</button>
                        </div>
                    </div>

                    @if ($members->isEmpty())
                        <div class="mt-8 rounded-lg bg-white p-8 text-center shadow-sm ring-1 ring-[#0A2A6B]/10">
                            <p class="text-sm font-black uppercase tracking-wide text-[#F5B400]">No portraits yet</p>
                            <h3 class="mt-3 text-2xl font-black text-[#0A2A6B]">The member wall is waiting for profile photos.</h3>
                            <p class="mx-auto mt-3 max-w-2xl text-sm leading-7 text-[#2E2E2E]/70">Active members will appear here after they upload a profile picture.</p>
                        </div>
                    @else
                        <div id="member-wall" class="mt-8 grid grid-cols-2 gap-4 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-5 xl:grid-cols-6">
                            @php
                                $gradients = [
                                    'from-[#0A2A6B] via-[#1FA774] to-[#F5B400]',
                                    'from-[#F5B400] via-[#1FA774] to-[#0A2A6B]',
                                    'from-[#1FA774] via-[#F5B400] to-[#0A2A6B]',
                                    'from-[#0A2A6B] via-[#F5B400] to-[#1FA774]',
                                    'from-[#1FA774] via-[#0A2A6B] to-[#F5B400]',
                                    'from-[#F5B400] via-[#0A2A6B] to-[#1FA774]',
                                ];
                            @endphp

                            @foreach ($members as $member)
                                @php
                                    $displayName = $member->nickname ?: $member->name;
                                    $levelText = trim(($member->academic_level ?? '').' '.($member->member_type ?? ''));
                                @endphp

                                <article class="member-card group rounded-lg bg-gradient-to-br {{ $gradients[$loop->index % count($gradients)] }} p-[3px] shadow-sm transition duration-300 hover:-translate-y-1 hover:shadow-xl" data-member-card>
                                    <button type="button" class="relative block w-full overflow-hidden rounded-md bg-[#0A2A6B] text-left" aria-label="Reveal member">
                                        <img src="{{ $member->public_image_url }}" alt="NABAMS member portrait" loading="lazy" class="aspect-square w-full object-cover transition duration-500 group-hover:scale-105">
                                        <span class="absolute inset-0 bg-gradient-to-t from-[#0A2A6B]/95 via-[#0A2A6B]/20 to-transparent opacity-0 transition duration-300 group-hover:opacity-100" data-member-overlay></span>
                                        <span class="absolute inset-x-0 bottom-0 translate-y-full p-3 text-white transition duration-300 group-hover:translate-y-0" data-member-details>
                                            <span class="block truncate text-sm font-black">{{ $displayName ?: 'NABAMS Member' }}</span>
                                            <span class="mt-1 block truncate text-xs font-bold text-[#F5B400]">{{ $levelText ?: 'Business Administration & Management' }}</span>
                                        </span>
                                    </button>
                                </article>
                            @endforeach
                        </div>
                    @endif
                </div>
            </section>

            <section class="bg-white py-12 sm:py-16">
                <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
                    <div class="rounded-lg bg-[#0A2A6B] p-6 text-white shadow-xl sm:p-8">
                        <div class="grid gap-6 lg:grid-cols-[1fr_auto] lg:items-center">
                            <div>
                                <p class="text-sm font-black uppercase tracking-wide text-[#F5B400]">Join The Wall</p>
                                <h2 class="mt-3 text-3xl font-black">Update your profile and be seen.</h2>
                                <p class="mt-4 max-w-2xl text-sm leading-7 text-[#F2F2F2]/80">Members with active accounts and profile photos can appear in the public NABAMS member wall.</p>
                            </div>
                            <a href="{{ route('login') }}" class="inline-flex justify-center rounded-lg bg-[#F5B400] px-5 py-3 text-sm font-black text-[#0A2A6B] transition hover:bg-[#ffd15c]">Go to Dashboard</a>
                        </div>
                    </div>
                </div>
            </section>
        </main>

        @include('partials.site.footer')

        <script>
            document.addEventListener('DOMContentLoaded', () => {
                const wall = document.getElementById('member-wall');
                const shuffleButton = document.getElementById('shuffle-members');
                const hideNamesButton = document.getElementById('hide-member-names');

                const closeAllCards = () => {
                    document.querySelectorAll('[data-member-card]').forEach((card) => {
                        card.classList.remove('is-revealed');
                        card.querySelector('[data-member-overlay]')?.classList.remove('opacity-100');
                        card.querySelector('[data-member-overlay]')?.classList.add('opacity-0');
                        card.querySelector('[data-member-details]')?.classList.remove('translate-y-0');
                        card.querySelector('[data-member-details]')?.classList.add('translate-y-full');
                    });
                };

                document.querySelectorAll('[data-member-card] button').forEach((button) => {
                    button.addEventListener('click', () => {
                        const card = button.closest('[data-member-card]');
                        const isOpen = card.classList.contains('is-revealed');

                        closeAllCards();

                        if (! isOpen) {
                            card.classList.add('is-revealed');
                            button.querySelector('[data-member-overlay]')?.classList.remove('opacity-0');
                            button.querySelector('[data-member-overlay]')?.classList.add('opacity-100');
                            button.querySelector('[data-member-details]')?.classList.remove('translate-y-full');
                            button.querySelector('[data-member-details]')?.classList.add('translate-y-0');
                        }
                    });
                });

                hideNamesButton?.addEventListener('click', closeAllCards);

                shuffleButton?.addEventListener('click', () => {
                    if (! wall) {
                        return;
                    }

                    closeAllCards();

                    [...wall.children]
                        .sort(() => Math.random() - 0.5)
                        .forEach((card) => wall.appendChild(card));
                });
            });
        </script>
    </body>
</html>
