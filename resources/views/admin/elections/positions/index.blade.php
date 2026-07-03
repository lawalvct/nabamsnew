@extends('layouts.dashboard', ['pageTitle' => 'Election Positions'])

@section('content')
    @include('admin.elections.partials.nav')

    <section class="rounded-lg bg-[#0A2A6B] p-6 text-white shadow-xl sm:p-8">
        <div class="flex flex-col gap-5 md:flex-row md:items-end md:justify-between">
            <div>
                <p class="text-sm font-black uppercase tracking-wide text-[#F5B400]">Election Positions</p>
                <h1 class="mt-3 text-3xl font-black sm:text-4xl">Manage election offices</h1>
                <p class="mt-4 max-w-3xl text-sm leading-7 text-[#F2F2F2]/80">Create offices members can contest for, set form amount, order them, and attach each office to an academic session.</p>
            </div>
            <a href="{{ route('admin.election.positions.create') }}" class="inline-flex justify-center rounded-lg bg-[#F5B400] px-5 py-3 text-sm font-black text-[#0A2A6B] transition hover:bg-[#ffd15c]">New Position</a>
        </div>
    </section>

    @if (session('success'))
        <div class="mt-6 rounded-lg border border-[#1FA774]/20 bg-[#1FA774]/10 px-5 py-4 text-sm font-bold text-[#0A2A6B]">{{ session('success') }}</div>
    @endif

    <section class="mt-6 grid gap-5 md:grid-cols-3">
        <div class="rounded-lg bg-white p-6 shadow-sm ring-1 ring-[#0A2A6B]/10">
            <p class="text-sm font-black uppercase tracking-wide text-[#F5B400]">Positions</p>
            <p class="mt-3 text-3xl font-black text-[#0A2A6B]">{{ $positions->total() }}</p>
        </div>
        <div class="rounded-lg bg-white p-6 shadow-sm ring-1 ring-[#0A2A6B]/10">
            <p class="text-sm font-black uppercase tracking-wide text-[#F5B400]">Logic</p>
            <p class="mt-3 text-lg font-black text-[#0A2A6B]">One vote per member per position</p>
        </div>
        <div class="rounded-lg bg-white p-6 shadow-sm ring-1 ring-[#0A2A6B]/10">
            <p class="text-sm font-black uppercase tracking-wide text-[#F5B400]">Aspirants</p>
            <p class="mt-3 text-lg font-black text-[#0A2A6B]">Can contest multiple positions</p>
        </div>
    </section>

    <section class="mt-6 overflow-hidden rounded-lg bg-white shadow-sm ring-1 ring-[#0A2A6B]/10">
        <div class="grid gap-4 p-4 lg:hidden">
            @forelse ($positions as $position)
                <article class="rounded-lg border border-[#0A2A6B]/10 bg-[#F2F2F2] p-4">
                    <h3 class="text-lg font-black text-[#0A2A6B]">{{ $position->name }}</h3>
                    <p class="mt-1 text-sm font-semibold text-[#2E2E2E]/65">{{ $position->academicSession?->name }} · ₦{{ number_format($position->form_amount) }}</p>
                    <div class="mt-4 flex flex-wrap gap-2">
                        <a href="{{ route('admin.election.positions.edit', $position) }}" class="rounded-lg bg-[#0A2A6B] px-3 py-2 text-xs font-black text-white">Edit</a>
                    </div>
                </article>
            @empty
                <p class="rounded-lg bg-[#F2F2F2] p-6 text-center text-sm font-semibold text-[#2E2E2E]/65">No election position yet.</p>
            @endforelse
        </div>

        <div class="hidden overflow-x-auto lg:block">
            <table class="min-w-full divide-y divide-[#0A2A6B]/10">
                <thead class="bg-[#F2F2F2]">
                    <tr>
                        <th class="px-5 py-3 text-left text-xs font-black uppercase tracking-wide text-[#2E2E2E]/65">Position</th>
                        <th class="px-5 py-3 text-left text-xs font-black uppercase tracking-wide text-[#2E2E2E]/65">Session</th>
                        <th class="px-5 py-3 text-left text-xs font-black uppercase tracking-wide text-[#2E2E2E]/65">Form</th>
                        <th class="px-5 py-3 text-left text-xs font-black uppercase tracking-wide text-[#2E2E2E]/65">Aspirants</th>
                        <th class="px-5 py-3 text-left text-xs font-black uppercase tracking-wide text-[#2E2E2E]/65">Votes</th>
                        <th class="px-5 py-3 text-right text-xs font-black uppercase tracking-wide text-[#2E2E2E]/65">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-[#0A2A6B]/10">
                    @forelse ($positions as $position)
                        <tr>
                            <td class="px-5 py-4">
                                <p class="font-black text-[#0A2A6B]">{{ $position->name }}</p>
                                <p class="mt-1 text-xs font-semibold text-[#2E2E2E]/60">Order {{ $position->sort_order }} · {{ $position->is_active }}</p>
                            </td>
                            <td class="px-5 py-4 text-sm font-semibold text-[#2E2E2E]/75">{{ $position->academicSession?->name }}</td>
                            <td class="px-5 py-4 text-sm font-black text-[#0A2A6B]">₦{{ number_format($position->form_amount) }}</td>
                            <td class="px-5 py-4 text-sm font-black text-[#1FA774]">{{ $position->aspirants_count }}</td>
                            <td class="px-5 py-4 text-sm font-black text-[#0A2A6B]">{{ $position->votes_count }}</td>
                            <td class="px-5 py-4">
                                <div class="flex justify-end gap-2">
                                    <a href="{{ route('admin.election.positions.edit', $position) }}" class="rounded-lg border border-[#0A2A6B]/20 px-3 py-2 text-xs font-black text-[#0A2A6B] transition hover:bg-[#0A2A6B] hover:text-white">Edit</a>
                                    <form action="{{ route('admin.election.positions.destroy', $position) }}" method="POST" onsubmit="return confirm('Delete this position?');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="rounded-lg border border-[#F5B400]/50 px-3 py-2 text-xs font-black text-[#0A2A6B] transition hover:bg-[#F5B400]">Delete</button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-5 py-10 text-center text-sm font-semibold text-[#2E2E2E]/65">No election position yet.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if ($positions->hasPages())
            <div class="border-t border-[#0A2A6B]/10 px-5 py-4">{{ $positions->links() }}</div>
        @endif
    </section>
@endsection
