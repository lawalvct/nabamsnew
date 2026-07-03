@extends('layouts.dashboard', ['pageTitle' => 'Election Votes'])

@section('content')
    @include('admin.elections.partials.nav')

    <section class="rounded-lg bg-[#0A2A6B] p-6 text-white shadow-xl sm:p-8">
        <div class="flex flex-col gap-5 lg:flex-row lg:items-end lg:justify-between">
            <div>
                <p class="text-sm font-black uppercase tracking-wide text-[#F5B400]">Election Votes</p>
                <h1 class="mt-3 text-3xl font-black sm:text-4xl">Read member votes</h1>
                <p class="mt-4 max-w-3xl text-sm leading-7 text-[#F2F2F2]/80">Votes are counted by position. Members can vote once for each position, enforced by database constraints.</p>
            </div>
            <form method="GET" class="min-w-full sm:min-w-72">
                <select name="academic_session_id" onchange="this.form.submit()" class="w-full rounded-lg border border-white/15 bg-white px-4 py-3 text-sm font-bold text-[#0A2A6B] outline-none">
                    @foreach ($academicSessions as $session)
                        <option value="{{ $session->id }}" @selected((int) $selectedSessionId === $session->id)>{{ $session->name }}</option>
                    @endforeach
                </select>
            </form>
        </div>
    </section>

    <section class="mt-6 grid gap-5 lg:grid-cols-2">
        @forelse ($positions as $position)
            <article class="rounded-lg bg-white p-6 shadow-sm ring-1 ring-[#0A2A6B]/10">
                <div class="flex items-start justify-between gap-4">
                    <div>
                        <h2 class="text-xl font-black text-[#0A2A6B]">{{ $position->name }}</h2>
                        <p class="mt-1 text-sm font-semibold text-[#2E2E2E]/65">{{ $position->votes_count }} total votes</p>
                    </div>
                    <span class="rounded-lg bg-[#F5B400]/20 px-3 py-2 text-xs font-black text-[#0A2A6B]">{{ $position->academicSession?->name }}</span>
                </div>

                <div class="mt-5 grid gap-3">
                    @forelse ($position->aspirants->sortByDesc(fn ($aspirant) => $aspirant->votes()->where('position_id', $position->id)->count()) as $aspirant)
                        @php
                            $aspirantVoteCount = $aspirant->votes()->where('position_id', $position->id)->count();
                            $percentage = $position->votes_count > 0 ? round(($aspirantVoteCount / $position->votes_count) * 100) : 0;
                        @endphp
                        <div>
                            <div class="flex items-center justify-between gap-4 text-sm">
                                <p class="font-black text-[#0A2A6B]">{{ $aspirant->name }}</p>
                                <p class="font-black text-[#1FA774]">{{ $aspirantVoteCount }} votes</p>
                            </div>
                            <div class="mt-2 h-2 overflow-hidden rounded-full bg-[#F2F2F2]">
                                <div class="h-full rounded-full bg-[#1FA774]" style="width: {{ $percentage }}%"></div>
                            </div>
                        </div>
                    @empty
                        <p class="rounded-lg bg-[#F2F2F2] p-4 text-sm font-semibold text-[#2E2E2E]/65">No aspirants assigned to this position.</p>
                    @endforelse
                </div>
            </article>
        @empty
            <p class="rounded-lg bg-white p-8 text-center text-sm font-semibold text-[#2E2E2E]/65 shadow-sm ring-1 ring-[#0A2A6B]/10 lg:col-span-2">No election position for this session.</p>
        @endforelse
    </section>

    <section class="mt-6 overflow-hidden rounded-lg bg-white shadow-sm ring-1 ring-[#0A2A6B]/10">
        <div class="border-b border-[#0A2A6B]/10 px-5 py-4">
            <h2 class="text-xl font-black text-[#0A2A6B]">Recent Votes</h2>
        </div>
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-[#0A2A6B]/10">
                <thead class="bg-[#F2F2F2]">
                    <tr>
                        <th class="px-5 py-3 text-left text-xs font-black uppercase tracking-wide text-[#2E2E2E]/65">Member</th>
                        <th class="px-5 py-3 text-left text-xs font-black uppercase tracking-wide text-[#2E2E2E]/65">Position</th>
                        <th class="px-5 py-3 text-left text-xs font-black uppercase tracking-wide text-[#2E2E2E]/65">Aspirant</th>
                        <th class="px-5 py-3 text-left text-xs font-black uppercase tracking-wide text-[#2E2E2E]/65">IP</th>
                        <th class="px-5 py-3 text-left text-xs font-black uppercase tracking-wide text-[#2E2E2E]/65">Time</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-[#0A2A6B]/10">
                    @forelse ($recentVotes as $vote)
                        <tr>
                            <td class="px-5 py-4 text-sm font-black text-[#0A2A6B]">{{ $vote->voter?->name }}</td>
                            <td class="px-5 py-4 text-sm font-semibold text-[#2E2E2E]/75">{{ $vote->position?->name }}</td>
                            <td class="px-5 py-4 text-sm font-semibold text-[#2E2E2E]/75">{{ $vote->aspirant?->name }}</td>
                            <td class="px-5 py-4 text-sm font-semibold text-[#2E2E2E]/65">{{ $vote->ip_address ?? 'N/A' }}</td>
                            <td class="px-5 py-4 text-sm font-semibold text-[#2E2E2E]/65">{{ $vote->created_at?->format('M d, Y h:i A') }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-5 py-10 text-center text-sm font-semibold text-[#2E2E2E]/65">No votes yet.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if ($recentVotes->hasPages())
            <div class="border-t border-[#0A2A6B]/10 px-5 py-4">{{ $recentVotes->links() }}</div>
        @endif
    </section>
@endsection
