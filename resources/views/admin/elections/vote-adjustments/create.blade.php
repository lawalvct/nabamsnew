@extends('layouts.dashboard', ['pageTitle' => 'Election Vote Adjustments'])

@section('content')
    @include('admin.elections.partials.nav')

    <section class="rounded-lg bg-[#0A2A6B] p-6 text-white shadow-xl sm:p-8">
        <div class="flex flex-col gap-5 lg:flex-row lg:items-end lg:justify-between">
            <div>
                <p class="text-sm font-black uppercase tracking-wide text-[#F5B400]">Election Audit</p>
                <h1 class="mt-3 text-3xl font-black sm:text-4xl">Vote correction adjustments</h1>
                <p class="mt-4 max-w-3xl text-sm leading-7 text-[#F2F2F2]/80">
                    This page records admin corrections separately from real member ballots. Every entry stores the admin, reason, IP address, before total, and after total.
                </p>
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

    @if (session('success'))
        <div class="mt-6 rounded-lg border border-[#1FA774]/20 bg-[#1FA774]/10 px-5 py-4 text-sm font-bold text-[#0A2A6B]">{{ session('success') }}</div>
    @endif

    @if ($errors->any())
        <div class="mt-6 rounded-lg border border-[#F5B400]/40 bg-[#F5B400]/15 px-5 py-4 text-sm font-bold text-[#0A2A6B]">
            {{ $errors->first() }}
        </div>
    @endif

    <section class="mt-6 rounded-lg border border-[#F5B400]/30 bg-[#F5B400]/15 p-5">
        <p class="text-sm font-black uppercase tracking-wide text-[#0A2A6B]">Accountability Rule</p>
        <p class="mt-2 text-sm leading-7 text-[#2E2E2E]/75">
            Use positive numbers to add correction votes and negative numbers to remove correction votes. These do not create member vote records.
        </p>
    </section>

    <section class="mt-6 grid gap-5">
        @forelse ($positions as $position)
            <article class="overflow-hidden rounded-lg bg-white shadow-sm ring-1 ring-[#0A2A6B]/10">
                <div class="border-b border-[#0A2A6B]/10 bg-[#F2F2F2] px-5 py-4">
                    <div class="flex flex-col gap-2 sm:flex-row sm:items-center sm:justify-between">
                        <div>
                            <h2 class="text-xl font-black text-[#0A2A6B]">{{ $position->name }}</h2>
                            <p class="mt-1 text-sm font-semibold text-[#2E2E2E]/65">{{ $position->academicSession?->name }}</p>
                        </div>
                        <span class="rounded-lg bg-white px-3 py-2 text-xs font-black text-[#0A2A6B] ring-1 ring-[#0A2A6B]/10">Correction Entry</span>
                    </div>
                </div>

                <div class="grid gap-4 p-5">
                    @forelse ($position->aspirants as $aspirant)
                        @php
                            $key = $position->id.':'.$aspirant->id;
                            $memberVotes = (int) ($voteTotals[$key]->vote_total ?? 0);
                            $adjustments = (int) ($adjustmentTotals[$key]->adjustment_total ?? 0);
                            $officialTotal = max(0, $memberVotes + $adjustments);
                        @endphp

                        <form action="{{ route('admin.election.vote-adjustments.store') }}" method="POST" class="rounded-lg border border-[#0A2A6B]/10 bg-white p-4">
                            @csrf
                            <input type="hidden" name="academic_session_id" value="{{ $selectedSessionId }}">
                            <input type="hidden" name="position_id" value="{{ $position->id }}">
                            <input type="hidden" name="aspirant_id" value="{{ $aspirant->id }}">

                            <div class="grid gap-4 lg:grid-cols-[1fr_120px_120px_120px_140px_1.2fr_auto] lg:items-end">
                                <div>
                                    <p class="text-xs font-black uppercase tracking-wide text-[#F5B400]">Aspirant</p>
                                    <p class="mt-1 text-base font-black text-[#0A2A6B]">{{ $aspirant->name }}</p>
                                </div>
                                <div>
                                    <p class="text-xs font-bold text-[#2E2E2E]/55">Member Votes</p>
                                    <p class="mt-1 text-lg font-black text-[#0A2A6B]">{{ $memberVotes }}</p>
                                </div>
                                <div>
                                    <p class="text-xs font-bold text-[#2E2E2E]/55">Adjustments</p>
                                    <p class="mt-1 text-lg font-black {{ $adjustments >= 0 ? 'text-[#1FA774]' : 'text-red-600' }}">{{ $adjustments }}</p>
                                </div>
                                <div>
                                    <p class="text-xs font-bold text-[#2E2E2E]/55">Official</p>
                                    <p class="mt-1 text-lg font-black text-[#0A2A6B]">{{ $officialTotal }}</p>
                                </div>
                                <label class="grid gap-2 text-sm font-bold text-[#0A2A6B]">
                                    Adjustment
                                    <input name="adjustment" type="number" min="-100000" max="100000" required placeholder="+1 or -1" class="rounded-lg border border-[#0A2A6B]/15 px-3 py-2 font-normal text-[#2E2E2E] outline-none transition focus:border-[#F5B400] focus:ring-4 focus:ring-[#F5B400]/20">
                                </label>
                                <label class="grid gap-2 text-sm font-bold text-[#0A2A6B]">
                                    Reason
                                    <input name="reason" required minlength="10" maxlength="1000" placeholder="Audit reason" class="rounded-lg border border-[#0A2A6B]/15 px-3 py-2 font-normal text-[#2E2E2E] outline-none transition focus:border-[#F5B400] focus:ring-4 focus:ring-[#F5B400]/20">
                                </label>
                                <button type="submit" class="rounded-lg bg-[#1FA774] px-4 py-2 text-sm font-black text-white transition hover:bg-[#198b61]">Record</button>
                            </div>
                        </form>
                    @empty
                        <p class="rounded-lg bg-[#F2F2F2] p-4 text-sm font-semibold text-[#2E2E2E]/65">No aspirants assigned to this position.</p>
                    @endforelse
                </div>
            </article>
        @empty
            <p class="rounded-lg bg-white p-8 text-center text-sm font-semibold text-[#2E2E2E]/65 shadow-sm ring-1 ring-[#0A2A6B]/10">No election position for this session.</p>
        @endforelse
    </section>

    <section class="mt-6 overflow-hidden rounded-lg bg-white shadow-sm ring-1 ring-[#0A2A6B]/10">
        <div class="border-b border-[#0A2A6B]/10 px-5 py-4">
            <h2 class="text-xl font-black text-[#0A2A6B]">Recent Adjustment Audit</h2>
        </div>
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-[#0A2A6B]/10">
                <thead class="bg-[#F2F2F2]">
                    <tr>
                        <th class="px-5 py-3 text-left text-xs font-black uppercase tracking-wide text-[#2E2E2E]/65">Admin</th>
                        <th class="px-5 py-3 text-left text-xs font-black uppercase tracking-wide text-[#2E2E2E]/65">Position</th>
                        <th class="px-5 py-3 text-left text-xs font-black uppercase tracking-wide text-[#2E2E2E]/65">Aspirant</th>
                        <th class="px-5 py-3 text-left text-xs font-black uppercase tracking-wide text-[#2E2E2E]/65">Change</th>
                        <th class="px-5 py-3 text-left text-xs font-black uppercase tracking-wide text-[#2E2E2E]/65">Before / After</th>
                        <th class="px-5 py-3 text-left text-xs font-black uppercase tracking-wide text-[#2E2E2E]/65">Reason</th>
                        <th class="px-5 py-3 text-left text-xs font-black uppercase tracking-wide text-[#2E2E2E]/65">Time</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-[#0A2A6B]/10">
                    @forelse ($recentAdjustments as $adjustment)
                        <tr>
                            <td class="px-5 py-4 text-sm font-black text-[#0A2A6B]">{{ $adjustment->admin?->name ?? 'Deleted admin' }}</td>
                            <td class="px-5 py-4 text-sm font-semibold text-[#2E2E2E]/75">{{ $adjustment->position?->name }}</td>
                            <td class="px-5 py-4 text-sm font-semibold text-[#2E2E2E]/75">{{ $adjustment->aspirant?->name }}</td>
                            <td class="px-5 py-4 text-sm font-black {{ $adjustment->adjustment >= 0 ? 'text-[#1FA774]' : 'text-red-600' }}">{{ $adjustment->adjustment >= 0 ? '+' : '' }}{{ $adjustment->adjustment }}</td>
                            <td class="px-5 py-4 text-sm font-semibold text-[#2E2E2E]/75">{{ $adjustment->before_total }} → {{ $adjustment->after_total }}</td>
                            <td class="max-w-sm px-5 py-4 text-sm font-semibold text-[#2E2E2E]/65">{{ $adjustment->reason }}</td>
                            <td class="px-5 py-4 text-sm font-semibold text-[#2E2E2E]/65">{{ $adjustment->created_at?->format('M d, Y h:i A') }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="px-5 py-10 text-center text-sm font-semibold text-[#2E2E2E]/65">No vote adjustments recorded yet.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </section>
@endsection
