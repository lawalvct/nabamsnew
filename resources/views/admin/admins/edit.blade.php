@extends('layouts.dashboard', ['pageTitle' => 'Edit Admin'])

@section('content')
    @if (session('error'))
        <div class="mb-6 rounded-lg border border-[#F5B400]/40 bg-[#F5B400]/15 px-5 py-4 text-sm font-bold text-[#0A2A6B]">{{ session('error') }}</div>
    @endif

    <section class="mx-auto max-w-5xl rounded-lg bg-white p-6 shadow-sm ring-1 ring-[#0A2A6B]/10 sm:p-8">
        <div class="flex flex-col gap-5 md:flex-row md:items-center md:justify-between">
            <div>
                <p class="text-sm font-black uppercase tracking-wide text-[#F5B400]">Admin Management</p>
                <h1 class="mt-3 text-3xl font-black text-[#0A2A6B]">Edit {{ $admin->name ?: $admin->firstname }}</h1>
                <p class="mt-3 max-w-2xl text-sm leading-6 text-[#2E2E2E]/65">
                    You can update admin contact details, access status, or set a new password.
                </p>
            </div>
            <a href="{{ route('admin.admins.index') }}" class="rounded-lg border border-[#0A2A6B]/20 px-5 py-3 text-sm font-black text-[#0A2A6B] transition hover:border-[#1FA774] hover:text-[#1FA774]">Back to Admins</a>
        </div>

        <form action="{{ route('admin.admins.update', $admin) }}" method="POST" class="mt-8">
            @include('admin.admins._form')
        </form>
    </section>
@endsection
