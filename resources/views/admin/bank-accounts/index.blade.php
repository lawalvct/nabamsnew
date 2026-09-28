@extends('layouts.dashboard', ['pageTitle' => 'Bank Accounts'])

@section('content')
    <section class="rounded-lg bg-[#0A2A6B] p-6 text-white shadow-xl sm:p-8">
        <div class="flex flex-col gap-5 lg:flex-row lg:items-end lg:justify-between">
            <div>
                <p class="text-sm font-black uppercase tracking-wide text-[#F5B400]">Bank Accounts</p>
                <h1 class="mt-3 text-3xl font-black leading-tight sm:text-4xl">Where members pay</h1>
                <p class="mt-4 max-w-3xl text-sm leading-7 text-[#F2F2F2]/80">
                    Add the bank accounts members should transfer their fees to. Only active accounts are shown to members.
                </p>
            </div>
            <a href="{{ route('admin.bank-accounts.create') }}" class="inline-flex justify-center rounded-lg bg-[#F5B400] px-5 py-3 text-sm font-black text-[#0A2A6B] transition hover:bg-[#ffd15c]">Add Account</a>
        </div>
    </section>

    @if (session('success'))
        <div class="mt-6 rounded-lg border border-[#1FA774]/20 bg-[#1FA774]/10 px-5 py-4 text-sm font-bold text-[#0A2A6B]">
            {{ session('success') }}
        </div>
    @endif

    <section class="mt-6 grid gap-5 md:grid-cols-2">
        <div class="rounded-lg bg-white p-6 shadow-sm ring-1 ring-[#0A2A6B]/10">
            <p class="text-sm font-black uppercase tracking-wide text-[#F5B400]">Total Accounts</p>
            <p class="mt-3 text-3xl font-black text-[#0A2A6B]">{{ $bankAccounts->total() }}</p>
        </div>
        <div class="rounded-lg bg-white p-6 shadow-sm ring-1 ring-[#0A2A6B]/10">
            <p class="text-sm font-black uppercase tracking-wide text-[#F5B400]">Visible to Members</p>
            <p class="mt-3 text-3xl font-black text-[#1FA774]">{{ $activeTotal }}</p>
        </div>
    </section>

    @if ($bankAccounts->total() > 0 && $activeTotal === 0)
        <div class="mt-6 rounded-lg border border-[#F5B400]/40 bg-[#F5B400]/15 px-5 py-4 text-sm font-bold text-[#0A2A6B]">
            No account is active, so members currently have nowhere to pay. Activate at least one account.
        </div>
    @endif

    <section class="mt-6 grid gap-5 md:grid-cols-2 xl:grid-cols-3">
        @forelse ($bankAccounts as $account)
            <article class="flex flex-col rounded-lg bg-white p-6 shadow-sm ring-1 ring-[#0A2A6B]/10 {{ $account->is_active === 'Yes' ? '' : 'opacity-70' }}">
                <div class="flex items-start justify-between gap-3">
                    <div class="grid h-11 w-11 shrink-0 place-items-center rounded-lg bg-[#0A2A6B] text-[#F5B400]">
                        @include('partials.dashboard.menu-icon', ['name' => 'bank-accounts', 'class' => 'h-5 w-5'])
                    </div>
                    <span class="rounded-full px-3 py-1 text-xs font-black {{ $account->is_active === 'Yes' ? 'bg-[#1FA774]/10 text-[#1FA774]' : 'bg-[#F5B400]/20 text-[#0A2A6B]' }}">{{ $account->is_active === 'Yes' ? 'Active' : 'Inactive' }}</span>
                </div>

                <p class="mt-4 text-sm font-black uppercase tracking-wide text-[#2E2E2E]/60">{{ $account->bank_name }}</p>
                <p class="mt-1 font-mono text-2xl font-black tracking-wider text-[#0A2A6B]">{{ $account->account_number }}</p>
                <p class="mt-1 text-sm font-bold text-[#2E2E2E]/80">{{ $account->account_name }}</p>

                @if ($account->instructions)
                    <p class="mt-4 rounded-lg bg-[#F2F2F2] px-3 py-2 text-xs leading-5 text-[#2E2E2E]/75">{{ $account->instructions }}</p>
                @endif

                <div class="mt-auto pt-5">
                    <p class="text-xs font-semibold text-[#2E2E2E]/55">
                        Order {{ $account->sort_order }}
                        @if ($account->updatedBy)
                            &middot; Updated by {{ $account->updatedBy->name ?: $account->updatedBy->firstname }}
                        @endif
                    </p>
                    <div class="mt-3 flex gap-2">
                        <a href="{{ route('admin.bank-accounts.edit', $account) }}" class="rounded-lg border border-[#0A2A6B]/20 px-3 py-2 text-xs font-black text-[#0A2A6B] transition hover:bg-[#0A2A6B] hover:text-white">Edit</a>
                        <form action="{{ route('admin.bank-accounts.destroy', $account) }}" method="POST" onsubmit="return confirm('Delete this bank account?');">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="rounded-lg border border-[#F5B400]/50 px-3 py-2 text-xs font-black text-[#0A2A6B] transition hover:bg-[#F5B400]">Delete</button>
                        </form>
                    </div>
                </div>
            </article>
        @empty
            <div class="rounded-lg bg-white p-8 text-center shadow-sm ring-1 ring-[#0A2A6B]/10 md:col-span-2 xl:col-span-3">
                <p class="text-sm font-black uppercase tracking-wide text-[#F5B400]">No bank account yet</p>
                <h2 class="mt-3 text-2xl font-black text-[#0A2A6B]">Add the account members should pay into.</h2>
                <a href="{{ route('admin.bank-accounts.create') }}" class="mt-5 inline-flex rounded-lg bg-[#1FA774] px-5 py-3 text-sm font-black text-white transition hover:bg-[#198b61]">Add Account</a>
            </div>
        @endforelse
    </section>

    @if ($bankAccounts->hasPages())
        <div class="mt-6">
            {{ $bankAccounts->links() }}
        </div>
    @endif
@endsection
