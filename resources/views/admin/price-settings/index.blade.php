@extends('layouts.dashboard', ['pageTitle' => 'Price Settings'])

@section('content')
    <section class="rounded-lg bg-[#0A2A6B] p-6 text-white shadow-xl sm:p-8">
        <div class="flex flex-col gap-5 lg:flex-row lg:items-end lg:justify-between">
            <div>
                <p class="text-sm font-black uppercase tracking-wide text-[#F5B400]">Price Settings</p>
                <h1 class="mt-3 text-3xl font-black leading-tight sm:text-4xl">Manage NABAMS fees and prices</h1>
                <p class="mt-4 max-w-3xl text-sm leading-7 text-[#F2F2F2]/80">
                    Set prices for each level by academic session and semester. Members are charged based on these settings.
                </p>
            </div>
            <a href="{{ route('admin.price-settings.create') }}" class="inline-flex justify-center rounded-lg bg-[#F5B400] px-5 py-3 text-sm font-black text-[#0A2A6B] transition hover:bg-[#ffd15c]">New Price</a>
        </div>
    </section>

    @if (session('success'))
        <div class="mt-6 rounded-lg border border-[#1FA774]/20 bg-[#1FA774]/10 px-5 py-4 text-sm font-bold text-[#0A2A6B]">
            {{ session('success') }}
        </div>
    @endif

    @if (session('error'))
        <div class="mt-6 rounded-lg border border-[#F5B400]/40 bg-[#F5B400]/15 px-5 py-4 text-sm font-bold text-[#0A2A6B]">
            {{ session('error') }}
        </div>
    @endif

    @if ($levelsWithoutPrice->isNotEmpty())
        <div class="mt-6 rounded-lg border border-[#F5B400]/40 bg-[#F5B400]/15 px-5 py-4 text-sm text-[#0A2A6B]">
            <p class="font-black">No active price for the current period: {{ $levelsWithoutPrice->implode(', ') }}</p>
            <p class="mt-1">Members in these levels can use the dashboard without paying. Add a price if they should pay.</p>
        </div>
    @endif

    <section class="mt-6 grid gap-5 md:grid-cols-3">
        <div class="rounded-lg bg-white p-6 shadow-sm ring-1 ring-[#0A2A6B]/10">
            <p class="text-sm font-black uppercase tracking-wide text-[#F5B400]">Matching Prices</p>
            <p class="mt-3 text-3xl font-black text-[#0A2A6B]">{{ $priceSettings->total() }}</p>
        </div>
        <div class="rounded-lg bg-white p-6 shadow-sm ring-1 ring-[#0A2A6B]/10">
            <p class="text-sm font-black uppercase tracking-wide text-[#F5B400]">Active Prices</p>
            <p class="mt-3 text-3xl font-black text-[#1FA774]">{{ $activeTotal }}</p>
        </div>
        <div class="rounded-lg bg-white p-6 shadow-sm ring-1 ring-[#0A2A6B]/10">
            <p class="text-sm font-black uppercase tracking-wide text-[#F5B400]">Current Session</p>
            <p class="mt-3 text-3xl font-black text-[#0A2A6B]">{{ $currentSession?->name ?? 'Not set' }}</p>
        </div>
    </section>

    @php
        $filterClass = 'rounded-lg border border-[#0A2A6B]/15 bg-white px-4 py-3 text-sm font-normal text-[#2E2E2E] outline-none transition focus:border-[#F5B400] focus:ring-4 focus:ring-[#F5B400]/20';
    @endphp

    <form method="GET" action="{{ route('admin.price-settings.index') }}" class="mt-6 grid gap-4 rounded-lg bg-white p-5 shadow-sm ring-1 ring-[#0A2A6B]/10 sm:grid-cols-2 lg:grid-cols-4">
        <label class="grid gap-2 text-sm font-bold text-[#0A2A6B]">
            Session
            <select name="academic_session_id" class="{{ $filterClass }}">
                <option value="">All sessions</option>
                @foreach ($academicSessions as $session)
                    <option value="{{ $session->id }}" @selected((string) ($filters['academic_session_id'] ?? '') === (string) $session->id)>{{ $session->name }}</option>
                @endforeach
            </select>
        </label>
        <label class="grid gap-2 text-sm font-bold text-[#0A2A6B]">
            Level
            <select name="level_id" class="{{ $filterClass }}">
                <option value="">All levels</option>
                @foreach ($levels as $level)
                    <option value="{{ $level->id }}" @selected((string) ($filters['level_id'] ?? '') === (string) $level->id)>{{ $level->name }}</option>
                @endforeach
            </select>
        </label>
        <label class="grid gap-2 text-sm font-bold text-[#0A2A6B]">
            Semester
            <select name="semester" class="{{ $filterClass }}">
                <option value="">All semesters</option>
                @foreach ($semesters as $semester)
                    <option value="{{ $semester }}" @selected(($filters['semester'] ?? '') === $semester)>{{ $semester }} Semester</option>
                @endforeach
            </select>
        </label>
        <div class="flex items-end gap-2">
            <button type="submit" class="flex-1 rounded-lg bg-[#0A2A6B] px-4 py-3 text-sm font-black text-white transition hover:bg-[#0d358a]">Filter</button>
            <a href="{{ route('admin.price-settings.index') }}" class="rounded-lg border border-[#0A2A6B]/20 px-4 py-3 text-sm font-black text-[#0A2A6B] transition hover:bg-[#F2F2F2]">Reset</a>
        </div>
    </form>

    <section class="mt-6 overflow-hidden rounded-lg bg-white shadow-sm ring-1 ring-[#0A2A6B]/10">
        <div class="border-b border-[#0A2A6B]/10 px-5 py-4">
            <h2 class="text-xl font-black text-[#0A2A6B]">Prices</h2>
        </div>

        <div class="hidden overflow-x-auto lg:block">
            <table class="min-w-full divide-y divide-[#0A2A6B]/10">
                <thead class="bg-[#F2F2F2]">
                    <tr>
                        <th class="px-5 py-3 text-left text-xs font-black uppercase tracking-wide text-[#2E2E2E]/65">Name</th>
                        <th class="px-5 py-3 text-left text-xs font-black uppercase tracking-wide text-[#2E2E2E]/65">Session</th>
                        <th class="px-5 py-3 text-left text-xs font-black uppercase tracking-wide text-[#2E2E2E]/65">Level</th>
                        <th class="px-5 py-3 text-left text-xs font-black uppercase tracking-wide text-[#2E2E2E]/65">Semester</th>
                        <th class="px-5 py-3 text-right text-xs font-black uppercase tracking-wide text-[#2E2E2E]/65">Amount</th>
                        <th class="px-5 py-3 text-left text-xs font-black uppercase tracking-wide text-[#2E2E2E]/65">Status</th>
                        <th class="px-5 py-3 text-right text-xs font-black uppercase tracking-wide text-[#2E2E2E]/65">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-[#0A2A6B]/10">
                    @forelse ($priceSettings as $price)
                        <tr>
                            <td class="px-5 py-4">
                                <p class="font-black text-[#0A2A6B]">{{ $price->name }}</p>
                                @if ($price->updatedBy)
                                    <p class="mt-1 text-xs font-semibold text-[#2E2E2E]/55">Updated by {{ $price->updatedBy->name ?: $price->updatedBy->firstname }}</p>
                                @endif
                            </td>
                            <td class="px-5 py-4 text-sm font-semibold text-[#2E2E2E]/75">{{ $price->academicSession?->name }}</td>
                            <td class="px-5 py-4 text-sm font-semibold text-[#2E2E2E]/75">{{ $price->level?->name }}</td>
                            <td class="px-5 py-4 text-sm font-semibold text-[#2E2E2E]/75">{{ $price->semester }}</td>
                            <td class="px-5 py-4 text-right font-black text-[#0A2A6B]">&#8358;{{ number_format($price->amount) }}</td>
                            <td class="px-5 py-4">
                                <span class="rounded-full px-3 py-1 text-xs font-black {{ $price->is_active === 'Yes' ? 'bg-[#1FA774]/10 text-[#1FA774]' : 'bg-[#F5B400]/20 text-[#0A2A6B]' }}">{{ $price->is_active === 'Yes' ? 'Active' : 'Inactive' }}</span>
                            </td>
                            <td class="px-5 py-4">
                                <div class="flex justify-end gap-2">
                                    <a href="{{ route('admin.price-settings.edit', $price) }}" class="rounded-lg border border-[#0A2A6B]/20 px-3 py-2 text-xs font-black text-[#0A2A6B] transition hover:bg-[#0A2A6B] hover:text-white">Edit</a>
                                    <form action="{{ route('admin.price-settings.destroy', $price) }}" method="POST" onsubmit="return confirm('Delete this price setting?');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="rounded-lg border border-[#F5B400]/50 px-3 py-2 text-xs font-black text-[#0A2A6B] transition hover:bg-[#F5B400]">Delete</button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="px-5 py-10 text-center text-sm font-semibold text-[#2E2E2E]/65">No price setting found.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="grid gap-4 p-4 lg:hidden">
            @forelse ($priceSettings as $price)
                <article class="rounded-lg border border-[#0A2A6B]/10 bg-[#F2F2F2] p-4">
                    <div class="flex items-start justify-between gap-4">
                        <div>
                            <h3 class="text-lg font-black text-[#0A2A6B]">{{ $price->name }}</h3>
                            <p class="mt-1 text-sm font-semibold text-[#2E2E2E]/65">{{ $price->academicSession?->name }} &middot; {{ $price->level?->name }} &middot; {{ $price->semester }} Semester</p>
                        </div>
                        <span class="rounded-full px-3 py-1 text-xs font-black {{ $price->is_active === 'Yes' ? 'bg-[#1FA774]/10 text-[#1FA774]' : 'bg-white text-[#2E2E2E]/70' }}">{{ $price->is_active === 'Yes' ? 'Active' : 'Inactive' }}</span>
                    </div>
                    <p class="mt-3 text-2xl font-black text-[#0A2A6B]">&#8358;{{ number_format($price->amount) }}</p>
                    <div class="mt-4 flex flex-wrap gap-2">
                        <a href="{{ route('admin.price-settings.edit', $price) }}" class="rounded-lg bg-[#0A2A6B] px-3 py-2 text-xs font-black text-white">Edit</a>
                        <form action="{{ route('admin.price-settings.destroy', $price) }}" method="POST" onsubmit="return confirm('Delete this price setting?');">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="rounded-lg bg-[#F5B400] px-3 py-2 text-xs font-black text-[#0A2A6B]">Delete</button>
                        </form>
                    </div>
                </article>
            @empty
                <p class="rounded-lg bg-[#F2F2F2] p-6 text-center text-sm font-semibold text-[#2E2E2E]/65">No price setting found.</p>
            @endforelse
        </div>

        @if ($priceSettings->hasPages())
            <div class="border-t border-[#0A2A6B]/10 px-5 py-4">
                {{ $priceSettings->links() }}
            </div>
        @endif
    </section>
@endsection
