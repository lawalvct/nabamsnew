@extends('layouts.dashboard', ['pageTitle' => 'Record Payment'])

@php
    $inputClass = 'rounded-lg border border-[#0A2A6B]/15 bg-white px-4 py-3 font-normal text-[#2E2E2E] outline-none transition focus:border-[#F5B400] focus:ring-4 focus:ring-[#F5B400]/20';
@endphp

@section('content')
    <a href="{{ route('admin.payments.index') }}" class="text-sm font-black text-[#0A2A6B] hover:text-[#1FA774]">&larr; Back to transactions</a>

    <section class="mx-auto mt-4 max-w-4xl rounded-lg bg-white p-6 shadow-sm ring-1 ring-[#0A2A6B]/10 sm:p-8">
        <p class="text-sm font-black uppercase tracking-wide text-[#F5B400]">Transactions</p>
        <h1 class="mt-3 text-3xl font-black text-[#0A2A6B]">Record a payment</h1>
        <p class="mt-4 text-sm leading-7 text-[#2E2E2E]/70">
            For cash, POS or transfers confirmed outside the portal. The payment is approved immediately in your name, the member's dashboard is unlocked, and they can download the receipt.
            Items and amount due are taken from Price Settings for the member's level.
        </p>

        <form action="{{ route('admin.payments.store') }}" method="POST" enctype="multipart/form-data" class="mt-8 grid gap-5 sm:grid-cols-2">
            @csrf

            <label class="grid gap-2 text-sm font-bold text-[#0A2A6B] sm:col-span-2">
                Member (matric number or email)
                <input name="member" required maxlength="150" value="{{ old('member', $member?->matno ?: $member?->email) }}" placeholder="HBAF/23/0001 or member@example.com" class="{{ $inputClass }}">
                @if ($member)
                    <span class="text-xs font-semibold text-[#1FA774]">{{ $member->name }} &middot; {{ $member->academic_level }}</span>
                @endif
                @error('member')<span class="text-sm font-bold text-red-600">{{ $message }}</span>@enderror
            </label>

            <label class="grid gap-2 text-sm font-bold text-[#0A2A6B]">
                Academic Session
                <select name="academic_session_id" required class="{{ $inputClass }}">
                    @foreach ($academicSessions as $session)
                        <option value="{{ $session->id }}" @selected((string) old('academic_session_id', $currentSession?->id) === (string) $session->id)>{{ $session->name }}{{ $session->is_current === 'Yes' ? ' (Current)' : '' }}</option>
                    @endforeach
                </select>
                @error('academic_session_id')<span class="text-sm font-bold text-red-600">{{ $message }}</span>@enderror
            </label>

            <label class="grid gap-2 text-sm font-bold text-[#0A2A6B]">
                Period covered
                <select name="period" required class="{{ $inputClass }}">
                    @foreach (['First' => 'First Semester', 'Second' => 'Second Semester', 'Full' => 'Full Session (both semesters)'] as $value => $label)
                        <option value="{{ $value }}" @selected(old('period', $defaultPeriod) === $value)>{{ $label }}</option>
                    @endforeach
                </select>
                @error('period')<span class="text-sm font-bold text-red-600">{{ $message }}</span>@enderror
            </label>

            <label class="grid gap-2 text-sm font-bold text-[#0A2A6B]">
                Payment method
                <select name="payment_method" required class="{{ $inputClass }}">
                    @foreach (\App\Models\Payment::METHODS as $value => $label)
                        <option value="{{ $value }}" @selected(old('payment_method', 'cash') === $value)>{{ $label }}</option>
                    @endforeach
                </select>
                @error('payment_method')<span class="text-sm font-bold text-red-600">{{ $message }}</span>@enderror
            </label>

            <label class="grid gap-2 text-sm font-bold text-[#0A2A6B]">
                Paid into (for transfers)
                <select name="bank_account_id" class="{{ $inputClass }}">
                    <option value="">Not applicable</option>
                    @foreach ($bankAccounts as $account)
                        <option value="{{ $account->id }}" @selected((string) old('bank_account_id') === (string) $account->id)>{{ $account->bank_name }} - {{ $account->account_number }}</option>
                    @endforeach
                </select>
                @error('bank_account_id')<span class="text-sm font-bold text-red-600">{{ $message }}</span>@enderror
            </label>

            <label class="grid gap-2 text-sm font-bold text-[#0A2A6B]">
                Amount received (&#8358;)
                <input name="amount_paid" type="number" min="0" required value="{{ old('amount_paid') }}" class="{{ $inputClass }}">
                @error('amount_paid')<span class="text-sm font-bold text-red-600">{{ $message }}</span>@enderror
            </label>

            <label class="grid gap-2 text-sm font-bold text-[#0A2A6B]">
                Date received
                <input name="paid_at" type="date" required max="{{ now()->toDateString() }}" value="{{ old('paid_at', now()->toDateString()) }}" class="{{ $inputClass }}">
                @error('paid_at')<span class="text-sm font-bold text-red-600">{{ $message }}</span>@enderror
            </label>

            <label class="grid gap-2 text-sm font-bold text-[#0A2A6B] sm:col-span-2">
                Note (optional, admins only)
                <input name="admin_note" maxlength="255" value="{{ old('admin_note') }}" placeholder="e.g. Paid cash to the Financial Secretary at the general meeting" class="{{ $inputClass }}">
                @error('admin_note')<span class="text-sm font-bold text-red-600">{{ $message }}</span>@enderror
            </label>

            <label class="grid gap-2 text-sm font-bold text-[#0A2A6B] sm:col-span-2">
                Evidence (optional, JPG/PNG/WEBP/PDF, max 5MB)
                <input name="evidence" type="file" accept=".jpg,.jpeg,.png,.webp,.pdf" class="rounded-lg border border-dashed border-[#0A2A6B]/30 bg-[#F2F2F2] px-4 py-4 text-sm font-normal file:mr-4 file:rounded-lg file:border-0 file:bg-[#0A2A6B] file:px-4 file:py-2 file:text-sm file:font-black file:text-white">
                @error('evidence')<span class="text-sm font-bold text-red-600">{{ $message }}</span>@enderror
            </label>

            <div class="flex flex-col gap-3 sm:col-span-2 sm:flex-row">
                <button type="submit" class="inline-flex justify-center rounded-lg bg-[#1FA774] px-6 py-3 text-sm font-black text-white transition hover:bg-[#198b61]">Record &amp; Approve</button>
                <a href="{{ route('admin.payments.index') }}" class="inline-flex justify-center rounded-lg border border-[#0A2A6B]/20 px-6 py-3 text-sm font-black text-[#0A2A6B] transition hover:border-[#1FA774] hover:text-[#1FA774]">Cancel</a>
            </div>
        </form>
    </section>
@endsection
