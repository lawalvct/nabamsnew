@extends('layouts.dashboard', ['pageTitle' => 'Admins'])

@section('content')
    <section class="rounded-lg bg-[#0A2A6B] p-6 text-white shadow-xl sm:p-8">
        <div class="flex flex-col gap-5 lg:flex-row lg:items-end lg:justify-between">
            <div>
                <p class="text-sm font-black uppercase tracking-wide text-[#F5B400]">Admin Management</p>
                <h1 class="mt-3 text-3xl font-black sm:text-4xl">Manage administrator accounts</h1>
                <p class="mt-4 max-w-3xl text-sm leading-7 text-[#F2F2F2]/80">
                    Create admin users, update access status, reset passwords, and protect critical admin accounts.
                </p>
            </div>
            <a href="{{ route('admin.admins.create') }}" class="inline-flex justify-center rounded-lg bg-[#F5B400] px-5 py-3 text-sm font-black text-[#0A2A6B] transition hover:bg-[#ffd15c]">New Admin</a>
        </div>
    </section>

    @if (session('success'))
        <div class="mt-6 rounded-lg border border-[#1FA774]/20 bg-[#1FA774]/10 px-5 py-4 text-sm font-bold text-[#0A2A6B]">{{ session('success') }}</div>
    @endif

    @if (session('error'))
        <div class="mt-6 rounded-lg border border-[#F5B400]/40 bg-[#F5B400]/15 px-5 py-4 text-sm font-bold text-[#0A2A6B]">{{ session('error') }}</div>
    @endif

    <section class="mt-6 grid gap-5 md:grid-cols-3">
        <div class="rounded-lg bg-white p-5 shadow-sm ring-1 ring-[#0A2A6B]/10">
            <p class="text-sm font-black uppercase tracking-wide text-[#F5B400]">Total Admins</p>
            <p class="mt-3 text-3xl font-black text-[#0A2A6B]">{{ $counts['total'] }}</p>
        </div>
        <div class="rounded-lg bg-white p-5 shadow-sm ring-1 ring-[#0A2A6B]/10">
            <p class="text-sm font-black uppercase tracking-wide text-[#F5B400]">Active</p>
            <p class="mt-3 text-3xl font-black text-[#1FA774]">{{ $counts['active'] }}</p>
        </div>
        <div class="rounded-lg bg-white p-5 shadow-sm ring-1 ring-[#0A2A6B]/10">
            <p class="text-sm font-black uppercase tracking-wide text-[#F5B400]">Banned</p>
            <p class="mt-3 text-3xl font-black text-[#0A2A6B]">{{ $counts['banned'] }}</p>
        </div>
    </section>

    <section class="mt-6 rounded-lg bg-white p-5 shadow-sm ring-1 ring-[#0A2A6B]/10">
        <form method="GET" class="grid gap-4 lg:grid-cols-[1fr_160px_auto]">
            <input name="search" value="{{ $search }}" placeholder="Search name, email, ID or phone" class="rounded-lg border border-[#0A2A6B]/15 px-4 py-3 text-sm outline-none transition focus:border-[#F5B400] focus:ring-4 focus:ring-[#F5B400]/20">
            <select name="is_active" class="rounded-lg border border-[#0A2A6B]/15 bg-white px-4 py-3 text-sm outline-none transition focus:border-[#F5B400] focus:ring-4 focus:ring-[#F5B400]/20">
                <option value="">All status</option>
                <option value="Yes" @selected($status === 'Yes')>Active</option>
                <option value="No" @selected($status === 'No')>Inactive</option>
            </select>
            <button type="submit" class="rounded-lg bg-[#1FA774] px-5 py-3 text-sm font-black text-white transition hover:bg-[#198b61]">Filter</button>
        </form>
    </section>

    <section class="mt-6 overflow-hidden rounded-lg bg-white shadow-sm ring-1 ring-[#0A2A6B]/10">
        <div class="hidden overflow-x-auto lg:block">
            <table class="min-w-full divide-y divide-[#0A2A6B]/10">
                <thead class="bg-[#F2F2F2]">
                    <tr>
                        <th class="px-5 py-3 text-left text-xs font-black uppercase tracking-wide text-[#2E2E2E]/65">Admin</th>
                        <th class="px-5 py-3 text-left text-xs font-black uppercase tracking-wide text-[#2E2E2E]/65">Admin ID</th>
                        <th class="px-5 py-3 text-left text-xs font-black uppercase tracking-wide text-[#2E2E2E]/65">Status</th>
                        <th class="px-5 py-3 text-left text-xs font-black uppercase tracking-wide text-[#2E2E2E]/65">Ban</th>
                        <th class="px-5 py-3 text-right text-xs font-black uppercase tracking-wide text-[#2E2E2E]/65">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-[#0A2A6B]/10">
                    @forelse ($admins as $admin)
                        <tr>
                            <td class="px-5 py-4">
                                <div class="flex items-center gap-3">
                                    <div class="grid h-11 w-11 shrink-0 place-items-center overflow-hidden rounded-lg bg-[#0A2A6B] text-xs font-black text-white">
                                        @if ($admin->image_url)
                                            <img src="{{ $admin->image_url }}" alt="{{ $admin->name }}" class="h-full w-full object-cover">
                                        @else
                                            {{ strtoupper(substr($admin->firstname, 0, 1).substr($admin->lastname ?? '', 0, 1)) ?: 'AD' }}
                                        @endif
                                    </div>
                                    <div>
                                        <p class="font-black text-[#0A2A6B]">{{ $admin->name ?: $admin->firstname }}</p>
                                        <p class="text-xs font-semibold text-[#2E2E2E]/60">{{ $admin->email }}</p>
                                    </div>
                                </div>
                            </td>
                            <td class="px-5 py-4 text-sm font-semibold text-[#2E2E2E]/75">{{ $admin->matno }}</td>
                            <td class="px-5 py-4"><span class="rounded-full px-3 py-1 text-xs font-black {{ $admin->is_active === 'Yes' ? 'bg-[#1FA774]/10 text-[#1FA774]' : 'bg-[#F5B400]/20 text-[#0A2A6B]' }}">{{ $admin->is_active }}</span></td>
                            <td class="px-5 py-4"><span class="rounded-full px-3 py-1 text-xs font-black {{ $admin->is_ban === 'Yes' ? 'bg-[#F5B400]/20 text-[#0A2A6B]' : 'bg-[#F2F2F2] text-[#2E2E2E]/70' }}">{{ $admin->is_ban }}</span></td>
                            <td class="px-5 py-4">
                                <div class="flex justify-end gap-2">
                                    <a href="{{ route('admin.admins.edit', $admin) }}" class="rounded-lg bg-[#F5B400] px-3 py-2 text-xs font-black text-[#0A2A6B]">Edit</a>
                                    <form action="{{ route('admin.admins.destroy', $admin) }}" method="POST" onsubmit="return confirm('Delete this admin account?');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="rounded-lg border border-red-200 px-3 py-2 text-xs font-black text-red-600 transition hover:bg-red-600 hover:text-white" @disabled($admin->is(auth()->user()))>Delete</button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-5 py-10 text-center text-sm font-semibold text-[#2E2E2E]/65">No admins found.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="grid gap-4 p-4 lg:hidden">
            @forelse ($admins as $admin)
                <article class="rounded-lg border border-[#0A2A6B]/10 bg-[#F2F2F2] p-4">
                    <div class="flex items-start gap-3">
                        <div class="grid h-14 w-14 shrink-0 place-items-center overflow-hidden rounded-lg bg-[#0A2A6B] text-sm font-black text-white">
                            @if ($admin->image_url)
                                <img src="{{ $admin->image_url }}" alt="{{ $admin->name }}" class="h-full w-full object-cover">
                            @else
                                {{ strtoupper(substr($admin->firstname, 0, 1).substr($admin->lastname ?? '', 0, 1)) ?: 'AD' }}
                            @endif
                        </div>
                        <div class="min-w-0">
                            <h3 class="truncate text-lg font-black text-[#0A2A6B]">{{ $admin->name ?: $admin->firstname }}</h3>
                            <p class="truncate text-sm font-semibold text-[#2E2E2E]/65">{{ $admin->email }}</p>
                            <p class="mt-1 text-xs font-black text-[#1FA774]">Active {{ $admin->is_active }} · Banned {{ $admin->is_ban }}</p>
                        </div>
                    </div>
                    <div class="mt-4 flex gap-2">
                        <a href="{{ route('admin.admins.edit', $admin) }}" class="rounded-lg bg-[#F5B400] px-3 py-2 text-xs font-black text-[#0A2A6B]">Edit</a>
                        <form action="{{ route('admin.admins.destroy', $admin) }}" method="POST" onsubmit="return confirm('Delete this admin account?');">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="rounded-lg border border-red-200 bg-white px-3 py-2 text-xs font-black text-red-600" @disabled($admin->is(auth()->user()))>Delete</button>
                        </form>
                    </div>
                </article>
            @empty
                <p class="rounded-lg bg-[#F2F2F2] p-6 text-center text-sm font-semibold text-[#2E2E2E]/65">No admins found.</p>
            @endforelse
        </div>

        @if ($admins->hasPages())
            <div class="border-t border-[#0A2A6B]/10 px-5 py-4">{{ $admins->links() }}</div>
        @endif
    </section>
@endsection
