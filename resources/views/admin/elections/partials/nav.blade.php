<div class="mb-6 flex flex-wrap gap-2">
    <a href="{{ route('admin.election.positions.index') }}" class="rounded-lg px-4 py-2 text-sm font-black transition {{ request()->routeIs('admin.election.positions.*') ? 'bg-[#0A2A6B] text-white' : 'bg-white text-[#0A2A6B] ring-1 ring-[#0A2A6B]/10 hover:ring-[#1FA774]' }}">Positions</a>
    <a href="{{ route('admin.election.aspirants.index') }}" class="rounded-lg px-4 py-2 text-sm font-black transition {{ request()->routeIs('admin.election.aspirants.*') ? 'bg-[#0A2A6B] text-white' : 'bg-white text-[#0A2A6B] ring-1 ring-[#0A2A6B]/10 hover:ring-[#1FA774]' }}">Aspirants</a>
    <a href="{{ route('admin.election.votes.index') }}" class="rounded-lg px-4 py-2 text-sm font-black transition {{ request()->routeIs('admin.election.votes.*') ? 'bg-[#0A2A6B] text-white' : 'bg-white text-[#0A2A6B] ring-1 ring-[#0A2A6B]/10 hover:ring-[#1FA774]' }}">Votes</a>
</div>
