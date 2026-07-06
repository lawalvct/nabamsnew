@extends('layouts.dashboard', ['pageTitle' => 'Add Member'])

@section('content')
    <section class="mx-auto max-w-5xl rounded-lg bg-white p-6 shadow-sm ring-1 ring-[#0A2A6B]/10 sm:p-8">
        <div class="flex flex-col gap-5 md:flex-row md:items-center md:justify-between">
            <div>
                <p class="text-sm font-black uppercase tracking-wide text-[#F5B400]">Members Management</p>
                <h1 class="mt-3 text-3xl font-black text-[#0A2A6B]">Add member</h1>
                <p class="mt-3 max-w-2xl text-sm leading-6 text-[#2E2E2E]/65">
                    The member will be created with the default password 12345678. After submission, advise them to change it after their first login.
                </p>
            </div>
            <a href="{{ route('admin.members.index') }}" class="rounded-lg border border-[#0A2A6B]/20 px-5 py-3 text-sm font-black text-[#0A2A6B] transition hover:border-[#1FA774] hover:text-[#1FA774]">Back to Members</a>
        </div>

        <form action="{{ route('admin.members.store') }}" method="POST" class="mt-8">
            @csrf

            <div class="grid gap-5 md:grid-cols-2">
                <label class="grid gap-2 text-sm font-bold text-[#0A2A6B] md:col-span-2">
                    Full Name
                    <input name="full_name" value="{{ old('full_name') }}" required class="rounded-lg border border-[#0A2A6B]/15 px-4 py-3 font-normal text-[#2E2E2E] outline-none transition focus:border-[#F5B400] focus:ring-4 focus:ring-[#F5B400]/20" placeholder="Surname Firstname Othername">
                    @error('full_name') <span class="text-xs font-black text-[#F5B400]">{{ $message }}</span> @enderror
                </label>

                <label class="grid gap-2 text-sm font-bold text-[#0A2A6B]">
                    Gender
                    <select name="gender" required class="rounded-lg border border-[#0A2A6B]/15 bg-white px-4 py-3 font-normal text-[#2E2E2E] outline-none transition focus:border-[#F5B400] focus:ring-4 focus:ring-[#F5B400]/20">
                        <option value="">Select gender</option>
                        @foreach (['Male', 'Female', 'Other'] as $gender)
                            <option value="{{ $gender }}" @selected(old('gender') === $gender)>{{ $gender }}</option>
                        @endforeach
                    </select>
                    @error('gender') <span class="text-xs font-black text-[#F5B400]">{{ $message }}</span> @enderror
                </label>

                <label class="grid gap-2 text-sm font-bold text-[#0A2A6B]">
                    Email Address
                    <input name="email" type="email" value="{{ old('email') }}" required class="rounded-lg border border-[#0A2A6B]/15 px-4 py-3 font-normal text-[#2E2E2E] outline-none transition focus:border-[#F5B400] focus:ring-4 focus:ring-[#F5B400]/20">
                    @error('email') <span class="text-xs font-black text-[#F5B400]">{{ $message }}</span> @enderror
                </label>

                <label class="grid gap-2 text-sm font-bold text-[#0A2A6B]">
                    Phone Number
                    <input name="phone" value="{{ old('phone') }}" required class="rounded-lg border border-[#0A2A6B]/15 px-4 py-3 font-normal text-[#2E2E2E] outline-none transition focus:border-[#F5B400] focus:ring-4 focus:ring-[#F5B400]/20">
                    @error('phone') <span class="text-xs font-black text-[#F5B400]">{{ $message }}</span> @enderror
                </label>

                <label class="grid gap-2 text-sm font-bold text-[#0A2A6B]">
                    Matric Number
                    <input name="matno" value="{{ old('matno') }}" pattern="(HBAF|NBAF)/(2[1-5][A-Za-z]?)/[0-9]{4}" class="rounded-lg border border-[#0A2A6B]/15 px-4 py-3 font-normal uppercase text-[#2E2E2E] outline-none transition focus:border-[#F5B400] focus:ring-4 focus:ring-[#F5B400]/20" placeholder="Optional">
                    @error('matno') <span class="text-xs font-black text-[#F5B400]">{{ $message }}</span> @enderror
                </label>
            </div>

            <p class="mt-5 rounded-lg bg-[#F2F2F2] px-4 py-3 text-sm font-semibold text-[#2E2E2E]/70">
                Default password: <span class="font-black text-[#0A2A6B]">12345678</span>. Tell the member to change it from their dashboard after login.
            </p>

            <div class="mt-7 flex flex-col gap-3 sm:flex-row">
                <button type="submit" class="rounded-lg bg-[#1FA774] px-6 py-3 text-sm font-black text-white transition hover:bg-[#198b61]">Create Member</button>
                <a href="{{ route('admin.members.index') }}" class="rounded-lg border border-[#0A2A6B]/20 px-6 py-3 text-center text-sm font-black text-[#0A2A6B] transition hover:border-[#1FA774] hover:text-[#1FA774]">Cancel</a>
            </div>
        </form>
    </section>
@endsection
