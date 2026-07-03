@extends('layouts.dashboard', ['pageTitle' => 'Election'])

@section('content')
    <section class="rounded-lg bg-[#0A2A6B] p-6 text-white shadow-xl sm:p-8">
        <p class="text-sm font-black uppercase tracking-wide text-[#F5B400]">NABAMS Election</p>
        <h1 class="mt-3 text-3xl font-black sm:text-4xl">Cast your vote</h1>
        <p class="mt-4 max-w-3xl text-sm leading-7 text-[#F2F2F2]/80">
            You can vote once per position for the current academic session. Choose carefully; submitted votes cannot be changed here.
        </p>
    </section>

    @if (session('success'))
        <div class="mt-6 rounded-lg border border-[#1FA774]/20 bg-[#1FA774]/10 px-5 py-4 text-sm font-bold text-[#0A2A6B]">{{ session('success') }}</div>
    @endif

    @if (session('error'))
        <div class="mt-6 rounded-lg border border-[#F5B400]/40 bg-[#F5B400]/15 px-5 py-4 text-sm font-bold text-[#0A2A6B]">{{ session('error') }}</div>
    @endif

    @if (! $electionEnabled)
        <section class="mt-6 rounded-lg bg-white p-8 text-center shadow-sm ring-1 ring-[#0A2A6B]/10">
            <p class="text-sm font-black uppercase tracking-wide text-[#F5B400]">Election Disabled</p>
            <h2 class="mt-3 text-2xl font-black text-[#0A2A6B]">Voting is currently closed</h2>
            <p class="mt-3 text-sm leading-7 text-[#2E2E2E]/70">Please check back later when the association enables election voting.</p>
        </section>
    @elseif (! $currentSession)
        <p class="mt-6 rounded-lg bg-white p-8 text-center text-sm font-semibold text-[#2E2E2E]/65 shadow-sm ring-1 ring-[#0A2A6B]/10">No current academic session is available for voting.</p>
    @else
        <section class="mt-6 grid gap-5 lg:grid-cols-2">
            @forelse ($positions as $position)
                @php
                    $hasVoted = in_array($position->id, $votedPositionIds, true);
                @endphp
                <article class="rounded-lg bg-white p-6 shadow-sm ring-1 ring-[#0A2A6B]/10">
                    <div class="flex items-start justify-between gap-4">
                        <div>
                            <h2 class="text-xl font-black text-[#0A2A6B]">{{ $position->name }}</h2>
                            <p class="mt-1 text-sm font-semibold text-[#2E2E2E]/65">{{ $currentSession->name }}</p>
                        </div>
                        <span class="rounded-lg px-3 py-2 text-xs font-black {{ $hasVoted ? 'bg-[#1FA774]/10 text-[#1FA774]' : 'bg-[#F5B400]/20 text-[#0A2A6B]' }}">{{ $hasVoted ? 'Voted' : 'Open' }}</span>
                    </div>

                    <div class="mt-5 grid gap-3">
                        @forelse ($position->aspirants as $aspirant)
                            <form action="{{ route('election.vote') }}" method="POST" class="rounded-lg border border-[#0A2A6B]/10 bg-[#F2F2F2] p-4">
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
                                    <button type="submit" @disabled($hasVoted) class="rounded-lg px-4 py-2 text-xs font-black {{ $hasVoted ? 'bg-[#2E2E2E]/20 text-[#2E2E2E]/50' : 'bg-[#1FA774] text-white hover:bg-[#198b61]' }}">Vote</button>
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
@endsection
