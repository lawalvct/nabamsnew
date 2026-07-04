<section class="mt-6 grid gap-5 lg:grid-cols-2">
    @forelse ($positions as $position)
        <article class="rounded-lg bg-white p-6 shadow-sm ring-1 ring-[#0A2A6B]/10">
            <div class="flex items-start justify-between gap-4">
                @php
                    $positionAdjustmentTotal = (int) ($positionAdjustmentTotals[$position->id]->adjustment_total ?? 0);
                    $positionOfficialTotal = max(0, $position->votes_count + $positionAdjustmentTotal);
                @endphp
                <div>
                    <h2 class="text-xl font-black text-[#0A2A6B]">{{ $position->name }}</h2>
                    <p class="mt-1 text-sm font-semibold text-[#2E2E2E]/65">
                        {{ $positionOfficialTotal }} official total &middot; {{ $position->votes_count }} member votes &middot; {{ $positionAdjustmentTotal >= 0 ? '+' : '' }}{{ $positionAdjustmentTotal }} adjustments
                    </p>
                </div>
                <span class="rounded-lg bg-[#F5B400]/20 px-3 py-2 text-xs font-black text-[#0A2A6B]">{{ $position->academicSession?->name }}</span>
            </div>

            <div class="mt-5 grid gap-3">
                @forelse ($position->aspirants->sortByDesc(function ($aspirant) use ($position, $voteTotals, $adjustmentTotals) {
                    $key = $position->id.':'.$aspirant->id;

                    return (int) ($voteTotals[$key]->vote_total ?? 0) + (int) ($adjustmentTotals[$key]->adjustment_total ?? 0);
                }) as $aspirant)
                    @php
                        $key = $position->id.':'.$aspirant->id;
                        $aspirantVoteCount = (int) ($voteTotals[$key]->vote_total ?? 0);
                        $aspirantAdjustmentCount = (int) ($adjustmentTotals[$key]->adjustment_total ?? 0);
                        $aspirantOfficialCount = max(0, $aspirantVoteCount + $aspirantAdjustmentCount);
                        $percentage = $positionOfficialTotal > 0 ? round(($aspirantOfficialCount / $positionOfficialTotal) * 100) : 0;
                    @endphp
                    <div>
                        <div class="flex items-center justify-between gap-4 text-sm">
                            <p class="font-black text-[#0A2A6B]">{{ $aspirant->name }}</p>
                            <p class="text-right font-black text-[#1FA774]">
                                {{ $aspirantOfficialCount }} official
                                <span class="block text-xs text-[#2E2E2E]/60">{{ $aspirantVoteCount }} member &middot; {{ $aspirantAdjustmentCount >= 0 ? '+' : '' }}{{ $aspirantAdjustmentCount }} adj.</span>
                            </p>
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
</section>

<section class="mt-6 overflow-hidden rounded-lg bg-white shadow-sm ring-1 ring-[#0A2A6B]/10">
    <div class="border-b border-[#0A2A6B]/10 px-5 py-4">
        <h2 class="text-xl font-black text-[#0A2A6B]">Recent Vote Adjustments</h2>
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
                </tr>
            </thead>
            <tbody class="divide-y divide-[#0A2A6B]/10">
                @forelse ($recentAdjustments as $adjustment)
                    <tr>
                        <td class="px-5 py-4 text-sm font-black text-[#0A2A6B]">{{ $adjustment->admin?->name ?? 'Deleted admin' }}</td>
                        <td class="px-5 py-4 text-sm font-semibold text-[#2E2E2E]/75">{{ $adjustment->position?->name }}</td>
                        <td class="px-5 py-4 text-sm font-semibold text-[#2E2E2E]/75">{{ $adjustment->aspirant?->name }}</td>
                        <td class="px-5 py-4 text-sm font-black {{ $adjustment->adjustment >= 0 ? 'text-[#1FA774]' : 'text-red-600' }}">{{ $adjustment->adjustment >= 0 ? '+' : '' }}{{ $adjustment->adjustment }}</td>
                        <td class="px-5 py-4 text-sm font-semibold text-[#2E2E2E]/75">{{ $adjustment->before_total }} &rarr; {{ $adjustment->after_total }}</td>
                        <td class="max-w-sm px-5 py-4 text-sm font-semibold text-[#2E2E2E]/65">{{ $adjustment->reason }}</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="px-5 py-8 text-center text-sm font-semibold text-[#2E2E2E]/65">No vote adjustments recorded for this session.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</section>
