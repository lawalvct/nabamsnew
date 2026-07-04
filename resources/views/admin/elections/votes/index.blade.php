@extends('layouts.dashboard', ['pageTitle' => 'Election Votes'])

@section('content')
    <style>
        #votes-shell:fullscreen {
            background: #F2F2F2;
            overflow-y: auto;
            padding: 1.5rem;
        }
        #votes-shell:-webkit-full-screen {
            background: #F2F2F2;
            overflow-y: auto;
            padding: 1.5rem;
        }
    </style>

    <div id="votes-shell" class="min-h-full">
    @include('admin.elections.partials.nav')

    <section class="rounded-lg bg-[#0A2A6B] p-6 text-white shadow-xl sm:p-8">
        <div class="flex flex-col gap-5 lg:flex-row lg:items-end lg:justify-between">
            <div>
                <p class="text-sm font-black uppercase tracking-wide text-[#F5B400]">Election Votes</p>
                <h1 class="mt-3 text-3xl font-black sm:text-4xl">Read member votes</h1>
                <p class="mt-4 max-w-3xl text-sm leading-7 text-[#F2F2F2]/80">
                    Votes are counted by position. This page auto-refreshes quietly, so admins can monitor results without refreshing the browser.
                </p>
                <p class="mt-3 text-xs font-bold uppercase tracking-wide text-[#F2F2F2]/70">
                    Auto refresh every 10 seconds · Last update: <span id="votes-last-updated">{{ now()->format('h:i:s A') }}</span>
                </p>
                <div class="mt-4 max-w-md">
                    <div class="flex items-center gap-2 text-xs font-black uppercase tracking-wide text-[#F2F2F2]/80">
                        <span id="votes-refresh-spinner" class="hidden h-4 w-4 animate-spin rounded-full border-2 border-white/30 border-t-[#F5B400]"></span>
                        <span id="votes-refresh-dot" class="h-2.5 w-2.5 rounded-full bg-[#1FA774]"></span>
                        <span id="votes-refresh-label">Live monitor active</span>
                    </div>
                    <div class="mt-3 h-1.5 overflow-hidden rounded-full bg-white/15">
                        <div id="votes-refresh-progress" class="h-full w-0 rounded-full bg-[#F5B400] transition-all duration-500"></div>
                    </div>
                </div>
            </div>
            <div class="grid min-w-full gap-3 sm:min-w-72">
                <form method="GET">
                    <select name="academic_session_id" onchange="this.form.submit()" class="w-full rounded-lg border border-white/15 bg-white px-4 py-3 text-sm font-bold text-[#0A2A6B] outline-none">
                        @foreach ($academicSessions as $session)
                            <option value="{{ $session->id }}" @selected((int) $selectedSessionId === $session->id)>{{ $session->name }}</option>
                        @endforeach
                    </select>
                </form>
                <div class="flex flex-col gap-3 sm:flex-row sm:items-center">
                    <button type="button" id="refresh-votes" class="rounded-lg bg-white px-4 py-3 text-sm font-black text-[#0A2A6B] transition hover:bg-[#F2F2F2]">Refresh</button>
                    <a href="{{ route('admin.election.votes.pdf', ['academic_session_id' => $selectedSessionId]) }}" class="inline-flex justify-center rounded-lg bg-[#F5B400] px-4 py-3 text-sm font-black text-[#0A2A6B] transition hover:bg-[#ffd15c]">Download PDF</a>
                    <button type="button" id="fullscreen-btn" class="inline-flex items-center justify-center gap-2 rounded-lg bg-[#F5B400] px-4 py-3 text-sm font-black text-[#0A2A6B] transition hover:bg-[#ffd15c]" title="Toggle fullscreen">
                        <svg id="fullscreen-icon-expand" xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path d="M8 3H5a2 2 0 0 0-2 2v3"></path>
                            <path d="M16 3h3a2 2 0 0 1 2 2v3"></path>
                            <path d="M8 21H5a2 2 0 0 1-2-2v-3"></path>
                            <path d="M16 21h3a2 2 0 0 0 2-2v-3"></path>
                        </svg>
                        <svg id="fullscreen-icon-compress" xmlns="http://www.w3.org/2000/svg" class="hidden h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M9 9V4m0 5H4m0 0l5-5M15 9h5m-5 0V4m0 5l5-5M9 15H4m5 0v5m0-5l-5 5m11-5h5m-5 0v5m0-5l5 5" />
                        </svg>
                        <span id="fullscreen-label">Full Screen</span>
                    </button>
                </div>
            </div>
        </div>
    </section>

    <div id="votes-live-status" class="mt-6 hidden rounded-lg px-5 py-4 text-sm font-bold"></div>

    <div id="votes-live-region" data-live-url="{{ route('admin.election.votes.live', ['academic_session_id' => $selectedSessionId]) }}">
        @include('admin.elections.votes.partials.live')
    </div>

    </div>

    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const liveRegion = document.getElementById('votes-live-region');
            const refreshButton = document.getElementById('refresh-votes');
            const lastUpdated = document.getElementById('votes-last-updated');
            const statusBox = document.getElementById('votes-live-status');
            const refreshSpinner = document.getElementById('votes-refresh-spinner');
            const refreshDot = document.getElementById('votes-refresh-dot');
            const refreshLabel = document.getElementById('votes-refresh-label');
            const refreshProgress = document.getElementById('votes-refresh-progress');
            let isRefreshing = false;

            const showStatus = (message, type = 'success') => {
                statusBox.textContent = message;
                statusBox.className = type === 'success'
                    ? 'mt-6 rounded-lg border border-[#1FA774]/20 bg-[#1FA774]/10 px-5 py-4 text-sm font-bold text-[#0A2A6B]'
                    : 'mt-6 rounded-lg border border-[#F5B400]/40 bg-[#F5B400]/15 px-5 py-4 text-sm font-bold text-[#0A2A6B]';
            };

            const refreshVotes = async (manual = false) => {
                if (! liveRegion || isRefreshing || document.hidden) {
                    return;
                }

                isRefreshing = true;
                refreshSpinner?.classList.remove('hidden');
                refreshDot?.classList.add('hidden');
                refreshLabel.textContent = manual ? 'Refreshing results now...' : 'Checking latest votes...';
                refreshProgress.style.width = '35%';

                if (manual && refreshButton) {
                    refreshButton.disabled = true;
                    refreshButton.textContent = 'Refreshing...';
                }

                try {
                    const response = await fetch(liveRegion.dataset.liveUrl, {
                        headers: {
                            'Accept': 'application/json',
                            'X-Requested-With': 'XMLHttpRequest',
                        },
                    });
                    const payload = await response.json();

                    if (! response.ok) {
                        throw new Error(payload.message || 'Unable to refresh results.');
                    }

                    liveRegion.innerHTML = payload.html || '';
                    lastUpdated.textContent = payload.updated_at || new Date().toLocaleTimeString();
                    refreshProgress.style.width = '100%';

                    if (manual) {
                        showStatus('Election results refreshed successfully.');
                    }
                } catch (error) {
                    if (manual) {
                        showStatus(error.message || 'Unable to refresh results.', 'error');
                    }
                } finally {
                    isRefreshing = false;
                    setTimeout(() => {
                        refreshSpinner?.classList.add('hidden');
                        refreshDot?.classList.remove('hidden');
                        refreshLabel.textContent = 'Live monitor active';
                        refreshProgress.style.width = '0%';
                    }, 500);

                    if (manual && refreshButton) {
                        refreshButton.disabled = false;
                        refreshButton.textContent = 'Refresh';
                    }
                }
            };

            const shell = document.getElementById('votes-shell');
            const fullscreenBtn = document.getElementById('fullscreen-btn');
            const fullscreenLabel = document.getElementById('fullscreen-label');
            const iconExpand = document.getElementById('fullscreen-icon-expand');
            const iconCompress = document.getElementById('fullscreen-icon-compress');

            const updateFullscreenUI = () => {
                const isFullscreen = !!document.fullscreenElement;
                fullscreenLabel.textContent = isFullscreen ? 'Exit Full Screen' : 'Full Screen';
                iconExpand.classList.toggle('hidden', isFullscreen);
                iconCompress.classList.toggle('hidden', !isFullscreen);
            };

            fullscreenBtn?.addEventListener('click', async () => {
                try {
                    if (document.fullscreenElement) {
                        await document.exitFullscreen();
                    } else {
                        await shell.requestFullscreen();
                    }
                } catch (e) {}
            });

            document.addEventListener('fullscreenchange', updateFullscreenUI);

            refreshButton?.addEventListener('click', () => refreshVotes(true));
            setInterval(() => refreshVotes(false), 10000);
        });

        function toggleFullscreen() {}

        document.addEventListener('fullscreenchange', () => {
            const iconExpand   = document.getElementById('fullscreen-icon-expand');
            const iconCompress = document.getElementById('fullscreen-icon-compress');
            const sidebar      = document.getElementById('dashboard-sidebar');
            const wrapper      = sidebar?.parentElement;
            const isFullscreen = !!document.fullscreenElement;

            iconExpand.classList.toggle('hidden', isFullscreen);
            iconCompress.classList.toggle('hidden', !isFullscreen);

            if (sidebar) {
                sidebar.style.display = isFullscreen ? 'none' : '';
            }
            if (wrapper) {
                wrapper.style.gridTemplateColumns = isFullscreen ? '1fr' : '';
            }
        });
    </script>

@endsection
