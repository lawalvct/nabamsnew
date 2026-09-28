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
            /* Reveal state */
            .member-card.is-revealed {
                box-shadow: 0 0 0 4px #F5B400, 0 20px 40px rgba(10, 42, 107, 0.18);
                animation: member-pop 0.45s cubic-bezier(0.2, 0.8, 0.2, 1.4);
            }

            .member-card.is-revealed [data-member-overlay] {
                opacity: 1;
            }

            .member-card.is-revealed [data-member-details] {
                translate: 0 0;
                transform: translateY(0);
            }

            .member-card.is-broken {
                display: none;
            }

            /* Entrance: cards rise in as they scroll into view */
            .wall-animate .member-card:not(.is-in) {
                opacity: 0;
                transform: translateY(28px) scale(0.92) rotate(-2deg);
            }

            .wall-animate .member-card.is-in {
                transition:
                    opacity 0.6s ease var(--delay, 0ms),
                    transform 0.7s cubic-bezier(0.2, 0.8, 0.2, 1) var(--delay, 0ms),
                    translate 0.3s ease,
                    box-shadow 0.3s ease;
            }

            /* New members: flowing border + pulsing badge */
            .member-card[data-new="1"] {
                background-size: 220% 220%;
                animation: border-flow 5s ease-in-out infinite;
            }

            .member-card[data-new="1"].is-revealed {
                animation: member-pop 0.45s cubic-bezier(0.2, 0.8, 0.2, 1.4), border-flow 5s ease-in-out infinite;
            }

            .member-new-badge {
                animation: badge-pulse 2.2s ease-in-out infinite;
            }

            /* Spotlight roulette */
            .member-card.is-hopping {
                box-shadow: 0 0 0 4px #F5B400, 0 0 28px rgba(245, 180, 0, 0.65);
                translate: 0 -4px;
            }

            .member-card.is-spotlit {
                box-shadow: 0 0 0 4px #F5B400, 0 0 0 10px rgba(245, 180, 0, 0.25), 0 24px 48px rgba(10, 42, 107, 0.25);
                z-index: 5;
            }

            /* New faces filter */
            .wall-new-only .member-card[data-new="0"] {
                opacity: 0.18;
                filter: grayscale(1);
            }

            /* Hero */
            .hero-blob {
                animation: blob-drift 16s ease-in-out infinite alternate;
            }

            .preview-float {
                animation: preview-float 6s ease-in-out infinite;
                animation-delay: var(--float-delay, 0s);
            }

            .preview-tile img {
                transition: transform 0.35s ease, opacity 0.35s ease;
            }

            .preview-tile.is-flipping img {
                transform: rotateY(90deg) scale(0.9);
                opacity: 0.2;
            }

            .preview-tile img[data-broken] {
                opacity: 0;
            }

            /* Confetti */
            .confetti-piece {
                position: fixed;
                z-index: 60;
                width: 9px;
                height: 14px;
                border-radius: 2px;
                pointer-events: none;
                animation: confetti-burst 1.1s cubic-bezier(0.15, 0.7, 0.3, 1) forwards;
            }

            /* Toast */
            #wall-toast {
                transition: opacity 0.35s ease, transform 0.35s cubic-bezier(0.2, 0.8, 0.2, 1);
            }

            #wall-toast:not(.is-open) {
                opacity: 0;
                transform: translateY(24px);
                pointer-events: none;
            }

            @keyframes member-pop {
                0% { transform: scale(1); }
                45% { transform: scale(1.06); }
                100% { transform: scale(1); }
            }

            @keyframes border-flow {
                0%, 100% { background-position: 0% 50%; }
                50% { background-position: 100% 50%; }
            }

            @keyframes badge-pulse {
                0%, 100% { box-shadow: 0 0 0 0 rgba(245, 180, 0, 0.7); }
                60% { box-shadow: 0 0 0 8px rgba(245, 180, 0, 0); }
            }

            @keyframes blob-drift {
                0% { transform: translate(0, 0) scale(1); }
                100% { transform: translate(40px, -30px) scale(1.15); }
            }

            @keyframes preview-float {
                0%, 100% { transform: translateY(0); }
                50% { transform: translateY(-7px); }
            }

            @keyframes confetti-burst {
                0% { transform: translate(0, 0) rotate(0deg); opacity: 1; }
                100% { transform: translate(var(--dx), var(--dy)) rotate(var(--rot)); opacity: 0; }
            }

            @media (prefers-reduced-motion: reduce) {
                .member-card,
                .member-card[data-new="1"],
                .member-new-badge,
                .hero-blob,
                .preview-float {
                    animation: none !important;
                }

                .wall-animate .member-card:not(.is-in) {
                    opacity: 1;
                    transform: none;
                }
            }
        </style>
    </head>
    <body class="min-h-screen bg-white font-sans text-[#2E2E2E] antialiased">
        @include('partials.site.topbar')
        @include('partials.site.navbar')

        <main>
            <section class="relative overflow-hidden bg-[#0A2A6B] text-white">
                <div class="absolute inset-0 bg-[linear-gradient(115deg,rgba(10,42,107,0.98),rgba(10,42,107,0.9),rgba(31,167,116,0.74))]"></div>
                <div class="hero-blob pointer-events-none absolute -left-20 top-10 h-72 w-72 rounded-full bg-[#F5B400]/20 blur-3xl"></div>
                <div class="hero-blob pointer-events-none absolute -bottom-24 right-10 h-80 w-80 rounded-full bg-[#1FA774]/30 blur-3xl" style="animation-delay: -8s"></div>

                <div class="relative mx-auto grid max-w-7xl gap-8 px-4 py-16 sm:px-6 sm:py-20 lg:grid-cols-[1fr_0.7fr] lg:items-end lg:px-8">
                    <div>
                        <p class="inline-flex rounded-full border border-white/20 bg-white/10 px-4 py-2 text-sm font-black uppercase tracking-wide text-[#F5B400]">Our Members</p>
                        <h1 class="mt-6 max-w-4xl text-4xl font-black leading-tight text-white sm:text-5xl lg:text-6xl">The faces that make NABAMS alive</h1>
                        <p class="mt-6 max-w-2xl text-base leading-8 text-[#F2F2F2] sm:text-lg">
                            A living wall of active Business Administration and Management students building friendships, confidence, leadership, and academic excellence together.
                        </p>

                        <div class="mt-8 flex flex-wrap gap-4">
                            <div class="rounded-lg border border-white/15 bg-white/10 px-5 py-3 backdrop-blur">
                                <p class="text-3xl font-black text-white" data-count-up="{{ $totalMembers }}">{{ number_format($totalMembers) }}</p>
                                <p class="text-xs font-bold uppercase tracking-wide text-[#F2F2F2]/70">Faces on the wall</p>
                            </div>
                            @if ($newMemberJoinTimes->isNotEmpty())
                                <div class="rounded-lg border border-[#F5B400]/40 bg-[#F5B400]/15 px-5 py-3 backdrop-blur">
                                    <p class="text-3xl font-black text-[#F5B400]" data-count-up="{{ $newMemberJoinTimes->count() }}">{{ number_format($newMemberJoinTimes->count()) }}</p>
                                    <p class="text-xs font-bold uppercase tracking-wide text-[#F2F2F2]/70">New this month</p>
                                </div>
                            @endif
                        </div>
                    </div>

                    <div class="rounded-lg border border-white/15 bg-white/10 p-5 shadow-2xl backdrop-blur">
                        <div class="grid grid-cols-3 gap-3" id="preview-grid">
                            @php
                                $previewGradients = [
                                    'from-[#F5B400] via-white to-[#1FA774]',
                                    'from-[#1FA774] via-white to-[#0A2A6B]',
                                    'from-[#0A2A6B] via-white to-[#F5B400]',
                                ];
                            @endphp
                            @foreach ($previewMembers as $member)
                                <div class="preview-float" style="--float-delay: -{{ $loop->index * 0.9 }}s">
                                    <div class="preview-tile rounded-lg bg-gradient-to-br {{ $previewGradients[$loop->index % count($previewGradients)] }} p-1 perspective-[600px]" data-preview-tile>
                                        <img src="{{ $member->public_image_url }}" alt="NABAMS member portrait" class="aspect-square w-full rounded-md object-cover" onerror="this.dataset.broken = '1'">
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>
            </section>

            <section class="bg-[#F2F2F2] py-12 sm:py-16" id="member-wall-section">
                <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
                    <div class="flex flex-col gap-5 lg:flex-row lg:items-end lg:justify-between">
                        <div>
                            <p class="text-sm font-black uppercase tracking-wide text-[#F5B400]">Member Wall</p>
                            <h2 class="mt-3 text-3xl font-black text-[#0A2A6B] sm:text-4xl">Tap a portrait. Meet a member.</h2>
                        </div>
                        @if ($members->count() > 0)
                            <div class="flex flex-wrap gap-3">
                                <button type="button" id="spotlight-member" class="rounded-lg bg-[#F5B400] px-5 py-3 text-sm font-black text-[#0A2A6B] shadow-sm transition hover:-translate-y-0.5 hover:bg-[#ffd15c] disabled:cursor-wait disabled:opacity-70">Spotlight a Member</button>
                                <button type="button" id="shuffle-members" class="rounded-lg bg-[#0A2A6B] px-5 py-3 text-sm font-black text-white transition hover:-translate-y-0.5 hover:bg-[#123982]">Shuffle Wall</button>
                                @if ($newMemberJoinTimes->isNotEmpty())
                                    <button type="button" id="toggle-new-faces" aria-pressed="false" class="rounded-lg border border-[#0A2A6B]/20 bg-white px-5 py-3 text-sm font-black text-[#0A2A6B] transition hover:border-[#1FA774] hover:text-[#1FA774] aria-pressed:border-[#1FA774] aria-pressed:bg-[#1FA774] aria-pressed:text-white">New Faces</button>
                                @endif
                                <button type="button" id="hide-member-names" class="rounded-lg border border-[#0A2A6B]/20 bg-white px-5 py-3 text-sm font-black text-[#0A2A6B] transition hover:border-[#1FA774] hover:text-[#1FA774]">Hide Names</button>
                            </div>
                        @endif
                    </div>

                    @if ($members->count() === 0)
                        <div class="mt-8 rounded-lg bg-white p-8 text-center shadow-sm ring-1 ring-[#0A2A6B]/10">
                            <p class="text-sm font-black uppercase tracking-wide text-[#F5B400]">No portraits yet</p>
                            <h3 class="mt-3 text-2xl font-black text-[#0A2A6B]">The member wall is waiting for profile photos.</h3>
                            <p class="mx-auto mt-3 max-w-2xl text-sm leading-7 text-[#2E2E2E]/70">Active members will appear here after they upload a profile picture.</p>
                        </div>
                    @else
                        <div id="member-wall" class="mt-8 grid grid-cols-2 gap-4 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-5 xl:grid-cols-6">
                            @foreach ($members as $member)
                                @include('partials.site.member-card', ['member' => $member, 'gradientIndex' => $loop->index])
                            @endforeach
                        </div>

                        @if ($members->hasMorePages())
                            <div class="mt-8 text-center">
                                <button type="button" id="load-more-members" data-next-page-url="{{ $members->nextPageUrl() }}" class="rounded-lg bg-[#1FA774] px-6 py-3 text-sm font-black text-white shadow-sm transition hover:-translate-y-0.5 hover:bg-[#198b61]">
                                    Load More Members
                                </button>
                            </div>
                        @endif
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
                                <p class="mt-4 max-w-2xl text-sm leading-7 text-[#F2F2F2]/80">Members with active accounts and profile photos can appear in the public NABAMS member wall. New faces get a NEW badge and show up more often during their first month.</p>
                            </div>
                            <a href="{{ route('login') }}" class="inline-flex justify-center rounded-lg bg-[#F5B400] px-5 py-3 text-sm font-black text-[#0A2A6B] transition hover:bg-[#ffd15c]">Go to Dashboard</a>
                        </div>
                    </div>
                </div>
            </section>
        </main>

        <div id="wall-toast" role="status" aria-live="polite" class="fixed bottom-6 left-1/2 z-50 flex w-[calc(100%-2rem)] max-w-md -translate-x-1/2 items-center gap-4 rounded-lg bg-[#0A2A6B] px-5 py-4 text-white shadow-2xl ring-1 ring-white/10">
            <p class="flex-1 text-sm font-bold leading-6" data-toast-message></p>
            <button type="button" class="hidden shrink-0 rounded-lg bg-[#F5B400] px-3 py-2 text-xs font-black text-[#0A2A6B]" data-toast-action></button>
            <button type="button" class="shrink-0 text-lg font-black text-white/60 hover:text-white" aria-label="Dismiss" data-toast-close>&times;</button>
        </div>

        @include('partials.site.footer')

        <script>
            document.addEventListener('DOMContentLoaded', () => {
                const wall = document.getElementById('member-wall');
                const shuffleButton = document.getElementById('shuffle-members');
                const spotlightButton = document.getElementById('spotlight-member');
                const newFacesButton = document.getElementById('toggle-new-faces');
                const hideNamesButton = document.getElementById('hide-member-names');
                const loadMoreButton = document.getElementById('load-more-members');
                const toast = document.getElementById('wall-toast');
                const newMemberJoinTimes = @json($newMemberJoinTimes);
                const reduceMotion = window.matchMedia('(prefers-reduced-motion: reduce)').matches;
                const wait = (ms) => new Promise((resolve) => setTimeout(resolve, ms));

                // Mirrors App\Support\MemberWallOrder::weight() so client shuffles favour new faces too.
                const memberWeight = (card) => {
                    const joined = Number(card.dataset.joined);

                    if (! joined) {
                        return 1;
                    }

                    const ageInDays = Math.max(0, (Date.now() / 1000 - joined) / 86400);

                    return 1 + 7 * Math.exp(-ageInDays / 30);
                };

                const weightedShuffle = (cards) => cards
                    .map((card) => ({ card, key: -Math.log(Math.random() || Number.MIN_VALUE) / memberWeight(card) }))
                    .sort((a, b) => a.key - b.key)
                    .map(({ card }) => card);

                const liveCards = () => wall
                    ? [...wall.querySelectorAll('[data-member-card]:not(.is-broken)')]
                    : [];

                /* ---------- Toast ---------- */
                let toastTimer;
                const showToast = (message, action = null, duration = 7000) => {
                    const actionButton = toast.querySelector('[data-toast-action]');

                    toast.querySelector('[data-toast-message]').textContent = message;
                    actionButton.classList.toggle('hidden', ! action);
                    actionButton.onclick = null;

                    if (action) {
                        actionButton.textContent = action.label;
                        actionButton.onclick = () => {
                            hideToast();
                            action.run();
                        };
                    }

                    toast.classList.add('is-open');
                    clearTimeout(toastTimer);
                    toastTimer = setTimeout(hideToast, duration);
                };
                const hideToast = () => toast.classList.remove('is-open');

                toast.querySelector('[data-toast-close]').addEventListener('click', hideToast);

                /* ---------- Card reveal ---------- */
                const closeCard = (card) => {
                    card.classList.remove('is-revealed', 'is-spotlit');
                    card.querySelector('[data-member-overlay]')?.classList.replace('opacity-100', 'opacity-0');
                    card.querySelector('[data-member-details]')?.classList.replace('translate-y-0', 'translate-y-full');
                };

                const closeAllCards = () => document.querySelectorAll('[data-member-card]').forEach(closeCard);

                const openCard = (card) => {
                    card.classList.add('is-revealed');
                    card.querySelector('[data-member-overlay]')?.classList.replace('opacity-0', 'opacity-100');
                    card.querySelector('[data-member-details]')?.classList.replace('translate-y-full', 'translate-y-0');
                };

                wall?.addEventListener('click', (event) => {
                    const button = event.target.closest('[data-member-card] button');

                    if (! button || ! wall.contains(button)) {
                        return;
                    }

                    const card = button.closest('[data-member-card]');
                    const isOpen = card.classList.contains('is-revealed');

                    closeAllCards();

                    if (! isOpen) {
                        openCard(card);
                    }
                });

                hideNamesButton?.addEventListener('click', closeAllCards);

                /* ---------- Entrance animation ---------- */
                const entranceObserver = 'IntersectionObserver' in window && ! reduceMotion
                    ? new IntersectionObserver((entries) => {
                        entries
                            .filter((entry) => entry.isIntersecting)
                            .forEach((entry, index) => {
                                entry.target.style.setProperty('--delay', `${Math.min(index, 12) * 45}ms`);
                                entry.target.classList.add('is-in');
                                entranceObserver.unobserve(entry.target);
                            });
                    }, { rootMargin: '0px 0px -40px 0px' })
                    : null;

                const prepareCards = (cards) => {
                    if (! entranceObserver) {
                        return;
                    }

                    cards.forEach((card) => entranceObserver.observe(card));
                };

                if (wall && entranceObserver) {
                    wall.classList.add('wall-animate');
                    prepareCards(liveCards());
                }

                /* ---------- Shuffle (FLIP) ---------- */
                shuffleButton?.addEventListener('click', () => {
                    if (! wall) {
                        return;
                    }

                    closeAllCards();

                    const cards = [...wall.children];
                    const before = new Map(cards.map((card) => [card, card.getBoundingClientRect()]));

                    weightedShuffle(cards).forEach((card) => wall.appendChild(card));

                    if (reduceMotion) {
                        return;
                    }

                    cards.forEach((card) => {
                        const from = before.get(card);
                        const to = card.getBoundingClientRect();
                        const dx = from.left - to.left;
                        const dy = from.top - to.top;

                        if ((! dx && ! dy) || (to.bottom < -200 && from.bottom < -200) || (to.top > innerHeight + 200 && from.top > innerHeight + 200)) {
                            return;
                        }

                        card.animate([
                            { transform: `translate(${dx}px, ${dy}px) scale(0.9) rotate(${(Math.random() - 0.5) * 12}deg)` },
                            { transform: 'translate(0, 0) scale(1) rotate(0deg)' },
                        ], {
                            duration: 550 + Math.random() * 250,
                            easing: 'cubic-bezier(0.2, 0.8, 0.2, 1)',
                        });
                    });
                });

                /* ---------- New faces filter ---------- */
                const setNewFaces = (enabled) => {
                    wall?.classList.toggle('wall-new-only', enabled);
                    newFacesButton?.setAttribute('aria-pressed', enabled ? 'true' : 'false');
                };

                newFacesButton?.addEventListener('click', () => {
                    setNewFaces(! wall?.classList.contains('wall-new-only'));
                });

                /* ---------- Confetti ---------- */
                const burstConfetti = (element) => {
                    if (reduceMotion) {
                        return;
                    }

                    const rect = element.getBoundingClientRect();
                    const colors = ['#F5B400', '#1FA774', '#0A2A6B', '#ffffff', '#ffd15c'];

                    for (let i = 0; i < 36; i++) {
                        const piece = document.createElement('span');
                        const angle = Math.random() * Math.PI * 2;
                        const distance = 90 + Math.random() * 160;

                        piece.className = 'confetti-piece';
                        piece.style.left = `${rect.left + rect.width / 2}px`;
                        piece.style.top = `${rect.top + rect.height / 2}px`;
                        piece.style.background = colors[i % colors.length];
                        piece.style.setProperty('--dx', `${Math.cos(angle) * distance}px`);
                        piece.style.setProperty('--dy', `${Math.sin(angle) * distance - 60}px`);
                        piece.style.setProperty('--rot', `${Math.random() * 720 - 360}deg`);
                        document.body.appendChild(piece);
                        piece.addEventListener('animationend', () => piece.remove());
                    }
                };

                /* ---------- Spotlight roulette ---------- */
                const isOnScreen = (card) => {
                    const rect = card.getBoundingClientRect();

                    return rect.bottom > 0 && rect.top < innerHeight && rect.width > 0;
                };

                spotlightButton?.addEventListener('click', async () => {
                    const cards = liveCards();

                    if (! cards.length) {
                        return;
                    }

                    spotlightButton.disabled = true;
                    closeAllCards();
                    setNewFaces(false);

                    const winner = weightedShuffle(cards)[0];

                    winner.classList.add('is-in');
                    winner.scrollIntoView({ behavior: reduceMotion ? 'auto' : 'smooth', block: 'center' });

                    if (! reduceMotion) {
                        await wait(650);

                        const visible = liveCards().filter((card) => card !== winner && isOnScreen(card));
                        const hops = Math.min(16, visible.length);

                        for (let i = 0; i < hops; i++) {
                            const card = visible[Math.floor(Math.random() * visible.length)];

                            card.classList.add('is-hopping');
                            await wait(55 + i * i * 1.4);
                            card.classList.remove('is-hopping');
                        }
                    }

                    openCard(winner);
                    winner.classList.add('is-spotlit');
                    burstConfetti(winner);

                    const name = winner.querySelector('[data-member-name]')?.textContent.trim();
                    const level = winner.querySelector('[data-member-level]')?.textContent.trim();

                    showToast(winner.dataset.new === '1'
                        ? `Say hello to ${name}! Fresh face on the wall.`
                        : `Meet ${name}, ${level}.`, null, 4500);

                    spotlightButton.disabled = false;
                });

                /* ---------- Hero: portraits swap with faces from the wall ---------- */
                const previewTiles = [...document.querySelectorAll('[data-preview-tile]')];

                const cyclePreview = () => {
                    if (document.hidden || ! previewTiles.length) {
                        return;
                    }

                    const shownSources = new Set(previewTiles.map((tile) => tile.querySelector('img').src));
                    const pool = weightedShuffle(liveCards())
                        .map((card) => card.querySelector('img'))
                        .filter((img) => img.complete && img.naturalWidth > 0 && ! shownSources.has(img.src));

                    if (! pool.length) {
                        return;
                    }

                    const tile = previewTiles.find((t) => t.querySelector('img').dataset.broken)
                        ?? previewTiles[Math.floor(Math.random() * previewTiles.length)];
                    const image = tile.querySelector('img');
                    const nextSource = pool[0].src;

                    if (reduceMotion) {
                        image.src = nextSource;
                        delete image.dataset.broken;

                        return;
                    }

                    tile.classList.add('is-flipping');
                    setTimeout(() => {
                        image.src = nextSource;
                        delete image.dataset.broken;
                        tile.classList.remove('is-flipping');
                    }, 350);
                };

                setTimeout(cyclePreview, 1200);
                setInterval(cyclePreview, reduceMotion ? 8000 : 3200);

                /* ---------- Hero counters ---------- */
                document.querySelectorAll('[data-count-up]').forEach((element) => {
                    const target = Number(element.dataset.countUp);

                    if (reduceMotion || ! target) {
                        return;
                    }

                    const started = performance.now();
                    const duration = 1400;
                    const tick = (now) => {
                        const progress = Math.min(1, (now - started) / duration);
                        const eased = 1 - Math.pow(1 - progress, 3);

                        element.textContent = Math.round(target * eased).toLocaleString();

                        if (progress < 1) {
                            requestAnimationFrame(tick);
                        }
                    };

                    element.textContent = '0';
                    requestAnimationFrame(tick);
                });

                /* ---------- Welcome back ---------- */
                const lastVisitKey = 'nabams:member-wall:last-visit';
                let lastVisit = null;

                try {
                    lastVisit = Number(localStorage.getItem(lastVisitKey)) || null;
                    localStorage.setItem(lastVisitKey, String(Math.floor(Date.now() / 1000)));
                } catch (error) {
                    // Storage can be blocked; the wall still works without it.
                }

                const showNewFaces = () => {
                    setNewFaces(true);
                    document.getElementById('member-wall-section')?.scrollIntoView({ behavior: 'smooth' });
                };
                const sinceLastVisit = lastVisit ? newMemberJoinTimes.filter((joined) => joined > lastVisit).length : 0;

                if (sinceLastVisit > 0) {
                    setTimeout(() => showToast(
                        `Welcome back! ${sinceLastVisit} new ${sinceLastVisit === 1 ? 'face has' : 'faces have'} joined since your last visit.`,
                        newFacesButton ? { label: 'Show me', run: showNewFaces } : null,
                    ), 1500);
                } else if (! lastVisit && newMemberJoinTimes.length > 0) {
                    setTimeout(() => showToast(
                        `${newMemberJoinTimes.length} new ${newMemberJoinTimes.length === 1 ? 'face' : 'faces'} joined this month. Say hi!`,
                        newFacesButton ? { label: 'Show me', run: showNewFaces } : null,
                    ), 1500);
                }

                /* ---------- Load more ---------- */
                loadMoreButton?.addEventListener('click', async () => {
                    const nextPageUrl = loadMoreButton.dataset.nextPageUrl;

                    if (! wall || ! nextPageUrl) {
                        return;
                    }

                    loadMoreButton.disabled = true;
                    loadMoreButton.textContent = 'Loading...';

                    try {
                        const response = await fetch(nextPageUrl, {
                            headers: {
                                'Accept': 'application/json',
                                'X-Requested-With': 'XMLHttpRequest',
                            },
                        });
                        const payload = await response.json();

                        if (! response.ok) {
                            throw new Error('Unable to load more members.');
                        }

                        const countBefore = wall.children.length;

                        wall.insertAdjacentHTML('beforeend', payload.html || '');
                        prepareCards([...wall.children].slice(countBefore));

                        if (payload.next_page_url) {
                            loadMoreButton.dataset.nextPageUrl = payload.next_page_url;
                            loadMoreButton.disabled = false;
                            loadMoreButton.textContent = 'Load More Members';
                        } else {
                            loadMoreButton.remove();
                        }
                    } catch (error) {
                        loadMoreButton.disabled = false;
                        loadMoreButton.textContent = 'Try Again';
                    }
                });
            });
        </script>
    </body>
</html>
