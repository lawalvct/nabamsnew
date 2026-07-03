@extends('layouts.dashboard', ['pageTitle' => 'Member Details'])

@section('content')
    @if (session('success'))
        <div class="mb-6 rounded-lg border border-[#1FA774]/20 bg-[#1FA774]/10 px-5 py-4 text-sm font-bold text-[#0A2A6B]">{{ session('success') }}</div>
    @endif

    <section class="rounded-lg bg-[#0A2A6B] p-6 text-white shadow-xl sm:p-8">
        <div class="flex flex-col gap-6 md:flex-row md:items-center md:justify-between">
            <div class="flex items-center gap-4">
                <div class="grid h-24 w-24 shrink-0 place-items-center overflow-hidden rounded-lg bg-white text-2xl font-black text-[#0A2A6B]">
                    @if ($member->image_url)
                        <img src="{{ $member->image_url }}" alt="{{ $member->name }}" class="h-full w-full object-cover">
                    @else
                        {{ strtoupper(substr($member->firstname, 0, 1).substr($member->lastname ?? '', 0, 1)) ?: 'NM' }}
                    @endif
                </div>
                <div>
                    <p class="text-sm font-black uppercase tracking-wide text-[#F5B400]">Member Profile</p>
                    <h1 class="mt-2 text-3xl font-black">{{ $member->name ?: $member->firstname }}</h1>
                    <p class="mt-2 text-sm font-semibold text-[#F2F2F2]/75">{{ $member->email }}</p>
                </div>
            </div>
            <a href="{{ route('admin.members.edit', $member) }}" class="inline-flex justify-center rounded-lg bg-[#F5B400] px-5 py-3 text-sm font-black text-[#0A2A6B] transition hover:bg-[#ffd15c]">Edit Member</a>
        </div>
    </section>

    <section class="mt-6 grid gap-5 md:grid-cols-3">
        <div class="rounded-lg bg-white p-5 shadow-sm ring-1 ring-[#0A2A6B]/10">
            <p class="text-sm font-black uppercase tracking-wide text-[#F5B400]">Member Type</p>
            <p class="mt-3 text-2xl font-black text-[#0A2A6B]">{{ $member->member_type }}</p>
        </div>
        <div class="rounded-lg bg-white p-5 shadow-sm ring-1 ring-[#0A2A6B]/10">
            <p class="text-sm font-black uppercase tracking-wide text-[#F5B400]">Active</p>
            <p class="mt-3 text-2xl font-black {{ $member->is_active === 'Yes' ? 'text-[#1FA774]' : 'text-[#0A2A6B]' }}">{{ $member->is_active }}</p>
        </div>
        <div class="rounded-lg bg-white p-5 shadow-sm ring-1 ring-[#0A2A6B]/10">
            <p class="text-sm font-black uppercase tracking-wide text-[#F5B400]">Fee Paid</p>
            <p class="mt-3 text-2xl font-black {{ $member->fee_paid === 'Yes' ? 'text-[#1FA774]' : 'text-[#0A2A6B]' }}">{{ $member->fee_paid }}</p>
        </div>
    </section>

    <section class="mt-6 rounded-lg bg-white p-6 shadow-sm ring-1 ring-[#0A2A6B]/10">
        <h2 class="text-xl font-black text-[#0A2A6B]">Member Information</h2>
        <dl class="mt-5 grid gap-5 md:grid-cols-2">
            @foreach ([
                'Matric Number' => $member->matno,
                'Phone' => $member->phone,
                'WhatsApp' => $member->whatsapp_number ?? 'Not provided',
                'Level' => $member->academic_level ?? 'Not set',
                'Member Type' => $member->member_type,
                'Banned' => $member->is_ban,
                'Department' => $member->department,
                'Date of Birth' => optional($member->dob)->format('M d, Y') ?? 'Not provided',
            ] as $label => $value)
                <div class="rounded-lg bg-[#F2F2F2] p-4">
                    <dt class="text-sm font-bold text-[#2E2E2E]/60">{{ $label }}</dt>
                    <dd class="mt-1 break-words font-black text-[#0A2A6B]">{{ $value }}</dd>
                </div>
            @endforeach
        </dl>
    </section>
@endsection
