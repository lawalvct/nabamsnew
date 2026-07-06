@extends('layouts.dashboard', ['pageTitle' => 'Edit Member'])

@section('content')
    <section class="mx-auto max-w-5xl rounded-lg bg-white p-6 shadow-sm ring-1 ring-[#0A2A6B]/10 sm:p-8">
        <div class="flex flex-col gap-5 md:flex-row md:items-center md:justify-between">
            <div>
                <p class="text-sm font-black uppercase tracking-wide text-[#F5B400]">Members Management</p>
                <h1 class="mt-3 text-3xl font-black text-[#0A2A6B]">Edit {{ $member->name ?: $member->firstname }}</h1>
            </div>
            <a href="{{ route('admin.members.show', $member) }}" class="rounded-lg border border-[#0A2A6B]/20 px-5 py-3 text-sm font-black text-[#0A2A6B] transition hover:border-[#1FA774] hover:text-[#1FA774]">View Profile</a>
        </div>

        <form action="{{ route('admin.members.update', $member) }}" method="POST" class="mt-8">
            @csrf
            @method('PUT')

            <div class="grid gap-5 md:grid-cols-2">
                @foreach ([
                    ['firstname', 'First Name', true],
                    ['lastname', 'Last Name', false],
                    ['nickname', 'Nickname', false],
                    ['email', 'Email', true],
                    ['phone', 'Phone', true],
                    ['whatsapp_number', 'WhatsApp Number', false],
                    ['matno', 'Matric Number', false],
                ] as [$field, $label, $required])
                    <label class="grid gap-2 text-sm font-bold text-[#0A2A6B]">
                        {{ $label }}
                        <input name="{{ $field }}" value="{{ old($field, $member->{$field}) }}" @required($required) @if ($field === 'matno') pattern="(HBAF|NBAF)/(2[1-5][A-Za-z]?)/[0-9]{4}" @endif class="rounded-lg border border-[#0A2A6B]/15 px-4 py-3 font-normal text-[#2E2E2E] outline-none transition focus:border-[#F5B400] focus:ring-4 focus:ring-[#F5B400]/20">
                        @error($field) <span class="text-xs font-black text-[#F5B400]">{{ $message }}</span> @enderror
                    </label>
                @endforeach

                <label class="grid gap-2 text-sm font-bold text-[#0A2A6B]">
                    Gender
                    <select name="gender" class="rounded-lg border border-[#0A2A6B]/15 bg-white px-4 py-3 font-normal text-[#2E2E2E] outline-none transition focus:border-[#F5B400] focus:ring-4 focus:ring-[#F5B400]/20">
                        <option value="">Select gender</option>
                        @foreach (['Male', 'Female', 'Other'] as $gender)
                            <option value="{{ $gender }}" @selected(old('gender', $member->gender) === $gender)>{{ $gender }}</option>
                        @endforeach
                    </select>
                </label>

                <label class="grid gap-2 text-sm font-bold text-[#0A2A6B]">
                    Date Of Birth
                    <input name="dob" type="date" value="{{ old('dob', optional($member->dob)->format('Y-m-d')) }}" class="rounded-lg border border-[#0A2A6B]/15 px-4 py-3 font-normal text-[#2E2E2E] outline-none transition focus:border-[#F5B400] focus:ring-4 focus:ring-[#F5B400]/20">
                    @error('dob') <span class="text-xs font-black text-[#F5B400]">{{ $message }}</span> @enderror
                </label>

                <label class="grid gap-2 text-sm font-bold text-[#0A2A6B]">
                    Level
                    <select name="academic_level" required class="rounded-lg border border-[#0A2A6B]/15 bg-white px-4 py-3 font-normal text-[#2E2E2E] outline-none transition focus:border-[#F5B400] focus:ring-4 focus:ring-[#F5B400]/20">
                        @foreach (['ND1', 'ND2', 'ND3', 'HND1', 'HND2', 'HND3', 'GRADUATE'] as $level)
                            <option value="{{ $level }}" @selected(old('academic_level', $member->academic_level) === $level)>{{ $level }}</option>
                        @endforeach
                    </select>
                </label>

                <label class="grid gap-2 text-sm font-bold text-[#0A2A6B]">
                    Member Type
                    <select name="member_type" required class="rounded-lg border border-[#0A2A6B]/15 bg-white px-4 py-3 font-normal text-[#2E2E2E] outline-none transition focus:border-[#F5B400] focus:ring-4 focus:ring-[#F5B400]/20">
                        @foreach (['Regular', 'Part-time', 'Alumni'] as $type)
                            <option value="{{ $type }}" @selected(old('member_type', $member->member_type) === $type)>{{ $type }}</option>
                        @endforeach
                    </select>
                </label>

                @foreach ([
                    'is_active' => ['Yes', 'No'],
                    'is_ban' => ['No', 'Yes'],
                    'fee_paid' => ['No', 'Yes'],
                ] as $field => $options)
                    <label class="grid gap-2 text-sm font-bold text-[#0A2A6B]">
                        {{ ucwords(str_replace('_', ' ', $field)) }}
                        <select name="{{ $field }}" required class="rounded-lg border border-[#0A2A6B]/15 bg-white px-4 py-3 font-normal text-[#2E2E2E] outline-none transition focus:border-[#F5B400] focus:ring-4 focus:ring-[#F5B400]/20">
                            @foreach ($options as $option)
                                <option value="{{ $option }}" @selected(old($field, $member->{$field}) === $option)>{{ $option }}</option>
                            @endforeach
                        </select>
                    </label>
                @endforeach

                <label class="grid gap-2 text-sm font-bold text-[#0A2A6B] md:col-span-2">
                    Home Address
                    <textarea name="home_address" rows="3" class="rounded-lg border border-[#0A2A6B]/15 px-4 py-3 font-normal text-[#2E2E2E] outline-none transition focus:border-[#F5B400] focus:ring-4 focus:ring-[#F5B400]/20">{{ old('home_address', $member->home_address) }}</textarea>
                </label>

                <label class="grid gap-2 text-sm font-bold text-[#0A2A6B] md:col-span-2">
                    Bio
                    <textarea name="bio" rows="4" class="rounded-lg border border-[#0A2A6B]/15 px-4 py-3 font-normal text-[#2E2E2E] outline-none transition focus:border-[#F5B400] focus:ring-4 focus:ring-[#F5B400]/20">{{ old('bio', $member->bio) }}</textarea>
                </label>
            </div>

            <div class="mt-7 flex flex-col gap-3 sm:flex-row">
                <button type="submit" class="rounded-lg bg-[#1FA774] px-6 py-3 text-sm font-black text-white transition hover:bg-[#198b61]">Save Member</button>
                <a href="{{ route('admin.members.index') }}" class="rounded-lg border border-[#0A2A6B]/20 px-6 py-3 text-center text-sm font-black text-[#0A2A6B] transition hover:border-[#1FA774] hover:text-[#1FA774]">Cancel</a>
            </div>
        </form>
    </section>
@endsection
