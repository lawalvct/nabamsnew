@extends('layouts.dashboard', ['pageTitle' => 'Members'])

@section('content')
    <section class="rounded-lg bg-[#0A2A6B] p-6 text-white shadow-xl sm:p-8">
        <div class="flex flex-col gap-5 lg:flex-row lg:items-end lg:justify-between">
            <div>
                <p class="text-sm font-black uppercase tracking-wide text-[#F5B400]">Members Management</p>
                <h1 class="mt-3 text-3xl font-black sm:text-4xl">Manage registered members</h1>
                <p class="mt-4 max-w-3xl text-sm leading-7 text-[#F2F2F2]/80">
                    Search members, review profiles, update account status, fee status, and academic details.
                </p>
            </div>
            <a href="{{ route('admin.members.create') }}" class="inline-flex justify-center rounded-lg bg-[#F5B400] px-5 py-3 text-sm font-black text-[#0A2A6B] transition hover:bg-[#ffd15c]">Add Member</a>
        </div>
    </section>

    @if (session('success'))
        <div class="mt-6 rounded-lg border border-[#1FA774]/20 bg-[#1FA774]/10 px-5 py-4 text-sm font-bold text-[#0A2A6B]">{{ session('success') }}</div>
    @endif

    <section class="mt-6 grid gap-5 md:grid-cols-4">
        <div class="rounded-lg bg-white p-5 shadow-sm ring-1 ring-[#0A2A6B]/10">
            <p class="text-sm font-black uppercase tracking-wide text-[#F5B400]">Total</p>
            <p class="mt-3 text-3xl font-black text-[#0A2A6B]">{{ $counts['total'] }}</p>
        </div>
        <div class="rounded-lg bg-white p-5 shadow-sm ring-1 ring-[#0A2A6B]/10">
            <p class="text-sm font-black uppercase tracking-wide text-[#F5B400]">Active</p>
            <p class="mt-3 text-3xl font-black text-[#1FA774]">{{ $counts['active'] }}</p>
        </div>
        <div class="rounded-lg bg-white p-5 shadow-sm ring-1 ring-[#0A2A6B]/10">
            <p class="text-sm font-black uppercase tracking-wide text-[#F5B400]">Fee Paid</p>
            <p class="mt-3 text-3xl font-black text-[#0A2A6B]">{{ $counts['paid'] }}</p>
        </div>
        <div class="rounded-lg bg-white p-5 shadow-sm ring-1 ring-[#0A2A6B]/10">
            <p class="text-sm font-black uppercase tracking-wide text-[#F5B400]">Banned</p>
            <p class="mt-3 text-3xl font-black text-[#0A2A6B]">{{ $counts['banned'] }}</p>
        </div>
    </section>

    <section class="mt-6 rounded-lg bg-white p-5 shadow-sm ring-1 ring-[#0A2A6B]/10">
        <form method="GET" class="grid gap-4 lg:grid-cols-[1fr_160px_160px_auto]">
            <input name="search" value="{{ $search }}" placeholder="Search name, email, matric or phone" class="rounded-lg border border-[#0A2A6B]/15 px-4 py-3 text-sm outline-none transition focus:border-[#F5B400] focus:ring-4 focus:ring-[#F5B400]/20">
            <select name="is_active" class="rounded-lg border border-[#0A2A6B]/15 bg-white px-4 py-3 text-sm outline-none transition focus:border-[#F5B400] focus:ring-4 focus:ring-[#F5B400]/20">
                <option value="">All status</option>
                <option value="Yes" @selected($status === 'Yes')>Active</option>
                <option value="No" @selected($status === 'No')>Inactive</option>
            </select>
            <select name="fee_paid" class="rounded-lg border border-[#0A2A6B]/15 bg-white px-4 py-3 text-sm outline-none transition focus:border-[#F5B400] focus:ring-4 focus:ring-[#F5B400]/20">
                <option value="">All fees</option>
                <option value="Yes" @selected($feePaid === 'Yes')>Paid</option>
                <option value="No" @selected($feePaid === 'No')>Not Paid</option>
            </select>
            <button type="submit" class="rounded-lg bg-[#1FA774] px-5 py-3 text-sm font-black text-white transition hover:bg-[#198b61]">Filter</button>
        </form>
    </section>

    <section class="mt-6 overflow-hidden rounded-lg bg-white shadow-sm ring-1 ring-[#0A2A6B]/10">
        <div class="hidden overflow-x-auto lg:block">
            <table class="min-w-full divide-y divide-[#0A2A6B]/10">
                <thead class="bg-[#F2F2F2]">
                    <tr>
                        <th class="px-5 py-3 text-left text-xs font-black uppercase tracking-wide text-[#2E2E2E]/65">Member</th>
                        <th class="px-5 py-3 text-left text-xs font-black uppercase tracking-wide text-[#2E2E2E]/65">Matric</th>
                        <th class="px-5 py-3 text-left text-xs font-black uppercase tracking-wide text-[#2E2E2E]/65">Status</th>
                        <th class="px-5 py-3 text-left text-xs font-black uppercase tracking-wide text-[#2E2E2E]/65">Fee</th>
                        <th class="px-5 py-3 text-right text-xs font-black uppercase tracking-wide text-[#2E2E2E]/65">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-[#0A2A6B]/10">
                    @forelse ($members as $member)
                        <tr>
                            <td class="px-5 py-4">
                                <div class="flex items-center gap-3">
                                    <div class="grid h-11 w-11 shrink-0 place-items-center overflow-hidden rounded-lg bg-[#0A2A6B] text-xs font-black text-white">
                                        @if ($member->image_url)
                                            <img src="{{ $member->image_url }}" alt="{{ $member->name }}" class="h-full w-full object-cover">
                                        @else
                                            {{ strtoupper(substr($member->firstname, 0, 1).substr($member->lastname ?? '', 0, 1)) ?: 'NM' }}
                                        @endif
                                    </div>
                                    <div>
                                        <p class="font-black text-[#0A2A6B]">{{ $member->name ?: $member->firstname }}</p>
                                        <p class="text-xs font-semibold text-[#2E2E2E]/60">{{ $member->email }}</p>
                                    </div>
                                </div>
                            </td>
                            <td class="px-5 py-4 text-sm font-semibold text-[#2E2E2E]/75">{{ $member->matno ?: 'Not provided' }}</td>
                            <td class="px-5 py-4"><span class="rounded-full px-3 py-1 text-xs font-black {{ $member->is_active === 'Yes' ? 'bg-[#1FA774]/10 text-[#1FA774]' : 'bg-[#F5B400]/20 text-[#0A2A6B]' }}">{{ $member->is_active }}</span></td>
                            <td class="px-5 py-4"><span class="rounded-full px-3 py-1 text-xs font-black {{ $member->fee_paid === 'Yes' ? 'bg-[#1FA774]/10 text-[#1FA774]' : 'bg-[#F2F2F2] text-[#2E2E2E]/70' }}">{{ $member->fee_paid }}</span></td>
                            <td class="px-5 py-4">
                                <div class="flex justify-end gap-2">
                                    <a href="{{ route('admin.members.show', $member) }}" class="rounded-lg border border-[#0A2A6B]/20 px-3 py-2 text-xs font-black text-[#0A2A6B] transition hover:bg-[#0A2A6B] hover:text-white">View</a>
                                    <a href="{{ route('admin.members.edit', $member) }}" class="rounded-lg bg-[#F5B400] px-3 py-2 text-xs font-black text-[#0A2A6B]">Edit</a>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-5 py-10 text-center text-sm font-semibold text-[#2E2E2E]/65">No members found.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="grid gap-4 p-4 lg:hidden">
            @forelse ($members as $member)
                <article class="rounded-lg border border-[#0A2A6B]/10 bg-[#F2F2F2] p-4">
                    <div class="flex items-start gap-3">
                        <div class="grid h-14 w-14 shrink-0 place-items-center overflow-hidden rounded-lg bg-[#0A2A6B] text-sm font-black text-white">
                            @if ($member->image_url)
                                <img src="{{ $member->image_url }}" alt="{{ $member->name }}" class="h-full w-full object-cover">
                            @else
                                {{ strtoupper(substr($member->firstname, 0, 1).substr($member->lastname ?? '', 0, 1)) ?: 'NM' }}
                            @endif
                        </div>
                        <div class="min-w-0">
                            <h3 class="truncate text-lg font-black text-[#0A2A6B]">{{ $member->name ?: $member->firstname }}</h3>
                            <p class="truncate text-sm font-semibold text-[#2E2E2E]/65">{{ $member->email }}</p>
                            <p class="mt-1 text-xs font-black text-[#1FA774]">{{ $member->matno ?: 'No matric number' }} · Fee {{ $member->fee_paid }}</p>
                        </div>
                    </div>
                    <div class="mt-4 flex gap-2">
                        <a href="{{ route('admin.members.show', $member) }}" class="rounded-lg bg-[#0A2A6B] px-3 py-2 text-xs font-black text-white">View</a>
                        <a href="{{ route('admin.members.edit', $member) }}" class="rounded-lg bg-[#F5B400] px-3 py-2 text-xs font-black text-[#0A2A6B]">Edit</a>
                    </div>
                </article>
            @empty
                <p class="rounded-lg bg-[#F2F2F2] p-6 text-center text-sm font-semibold text-[#2E2E2E]/65">No members found.</p>
            @endforelse
        </div>

        @if ($members->hasPages())
            <div class="border-t border-[#0A2A6B]/10 px-5 py-4">{{ $members->links() }}</div>
        @endif
    </section>
@endsection
