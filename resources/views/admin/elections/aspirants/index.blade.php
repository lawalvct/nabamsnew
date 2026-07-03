@extends('layouts.dashboard', ['pageTitle' => 'Election Aspirants'])

@section('content')
    @include('admin.elections.partials.nav')

    <section class="rounded-lg bg-[#0A2A6B] p-6 text-white shadow-xl sm:p-8">
        <div class="flex flex-col gap-5 md:flex-row md:items-end md:justify-between">
            <div>
                <p class="text-sm font-black uppercase tracking-wide text-[#F5B400]">Election Aspirants</p>
                <h1 class="mt-3 text-3xl font-black sm:text-4xl">Manage candidates and positions</h1>
                <p class="mt-4 max-w-3xl text-sm leading-7 text-[#F2F2F2]/80">Add registered members as aspirants and assign them to one or more positions in the same election session.</p>
            </div>
            <a href="{{ route('admin.election.aspirants.create') }}" class="inline-flex justify-center rounded-lg bg-[#F5B400] px-5 py-3 text-sm font-black text-[#0A2A6B] transition hover:bg-[#ffd15c]">New Aspirant</a>
        </div>
    </section>

    @if (session('success'))
        <div class="mt-6 rounded-lg border border-[#1FA774]/20 bg-[#1FA774]/10 px-5 py-4 text-sm font-bold text-[#0A2A6B]">{{ session('success') }}</div>
    @endif

    <section class="mt-6 grid gap-5 md:grid-cols-3">
        <div class="rounded-lg bg-white p-6 shadow-sm ring-1 ring-[#0A2A6B]/10">
            <p class="text-sm font-black uppercase tracking-wide text-[#F5B400]">Aspirants</p>
            <p class="mt-3 text-3xl font-black text-[#0A2A6B]">{{ $aspirants->total() }}</p>
        </div>
        <div class="rounded-lg bg-white p-6 shadow-sm ring-1 ring-[#0A2A6B]/10">
            <p class="text-sm font-black uppercase tracking-wide text-[#F5B400]">Assignment</p>
            <p class="mt-3 text-lg font-black text-[#0A2A6B]">Multiple positions allowed</p>
        </div>
        <div class="rounded-lg bg-white p-6 shadow-sm ring-1 ring-[#0A2A6B]/10">
            <p class="text-sm font-black uppercase tracking-wide text-[#F5B400]">Voting</p>
            <p class="mt-3 text-lg font-black text-[#0A2A6B]">Approved + paid aspirants only</p>
        </div>
    </section>

    <section class="mt-6 grid gap-5 md:grid-cols-2 xl:grid-cols-3">
        @forelse ($aspirants as $aspirant)
            <article class="rounded-lg bg-white p-5 shadow-sm ring-1 ring-[#0A2A6B]/10">
                <div class="flex items-start justify-between gap-4">
                    <div class="min-w-0">
                        <p class="truncate text-xl font-black text-[#0A2A6B]">{{ $aspirant->name }}</p>
                        <p class="mt-1 truncate text-sm font-semibold text-[#2E2E2E]/65">{{ $aspirant->user?->email }}</p>
                    </div>
                    <span class="rounded-full px-3 py-1 text-xs font-black {{ $aspirant->screening_status === 'approved' ? 'bg-[#1FA774]/10 text-[#1FA774]' : 'bg-[#F5B400]/20 text-[#0A2A6B]' }}">{{ ucfirst($aspirant->screening_status) }}</span>
                </div>

                <div class="mt-5 flex flex-wrap gap-2">
                    @foreach ($aspirant->positions as $position)
                        <span class="rounded-full bg-[#0A2A6B]/10 px-3 py-1 text-xs font-black text-[#0A2A6B]">{{ $position->name }} · {{ ucfirst($position->pivot->payment_status) }}</span>
                    @endforeach
                </div>

                <div class="mt-5 flex items-center justify-between gap-4 border-t border-[#0A2A6B]/10 pt-4">
                    <p class="text-sm font-black text-[#1FA774]">{{ $aspirant->votes_count }} votes</p>
                    <div class="flex gap-2">
                        <a href="{{ route('admin.election.aspirants.edit', $aspirant) }}" class="rounded-lg bg-[#0A2A6B] px-3 py-2 text-xs font-black text-white">Edit</a>
                        <form action="{{ route('admin.election.aspirants.destroy', $aspirant) }}" method="POST" onsubmit="return confirm('Delete this aspirant?');">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="rounded-lg bg-[#F5B400] px-3 py-2 text-xs font-black text-[#0A2A6B]">Delete</button>
                        </form>
                    </div>
                </div>
            </article>
        @empty
            <p class="rounded-lg bg-white p-8 text-center text-sm font-semibold text-[#2E2E2E]/65 shadow-sm ring-1 ring-[#0A2A6B]/10 md:col-span-2 xl:col-span-3">No election aspirant yet.</p>
        @endforelse
    </section>

    @if ($aspirants->hasPages())
        <div class="mt-6 rounded-lg bg-white px-5 py-4 shadow-sm ring-1 ring-[#0A2A6B]/10">{{ $aspirants->links() }}</div>
    @endif
@endsection
