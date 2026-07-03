@extends('layouts.dashboard', ['pageTitle' => 'Academic Sessions'])

@section('content')
    <section class="rounded-lg bg-[#0A2A6B] p-6 text-white shadow-xl sm:p-8">
        <div class="flex flex-col gap-5 lg:flex-row lg:items-end lg:justify-between">
            <div>
                <p class="text-sm font-black uppercase tracking-wide text-[#F5B400]">Academic Session Management</p>
                <h1 class="mt-3 text-3xl font-black leading-tight sm:text-4xl">Manage NABAMS academic sessions</h1>
                <p class="mt-4 max-w-3xl text-sm leading-7 text-[#F2F2F2]/80">
                    Create sessions, keep old records, and choose exactly one current active session for fees, registrations, and reports.
                </p>
            </div>
            <a href="{{ route('admin.academic-sessions.create') }}" class="inline-flex justify-center rounded-lg bg-[#F5B400] px-5 py-3 text-sm font-black text-[#0A2A6B] transition hover:bg-[#ffd15c]">New Session</a>
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

    <section class="mt-6 grid gap-5 md:grid-cols-3">
        <div class="rounded-lg bg-white p-6 shadow-sm ring-1 ring-[#0A2A6B]/10">
            <p class="text-sm font-black uppercase tracking-wide text-[#F5B400]">Total Sessions</p>
            <p class="mt-3 text-3xl font-black text-[#0A2A6B]">{{ $academicSessions->total() }}</p>
        </div>
        <div class="rounded-lg bg-white p-6 shadow-sm ring-1 ring-[#0A2A6B]/10">
            <p class="text-sm font-black uppercase tracking-wide text-[#F5B400]">Current Session</p>
            <p class="mt-3 text-3xl font-black text-[#1FA774]">{{ $currentSession?->name ?? 'Not set' }}</p>
        </div>
        <div class="rounded-lg bg-white p-6 shadow-sm ring-1 ring-[#0A2A6B]/10">
            <p class="text-sm font-black uppercase tracking-wide text-[#F5B400]">Rule</p>
            <p class="mt-3 text-lg font-black text-[#0A2A6B]">Only one active current session</p>
        </div>
    </section>

    <section class="mt-6 overflow-hidden rounded-lg bg-white shadow-sm ring-1 ring-[#0A2A6B]/10">
        <div class="border-b border-[#0A2A6B]/10 px-5 py-4">
            <h2 class="text-xl font-black text-[#0A2A6B]">Sessions</h2>
        </div>

        <div class="hidden overflow-x-auto lg:block">
            <table class="min-w-full divide-y divide-[#0A2A6B]/10">
                <thead class="bg-[#F2F2F2]">
                    <tr>
                        <th class="px-5 py-3 text-left text-xs font-black uppercase tracking-wide text-[#2E2E2E]/65">Name</th>
                        <th class="px-5 py-3 text-left text-xs font-black uppercase tracking-wide text-[#2E2E2E]/65">Years</th>
                        <th class="px-5 py-3 text-left text-xs font-black uppercase tracking-wide text-[#2E2E2E]/65">Current</th>
                        <th class="px-5 py-3 text-left text-xs font-black uppercase tracking-wide text-[#2E2E2E]/65">Status</th>
                        <th class="px-5 py-3 text-right text-xs font-black uppercase tracking-wide text-[#2E2E2E]/65">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-[#0A2A6B]/10">
                    @forelse ($academicSessions as $session)
                        <tr>
                            <td class="px-5 py-4 font-black text-[#0A2A6B]">{{ $session->name }}</td>
                            <td class="px-5 py-4 text-sm font-semibold text-[#2E2E2E]/75">{{ $session->starts_at_year }} - {{ $session->ends_at_year }}</td>
                            <td class="px-5 py-4">
                                <span class="rounded-full px-3 py-1 text-xs font-black {{ $session->is_current === 'Yes' ? 'bg-[#1FA774]/10 text-[#1FA774]' : 'bg-[#F2F2F2] text-[#2E2E2E]/70' }}">{{ $session->is_current }}</span>
                            </td>
                            <td class="px-5 py-4">
                                <span class="rounded-full px-3 py-1 text-xs font-black {{ $session->is_active === 'Yes' ? 'bg-[#0A2A6B]/10 text-[#0A2A6B]' : 'bg-[#F5B400]/20 text-[#0A2A6B]' }}">{{ $session->is_active }}</span>
                            </td>
                            <td class="px-5 py-4">
                                <div class="flex justify-end gap-2">
                                    @if ($session->is_current !== 'Yes')
                                        <form action="{{ route('admin.academic-sessions.make-current', $session) }}" method="POST">
                                            @csrf
                                            @method('PATCH')
                                            <button type="submit" class="rounded-lg border border-[#1FA774]/30 px-3 py-2 text-xs font-black text-[#1FA774] transition hover:bg-[#1FA774] hover:text-white">Make Current</button>
                                        </form>
                                    @endif
                                    <a href="{{ route('admin.academic-sessions.edit', $session) }}" class="rounded-lg border border-[#0A2A6B]/20 px-3 py-2 text-xs font-black text-[#0A2A6B] transition hover:bg-[#0A2A6B] hover:text-white">Edit</a>
                                    @if ($session->is_current !== 'Yes')
                                        <form action="{{ route('admin.academic-sessions.destroy', $session) }}" method="POST" onsubmit="return confirm('Delete this academic session?');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="rounded-lg border border-[#F5B400]/50 px-3 py-2 text-xs font-black text-[#0A2A6B] transition hover:bg-[#F5B400]">Delete</button>
                                        </form>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-5 py-10 text-center text-sm font-semibold text-[#2E2E2E]/65">No academic session has been created yet.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="grid gap-4 p-4 lg:hidden">
            @forelse ($academicSessions as $session)
                <article class="rounded-lg border border-[#0A2A6B]/10 bg-[#F2F2F2] p-4">
                    <div class="flex items-start justify-between gap-4">
                        <div>
                            <h3 class="text-lg font-black text-[#0A2A6B]">{{ $session->name }}</h3>
                            <p class="mt-1 text-sm font-semibold text-[#2E2E2E]/65">{{ $session->starts_at_year }} - {{ $session->ends_at_year }}</p>
                        </div>
                        <span class="rounded-full px-3 py-1 text-xs font-black {{ $session->is_current === 'Yes' ? 'bg-[#1FA774]/10 text-[#1FA774]' : 'bg-white text-[#2E2E2E]/70' }}">{{ $session->is_current }}</span>
                    </div>
                    <div class="mt-4 flex flex-wrap gap-2">
                        @if ($session->is_current !== 'Yes')
                            <form action="{{ route('admin.academic-sessions.make-current', $session) }}" method="POST">
                                @csrf
                                @method('PATCH')
                                <button type="submit" class="rounded-lg bg-[#1FA774] px-3 py-2 text-xs font-black text-white">Make Current</button>
                            </form>
                        @endif
                        <a href="{{ route('admin.academic-sessions.edit', $session) }}" class="rounded-lg bg-[#0A2A6B] px-3 py-2 text-xs font-black text-white">Edit</a>
                    </div>
                </article>
            @empty
                <p class="rounded-lg bg-[#F2F2F2] p-6 text-center text-sm font-semibold text-[#2E2E2E]/65">No academic session has been created yet.</p>
            @endforelse
        </div>

        @if ($academicSessions->hasPages())
            <div class="border-t border-[#0A2A6B]/10 px-5 py-4">
                {{ $academicSessions->links() }}
            </div>
        @endif
    </section>
@endsection
