@extends('layouts.dashboard', ['pageTitle' => 'Transactions'])

@php
    $filterClass = 'rounded-lg border border-[#0A2A6B]/15 bg-white px-4 py-3 text-sm font-normal text-[#2E2E2E] outline-none transition focus:border-[#F5B400] focus:ring-4 focus:ring-[#F5B400]/20';
    $statusLabels = ['pending' => 'Awaiting verification', 'approved' => 'Approved', 'rejected' => 'Rejected', 'awaiting_payment' => 'Not paid (drafts)'];
@endphp

@section('content')
    <section class="rounded-lg bg-[#0A2A6B] p-6 text-white shadow-xl sm:p-8">
        <p class="text-sm font-black uppercase tracking-wide text-[#F5B400]">Transactions</p>
        <h1 class="mt-3 text-3xl font-black leading-tight sm:text-4xl">Verify member payments</h1>
        <div class="flex flex-col gap-5 lg:flex-row lg:items-end lg:justify-between">
            <p class="mt-4 max-w-3xl text-sm leading-7 text-[#F2F2F2]/80">Check each uploaded evidence against your bank statement, then approve or reject. Members are told to expect a response within 24 hours.</p>
            <div class="flex flex-wrap gap-3">
                <a href="{{ route('admin.payments.create') }}" class="inline-flex justify-center rounded-lg bg-[#F5B400] px-5 py-3 text-sm font-black text-[#0A2A6B] transition hover:bg-[#ffd15c]">Record Payment</a>
                <a href="{{ route('admin.payments.export', request()->query()) }}" class="inline-flex justify-center rounded-lg border border-white/25 px-5 py-3 text-sm font-black text-white transition hover:bg-white/10">Export CSV</a>
            </div>
        </div>
    </section>

    @foreach (['success' => 'border-[#1FA774]/20 bg-[#1FA774]/10', 'error' => 'border-red-200 bg-red-50'] as $key => $tone)
        @if (session($key))
            <div class="mt-6 rounded-lg border {{ $tone }} px-5 py-4 text-sm font-bold text-[#0A2A6B]">{{ session($key) }}</div>
        @endif
    @endforeach

    <section class="mt-6 grid gap-5 sm:grid-cols-2 xl:grid-cols-4">
        <a href="{{ route('admin.payments.index', ['status' => 'pending']) }}" class="rounded-lg bg-white p-6 shadow-sm ring-1 ring-[#0A2A6B]/10 transition hover:ring-[#F5B400]">
            <p class="text-sm font-black uppercase tracking-wide text-[#F5B400]">Awaiting Verification</p>
            <p class="mt-3 text-3xl font-black text-[#0A2A6B]">{{ $statusCounts['pending'] ?? 0 }}</p>
        </a>
        <a href="{{ route('admin.payments.index', ['status' => 'approved']) }}" class="rounded-lg bg-white p-6 shadow-sm ring-1 ring-[#0A2A6B]/10 transition hover:ring-[#F5B400]">
            <p class="text-sm font-black uppercase tracking-wide text-[#F5B400]">Approved</p>
            <p class="mt-3 text-3xl font-black text-[#1FA774]">{{ $statusCounts['approved'] ?? 0 }}</p>
        </a>
        <a href="{{ route('admin.payments.index', ['status' => 'rejected']) }}" class="rounded-lg bg-white p-6 shadow-sm ring-1 ring-[#0A2A6B]/10 transition hover:ring-[#F5B400]">
            <p class="text-sm font-black uppercase tracking-wide text-[#F5B400]">Rejected</p>
            <p class="mt-3 text-3xl font-black text-red-600">{{ $statusCounts['rejected'] ?? 0 }}</p>
        </a>
        <div class="rounded-lg bg-white p-6 shadow-sm ring-1 ring-[#0A2A6B]/10">
            <p class="text-sm font-black uppercase tracking-wide text-[#F5B400]">Total Approved</p>
            <p class="mt-3 text-3xl font-black text-[#0A2A6B]">&#8358;{{ number_format($approvedTotal) }}</p>
        </div>
    </section>

    <form method="GET" action="{{ route('admin.payments.index') }}" class="mt-6 grid gap-4 rounded-lg bg-white p-5 shadow-sm ring-1 ring-[#0A2A6B]/10 sm:grid-cols-2 lg:grid-cols-7">
        <label class="grid gap-2 text-sm font-bold text-[#0A2A6B] lg:col-span-2">
            Search
            <input name="q" value="{{ $filters['q'] ?? '' }}" placeholder="Reference, name, matric no, email" class="{{ $filterClass }}">
        </label>
        <label class="grid gap-2 text-sm font-bold text-[#0A2A6B]">
            Status
            <select name="status" class="{{ $filterClass }}">
                <option value="">Submitted (all)</option>
                @foreach ($statusLabels as $value => $label)
                    <option value="{{ $value }}" @selected(($filters['status'] ?? '') === $value)>{{ $label }}</option>
                @endforeach
            </select>
        </label>
        <label class="grid gap-2 text-sm font-bold text-[#0A2A6B]">
            Type
            <select name="type" class="{{ $filterClass }}">
                <option value="">Dues &amp; resources</option>
                <option value="dues" @selected(($filters['type'] ?? '') === 'dues')>Dues</option>
                <option value="resource" @selected(($filters['type'] ?? '') === 'resource')>Resources</option>
            </select>
        </label>
        <label class="grid gap-2 text-sm font-bold text-[#0A2A6B]">
            Session
            <select name="academic_session_id" class="{{ $filterClass }}">
                <option value="">All</option>
                @foreach ($academicSessions as $session)
                    <option value="{{ $session->id }}" @selected((string) ($filters['academic_session_id'] ?? '') === (string) $session->id)>{{ $session->name }}</option>
                @endforeach
            </select>
        </label>
        <label class="grid gap-2 text-sm font-bold text-[#0A2A6B]">
            Semester / Level
            <div class="grid grid-cols-2 gap-2">
                <select name="semester" class="{{ $filterClass }} px-2">
                    <option value="">Sem.</option>
                    @foreach (['First', 'Second', 'Full'] as $semester)
                        <option value="{{ $semester }}" @selected(($filters['semester'] ?? '') === $semester)>{{ $semester }}</option>
                    @endforeach
                </select>
                <select name="level_id" class="{{ $filterClass }} px-2">
                    <option value="">Level</option>
                    @foreach ($levels as $level)
                        <option value="{{ $level->id }}" @selected((string) ($filters['level_id'] ?? '') === (string) $level->id)>{{ $level->name }}</option>
                    @endforeach
                </select>
            </div>
        </label>
        <div class="flex items-end gap-2">
            <button type="submit" class="flex-1 rounded-lg bg-[#0A2A6B] px-4 py-3 text-sm font-black text-white transition hover:bg-[#0d358a]">Filter</button>
            <a href="{{ route('admin.payments.index') }}" class="rounded-lg border border-[#0A2A6B]/20 px-4 py-3 text-sm font-black text-[#0A2A6B] transition hover:bg-[#F2F2F2]">Reset</a>
        </div>
    </form>

    <section class="mt-6 overflow-hidden rounded-lg bg-white shadow-sm ring-1 ring-[#0A2A6B]/10">
        <div class="hidden overflow-x-auto lg:block">
            <table class="min-w-full divide-y divide-[#0A2A6B]/10">
                <thead class="bg-[#F2F2F2]">
                    <tr>
                        @foreach (['Reference', 'Member', 'Period', 'Due / Paid', 'Submitted', 'Status', ''] as $heading)
                            <th class="px-5 py-3 text-left text-xs font-black uppercase tracking-wide text-[#2E2E2E]/65">{{ $heading }}</th>
                        @endforeach
                    </tr>
                </thead>
                <tbody class="divide-y divide-[#0A2A6B]/10">
                    @forelse ($payments as $payment)
                        <tr class="{{ $payment->status === 'pending' ? 'bg-[#F5B400]/5' : '' }}">
                            <td class="px-5 py-4 font-mono font-black tracking-wider text-[#0A2A6B]">{{ $payment->reference }}</td>
                            <td class="px-5 py-4">
                                <p class="font-black text-[#0A2A6B]">{{ $payment->user?->name }}</p>
                                <p class="text-xs font-semibold text-[#2E2E2E]/60">{{ $payment->user?->matno ?: $payment->user?->email }} &middot; {{ $payment->level_name }}</p>
                            </td>
                            <td class="px-5 py-4 text-sm font-semibold text-[#2E2E2E]/75">{{ $payment->periodLabel() }}</td>
                            <td class="px-5 py-4 text-sm">
                                <p class="font-semibold text-[#2E2E2E]/70">&#8358;{{ number_format($payment->amount_due) }}</p>
                                @if ($payment->amount_paid !== null)
                                    <p class="font-black {{ $payment->amount_paid < $payment->amount_due ? 'text-red-600' : 'text-[#1FA774]' }}">&#8358;{{ number_format($payment->amount_paid) }}</p>
                                @endif
                            </td>
                            <td class="px-5 py-4 text-sm text-[#2E2E2E]/70">{{ $payment->submitted_at?->diffForHumans() ?? '-' }}</td>
                            <td class="px-5 py-4"><span class="rounded-full px-3 py-1 text-xs font-black {{ $payment->statusClasses() }}">{{ $payment->statusLabel() }}</span></td>
                            <td class="px-5 py-4 text-right">
                                <a href="{{ route('admin.payments.show', $payment) }}" class="rounded-lg {{ $payment->status === 'pending' ? 'bg-[#F5B400] text-[#0A2A6B]' : 'border border-[#0A2A6B]/20 text-[#0A2A6B]' }} px-3 py-2 text-xs font-black transition hover:bg-[#0A2A6B] hover:text-white">
                                    {{ $payment->status === 'pending' ? 'Review' : 'View' }}
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="7" class="px-5 py-10 text-center text-sm font-semibold text-[#2E2E2E]/65">No payment found.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="grid gap-4 p-4 lg:hidden">
            @forelse ($payments as $payment)
                <a href="{{ route('admin.payments.show', $payment) }}" class="block rounded-lg border border-[#0A2A6B]/10 bg-[#F2F2F2] p-4">
                    <div class="flex items-start justify-between gap-3">
                        <div>
                            <p class="font-mono text-lg font-black tracking-wider text-[#0A2A6B]">{{ $payment->reference }}</p>
                            <p class="text-sm font-bold text-[#0A2A6B]">{{ $payment->user?->name }}</p>
                            <p class="text-xs font-semibold text-[#2E2E2E]/60">{{ $payment->periodLabel() }}</p>
                        </div>
                        <span class="rounded-full px-3 py-1 text-xs font-black {{ $payment->statusClasses() }}">{{ $payment->statusLabel() }}</span>
                    </div>
                    <p class="mt-2 font-black text-[#0A2A6B]">&#8358;{{ number_format($payment->amount_paid ?? $payment->amount_due) }}</p>
                </a>
            @empty
                <p class="rounded-lg bg-[#F2F2F2] p-6 text-center text-sm font-semibold text-[#2E2E2E]/65">No payment found.</p>
            @endforelse
        </div>

        @if ($payments->hasPages())
            <div class="border-t border-[#0A2A6B]/10 px-5 py-4">{{ $payments->links() }}</div>
        @endif
    </section>
@endsection
