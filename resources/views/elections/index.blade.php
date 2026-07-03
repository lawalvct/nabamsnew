@extends('layouts.dashboard', ['pageTitle' => 'Election'])

@section('content')
    @php
        $totalPositions = $positions->count();
        $completedVotes = count($votedPositionIds);
    @endphp

    <style>
        #election-voting-shell:fullscreen {
            background: #F2F2F2;
            overflow-y: auto;
            padding: 1rem;
        }

        #election-voting-shell:-webkit-full-screen {
            background: #F2F2F2;
            overflow-y: auto;
            padding: 1rem;
        }

        .vote-position-card {
            transition: opacity 300ms ease, transform 300ms ease, max-height 300ms ease, margin 300ms ease, padding 300ms ease;
        }

        .vote-position-card.is-leaving {
            max-height: 0;
            margin: 0;
            opacity: 0;
            overflow: hidden;
            padding-bottom: 0;
            padding-top: 0;
            transform: translateY(8px);
        }
    </style>

    <div id="election-voting-shell" class="min-h-full">
        <section class="rounded-lg bg-[#0A2A6B] p-6 text-white shadow-xl sm:p-8">
            <div class="flex flex-col gap-5 lg:flex-row lg:items-end lg:justify-between">
                <div>
                    <p class="text-sm font-black uppercase tracking-wide text-[#F5B400]">NABAMS Election</p>
                    <h1 class="mt-3 text-3xl font-black sm:text-4xl">Cast your vote</h1>
                    <p class="mt-4 max-w-3xl text-sm leading-7 text-[#F2F2F2]/80">
                        You can vote once per position for the current academic session. Choose carefully; submitted votes cannot be changed here.
                    </p>
                </div>

                @if ($electionEnabled && $currentSession)
                    <button type="button" id="election-fullscreen-button" class="inline-flex items-center justify-center gap-2 rounded-lg bg-[#F5B400] px-5 py-3 text-sm font-black text-[#0A2A6B] transition hover:bg-[#ffd15c]">
                        <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true">
                            <path d="M8 3H5a2 2 0 0 0-2 2v3"></path>
                            <path d="M16 3h3a2 2 0 0 1 2 2v3"></path>
                            <path d="M8 21H5a2 2 0 0 1-2-2v-3"></path>
                            <path d="M16 21h3a2 2 0 0 0 2-2v-3"></path>
                        </svg>
                        <span id="election-fullscreen-label">Full Screen</span>
                    </button>
                @endif
            </div>
        </section>

        <div id="vote-alert" class="mt-6 hidden rounded-lg px-5 py-4 text-sm font-bold"></div>

        @if (session('success'))
            <div class="mt-6 rounded-lg border border-[#1FA774]/20 bg-[#1FA774]/10 px-5 py-4 text-sm font-bold text-[#0A2A6B]">{{ session('success') }}</div>
        @endif

        @if (session('error'))
            <div class="mt-6 rounded-lg border border-[#F5B400]/40 bg-[#F5B400]/15 px-5 py-4 text-sm font-bold text-[#0A2A6B]">{{ session('error') }}</div>
        @endif

        @if (! $electionEnabled)
            <section class="mt-6 rounded-lg bg-white p-8 text-center shadow-sm ring-1 ring-[#0A2A6B]/10">
                <p class="text-sm font-black uppercase tracking-wide text-[#F5B400]">Election Closed</p>
                <h2 class="mt-3 text-2xl font-black text-[#0A2A6B]">Voting is currently closed</h2>
                <p class="mx-auto mt-3 max-w-2xl text-sm leading-7 text-[#2E2E2E]/70">
                    The admin has closed election voting. Your vote record is now available below for confirmation.
                </p>
            </section>

            <section class="mt-6 rounded-lg bg-white p-6 shadow-sm ring-1 ring-[#0A2A6B]/10">
                <div class="flex flex-col gap-4 sm:flex-row sm:items-end sm:justify-between">
                    <div>
                        <p class="text-sm font-black uppercase tracking-wide text-[#F5B400]">My Votes</p>
                        <h2 class="mt-2 text-2xl font-black text-[#0A2A6B]">You voted in {{ $myVotes->count() }} {{ \Illuminate\Support\Str::plural('position', $myVotes->count()) }}</h2>
                    </div>
                    @if ($currentSession)
                        <span class="rounded-lg bg-[#0A2A6B]/10 px-4 py-2 text-sm font-black text-[#0A2A6B]">{{ $currentSession->name }}</span>
                    @endif
                </div>

                <div class="mt-6 grid gap-4 md:grid-cols-2">
                    @forelse ($myVotes as $vote)
                        <article class="rounded-lg border border-[#0A2A6B]/10 bg-[#F2F2F2] p-4">
                            <p class="text-sm font-bold text-[#2E2E2E]/60">{{ $vote->position?->name ?? 'Position removed' }}</p>
                            <p class="mt-2 text-lg font-black text-[#0A2A6B]">{{ $vote->aspirant?->name ?? 'Aspirant removed' }}</p>
                            <p class="mt-2 text-xs font-bold text-[#1FA774]">Recorded {{ $vote->created_at?->format('M d, Y h:i A') }}</p>
                        </article>
                    @empty
                        <p class="rounded-lg bg-[#F2F2F2] p-5 text-sm font-semibold text-[#2E2E2E]/65 md:col-span-2">No vote record was found for your account in this session.</p>
                    @endforelse
                </div>
            </section>
        @elseif (! $currentSession)
            <p class="mt-6 rounded-lg bg-white p-8 text-center text-sm font-semibold text-[#2E2E2E]/65 shadow-sm ring-1 ring-[#0A2A6B]/10">No current academic session is available for voting.</p>
        @else
            <section class="mt-6 grid gap-5 lg:grid-cols-[1fr_320px]">
                <div class="rounded-lg bg-white p-5 shadow-sm ring-1 ring-[#0A2A6B]/10">
                    <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
                        <div>
                            <p class="text-sm font-black uppercase tracking-wide text-[#F5B400]">Voting Progress</p>
                            <h2 class="mt-2 text-2xl font-black text-[#0A2A6B]">
                                <span id="completed-vote-count">{{ $completedVotes }}</span> of <span id="total-position-count">{{ $totalPositions }}</span> positions completed
                            </h2>
                        </div>
                        <span class="rounded-lg bg-[#1FA774]/10 px-4 py-2 text-sm font-black text-[#1FA774]">{{ $currentSession->name }}</span>
                    </div>
                    <p class="mt-4 text-sm leading-6 text-[#2E2E2E]/65">
                        Your selected candidates will be available for viewing after the admin closes election voting.
                    </p>
                </div>

                <div class="rounded-lg bg-[#0A2A6B] p-5 text-white shadow-sm">
                    <p class="text-sm font-black uppercase tracking-wide text-[#F5B400]">Voting Notice</p>
                    <p class="mt-3 text-sm leading-7 text-[#F2F2F2]/80">
                        After you vote for a position, all buttons for that position will lock immediately and the position will leave the list after a few seconds.
                    </p>
                </div>
            </section>

            <section id="vote-positions-list" class="mt-6 grid gap-5 lg:grid-cols-2">
                @forelse ($positions as $position)
                    @php
                        $hasVoted = in_array($position->id, $votedPositionIds, true);
                    @endphp
                    <article class="vote-position-card rounded-lg bg-white p-6 shadow-sm ring-1 ring-[#0A2A6B]/10" data-position-card="{{ $position->id }}">
                        <div class="flex items-start justify-between gap-4">
                            <div>
                                <h2 class="text-xl font-black text-[#0A2A6B]">{{ $position->name }}</h2>
                                <p class="mt-1 text-sm font-semibold text-[#2E2E2E]/65">{{ $currentSession->name }}</p>
                            </div>
                            <span data-position-status="{{ $position->id }}" class="rounded-lg px-3 py-2 text-xs font-black {{ $hasVoted ? 'bg-[#1FA774]/10 text-[#1FA774]' : 'bg-[#F5B400]/20 text-[#0A2A6B]' }}">{{ $hasVoted ? 'Voted' : 'Open' }}</span>
                        </div>

                        <div class="mt-5 grid gap-3">
                            @forelse ($position->aspirants as $aspirant)
                                <form action="{{ route('election.vote') }}" method="POST" data-vote-form data-position-id="{{ $position->id }}" class="rounded-lg border border-[#0A2A6B]/10 bg-[#F2F2F2] p-4">
                                    @csrf
                                    <input type="hidden" name="position_id" value="{{ $position->id }}">
                                    <input type="hidden" name="aspirant_id" value="{{ $aspirant->id }}">
                                    @php
                                        $aspirantInitials = collect(explode(' ', trim($aspirant->name)))
                                            ->filter()
                                            ->take(2)
                                            ->map(fn ($name) => strtoupper(substr($name, 0, 1)))
                                            ->implode('');
                                    @endphp
                                    <div class="flex items-center justify-between gap-4">
                                        <div class="flex min-w-0 items-center gap-4">
                                            <div class="grid h-16 w-16 shrink-0 place-items-center overflow-hidden rounded-lg bg-[#0A2A6B] text-sm font-black text-white ring-2 ring-white">
                                                @if ($aspirant->photo_url)
                                                    <img src="{{ $aspirant->photo_url }}" alt="{{ $aspirant->name }}" class="h-full w-full object-cover">
                                                @else
                                                    {{ $aspirantInitials ?: 'NA' }}
                                                @endif
                                            </div>
                                            <div class="min-w-0">
                                                <p class="font-black text-[#0A2A6B]">{{ $aspirant->name }}</p>
                                                @if ($aspirant->manifesto)
                                                    <p class="mt-1 text-sm leading-6 text-[#2E2E2E]/65">{{ $aspirant->manifesto }}</p>
                                                @endif
                                            </div>
                                        </div>
                                        <button type="submit" data-vote-button @disabled($hasVoted) class="rounded-lg px-4 py-2 text-xs font-black transition {{ $hasVoted ? 'bg-[#2E2E2E]/20 text-[#2E2E2E]/50' : 'bg-[#1FA774] text-white hover:bg-[#198b61]' }}">{{ $hasVoted ? 'Voted' : 'Vote' }}</button>
                                    </div>
                                </form>
                            @empty
                                <p class="rounded-lg bg-[#F2F2F2] p-4 text-sm font-semibold text-[#2E2E2E]/65">No approved aspirant for this position.</p>
                            @endforelse
                        </div>
                    </article>
                @empty
                    <p class="rounded-lg bg-white p-8 text-center text-sm font-semibold text-[#2E2E2E]/65 shadow-sm ring-1 ring-[#0A2A6B]/10 lg:col-span-2">No active election positions are available yet.</p>
                @endforelse
            </section>
        @endif
    </div>

    @if ($electionEnabled && $currentSession)
        <script>
            document.addEventListener('DOMContentLoaded', () => {
                const shell = document.getElementById('election-voting-shell');
                const alertBox = document.getElementById('vote-alert');
                const completedCount = document.getElementById('completed-vote-count');
                const fullscreenButton = document.getElementById('election-fullscreen-button');
                const fullscreenLabel = document.getElementById('election-fullscreen-label');
                const votedPositions = new Set(@json(array_values($votedPositionIds)));

                const showAlert = (message, type = 'success') => {
                    alertBox.textContent = message;
                    alertBox.className = type === 'success'
                        ? 'mt-6 rounded-lg border border-[#1FA774]/20 bg-[#1FA774]/10 px-5 py-4 text-sm font-bold text-[#0A2A6B]'
                        : 'mt-6 rounded-lg border border-[#F5B400]/40 bg-[#F5B400]/15 px-5 py-4 text-sm font-bold text-[#0A2A6B]';
                };

                const setPositionBusy = (positionId, selectedButton = null) => {
                    document.querySelectorAll(`[data-vote-form][data-position-id="${positionId}"]`).forEach((form) => {
                        const button = form.querySelector('[data-vote-button]');
                        if (! button) {
                            return;
                        }

                        button.disabled = true;
                        button.className = 'rounded-lg bg-[#2E2E2E]/20 px-4 py-2 text-xs font-black text-[#2E2E2E]/50 transition';
                        button.textContent = button === selectedButton ? 'Saving...' : 'Please wait';
                    });
                };

                const unlockPosition = (positionId) => {
                    document.querySelectorAll(`[data-vote-form][data-position-id="${positionId}"]`).forEach((form) => {
                        const button = form.querySelector('[data-vote-button]');
                        if (! button) {
                            return;
                        }

                        button.disabled = false;
                        button.className = 'rounded-lg bg-[#1FA774] px-4 py-2 text-xs font-black text-white transition hover:bg-[#198b61]';
                        button.textContent = 'Vote';
                    });
                };

                const lockPosition = (positionId, selectedButton = null) => {
                    document.querySelectorAll(`[data-vote-form][data-position-id="${positionId}"]`).forEach((form) => {
                        const button = form.querySelector('[data-vote-button]');
                        if (! button) {
                            return;
                        }

                        button.disabled = true;
                        button.className = 'rounded-lg bg-[#2E2E2E]/20 px-4 py-2 text-xs font-black text-[#2E2E2E]/50 transition';
                        button.textContent = button === selectedButton ? 'Recorded' : 'Locked';
                    });

                    const status = document.querySelector(`[data-position-status="${positionId}"]`);
                    if (status) {
                        status.textContent = 'Voted';
                        status.className = 'rounded-lg bg-[#1FA774]/10 px-3 py-2 text-xs font-black text-[#1FA774]';
                    }
                };

                const removePositionSoon = (positionId) => {
                    const card = document.querySelector(`[data-position-card="${positionId}"]`);
                    if (! card) {
                        return;
                    }

                    setTimeout(() => {
                        card.classList.add('is-leaving');
                        setTimeout(() => card.remove(), 350);
                    }, 3000);
                };

                document.querySelectorAll('[data-vote-form]').forEach((form) => {
                    form.addEventListener('submit', async (event) => {
                        event.preventDefault();

                        const positionId = Number(form.dataset.positionId);
                        const button = form.querySelector('[data-vote-button]');

                        if (votedPositions.has(positionId)) {
                            lockPosition(positionId, button);
                            showAlert('You have already voted for this position.', 'error');
                            return;
                        }

                        setPositionBusy(positionId, button);

                        try {
                            const response = await fetch(form.action, {
                                method: 'POST',
                                body: new FormData(form),
                                headers: {
                                    'Accept': 'application/json',
                                    'X-Requested-With': 'XMLHttpRequest',
                                },
                            });

                            const payload = await response.json().catch(() => ({}));

                            if (! response.ok) {
                                if (response.status === 409) {
                                    votedPositions.add(positionId);
                                    lockPosition(positionId, button);
                                }

                                throw new Error(payload.message || 'Unable to record your vote. Please try again.');
                            }

                            votedPositions.add(positionId);
                            lockPosition(positionId, button);
                            completedCount.textContent = String(Number(completedCount.textContent || 0) + 1);
                            showAlert(payload.message || 'Your vote has been recorded successfully.');
                            removePositionSoon(positionId);
                        } catch (error) {
                            if (! votedPositions.has(positionId)) {
                                unlockPosition(positionId);
                            }

                            showAlert(error.message || 'Unable to record your vote. Please try again.', 'error');
                        }
                    });
                });

                if (fullscreenButton && shell) {
                    const updateFullscreenLabel = () => {
                        fullscreenLabel.textContent = document.fullscreenElement ? 'Exit Full Screen' : 'Full Screen';
                    };

                    fullscreenButton.addEventListener('click', async () => {
                        try {
                            if (document.fullscreenElement) {
                                await document.exitFullscreen();
                            } else {
                                await shell.requestFullscreen();
                            }
                        } catch (error) {
                            showAlert('Full-screen mode is not available in this browser.', 'error');
                        }
                    });

                    document.addEventListener('fullscreenchange', updateFullscreenLabel);
                }
            });
        </script>
    @endif
@endsection
