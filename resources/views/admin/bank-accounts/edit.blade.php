@extends('layouts.dashboard', ['pageTitle' => 'Edit Bank Account'])

@section('content')
    <section class="mx-auto max-w-4xl rounded-lg bg-white p-6 shadow-sm ring-1 ring-[#0A2A6B]/10 sm:p-8">
        <p class="text-sm font-black uppercase tracking-wide text-[#F5B400]">Bank Accounts</p>
        <h1 class="mt-3 text-3xl font-black text-[#0A2A6B]">Edit {{ $bankAccount->bank_name }}</h1>
        <p class="mt-4 text-sm leading-7 text-[#2E2E2E]/70">Changes show to members immediately. Double-check the account number before saving.</p>

        <form action="{{ route('admin.bank-accounts.update', $bankAccount) }}" method="POST" class="mt-8">
            @method('PUT')
            @include('admin.bank-accounts._form', ['buttonLabel' => 'Save Changes'])
        </form>
    </section>
@endsection
