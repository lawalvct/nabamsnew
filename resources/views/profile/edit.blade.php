@extends('layouts.dashboard', ['pageTitle' => 'Profile'])

@section('content')
    <section class="rounded-lg bg-[#0A2A6B] p-6 text-white shadow-xl sm:p-8">
        <div class="flex flex-col gap-6 lg:flex-row lg:items-center lg:justify-between">
            <div>
                <p class="text-sm font-black uppercase tracking-wide text-[#F5B400]">Profile</p>
                <h1 class="mt-3 text-3xl font-black sm:text-4xl">Manage your NABAMS profile</h1>
                <p class="mt-4 max-w-3xl text-sm leading-7 text-[#F2F2F2]/80">
                    Keep your contact, academic, social, and security details up to date. Profile photos are validated before storage.
                </p>
            </div>
            <div class="grid h-24 w-24 shrink-0 place-items-center overflow-hidden rounded-lg bg-white text-2xl font-black text-[#0A2A6B]">
                @if ($user->image_url)
                    <img src="{{ $user->image_url }}" alt="{{ $displayName }}" class="h-full w-full object-cover">
                @else
                    {{ $initials ?: 'NM' }}
                @endif
            </div>
        </div>
    </section>

    @if (session('success'))
        <div class="mt-6 rounded-lg border border-[#1FA774]/20 bg-[#1FA774]/10 px-5 py-4 text-sm font-bold text-[#0A2A6B]">{{ session('success') }}</div>
    @endif

    @if (session('password_success'))
        <div class="mt-6 rounded-lg border border-[#1FA774]/20 bg-[#1FA774]/10 px-5 py-4 text-sm font-bold text-[#0A2A6B]">{{ session('password_success') }}</div>
    @endif

    <div class="mt-6 grid gap-6 xl:grid-cols-[1.25fr_0.75fr]">
        <section class="rounded-lg bg-white p-6 shadow-sm ring-1 ring-[#0A2A6B]/10 sm:p-8">
            <p class="text-sm font-black uppercase tracking-wide text-[#F5B400]">Personal And Academic Details</p>
            <h2 class="mt-2 text-2xl font-black text-[#0A2A6B]">Edit profile</h2>

            <form action="{{ route('profile.update') }}" method="POST" enctype="multipart/form-data" class="mt-7">
                @csrf
                @method('PUT')

                <div class="grid gap-5 md:grid-cols-2">
                    <label class="grid gap-2 text-sm font-bold text-[#0A2A6B]">
                        First Name
                        <input name="firstname" value="{{ old('firstname', $user->firstname) }}" required maxlength="50" class="rounded-lg border border-[#0A2A6B]/15 px-4 py-3 font-normal text-[#2E2E2E] outline-none transition focus:border-[#F5B400] focus:ring-4 focus:ring-[#F5B400]/20">
                        @error('firstname') <span class="text-xs font-black text-[#F5B400]">{{ $message }}</span> @enderror
                    </label>

                    <label class="grid gap-2 text-sm font-bold text-[#0A2A6B]">
                        Last Name
                        <input name="lastname" value="{{ old('lastname', $user->lastname) }}" maxlength="50" class="rounded-lg border border-[#0A2A6B]/15 px-4 py-3 font-normal text-[#2E2E2E] outline-none transition focus:border-[#F5B400] focus:ring-4 focus:ring-[#F5B400]/20">
                        @error('lastname') <span class="text-xs font-black text-[#F5B400]">{{ $message }}</span> @enderror
                    </label>

                    <label class="grid gap-2 text-sm font-bold text-[#0A2A6B]">
                        Nickname
                        <input name="nickname" value="{{ old('nickname', $user->nickname) }}" maxlength="50" class="rounded-lg border border-[#0A2A6B]/15 px-4 py-3 font-normal text-[#2E2E2E] outline-none transition focus:border-[#F5B400] focus:ring-4 focus:ring-[#F5B400]/20">
                        @error('nickname') <span class="text-xs font-black text-[#F5B400]">{{ $message }}</span> @enderror
                    </label>

                    <label class="grid gap-2 text-sm font-bold text-[#0A2A6B]">
                        Gender
                        <select name="gender" class="rounded-lg border border-[#0A2A6B]/15 bg-white px-4 py-3 font-normal text-[#2E2E2E] outline-none transition focus:border-[#F5B400] focus:ring-4 focus:ring-[#F5B400]/20">
                            <option value="">Select gender</option>
                            @foreach (['Male', 'Female', 'Other'] as $gender)
                                <option value="{{ $gender }}" @selected(old('gender', $user->gender) === $gender)>{{ $gender }}</option>
                            @endforeach
                        </select>
                        @error('gender') <span class="text-xs font-black text-[#F5B400]">{{ $message }}</span> @enderror
                    </label>

                    <label class="grid gap-2 text-sm font-bold text-[#0A2A6B]">
                        Date Of Birth
                        <input name="dob" type="date" value="{{ old('dob', optional($user->dob)->format('Y-m-d')) }}" class="rounded-lg border border-[#0A2A6B]/15 px-4 py-3 font-normal text-[#2E2E2E] outline-none transition focus:border-[#F5B400] focus:ring-4 focus:ring-[#F5B400]/20">
                        @error('dob') <span class="text-xs font-black text-[#F5B400]">{{ $message }}</span> @enderror
                    </label>

                    <label class="grid gap-2 text-sm font-bold text-[#0A2A6B]">
                        Email
                        <input name="email" type="email" value="{{ old('email', $user->email) }}" required maxlength="60" class="rounded-lg border border-[#0A2A6B]/15 px-4 py-3 font-normal text-[#2E2E2E] outline-none transition focus:border-[#F5B400] focus:ring-4 focus:ring-[#F5B400]/20">
                        @error('email') <span class="text-xs font-black text-[#F5B400]">{{ $message }}</span> @enderror
                    </label>

                    <label class="grid gap-2 text-sm font-bold text-[#0A2A6B]">
                        Phone
                        <input name="phone" value="{{ old('phone', $user->phone) }}" required maxlength="30" class="rounded-lg border border-[#0A2A6B]/15 px-4 py-3 font-normal text-[#2E2E2E] outline-none transition focus:border-[#F5B400] focus:ring-4 focus:ring-[#F5B400]/20">
                        @error('phone') <span class="text-xs font-black text-[#F5B400]">{{ $message }}</span> @enderror
                    </label>

                    <label class="grid gap-2 text-sm font-bold text-[#0A2A6B]">
                        WhatsApp Number
                        <input name="whatsapp_number" value="{{ old('whatsapp_number', $user->whatsapp_number) }}" maxlength="30" class="rounded-lg border border-[#0A2A6B]/15 px-4 py-3 font-normal text-[#2E2E2E] outline-none transition focus:border-[#F5B400] focus:ring-4 focus:ring-[#F5B400]/20">
                        @error('whatsapp_number') <span class="text-xs font-black text-[#F5B400]">{{ $message }}</span> @enderror
                    </label>

                    <label class="grid gap-2 text-sm font-bold text-[#0A2A6B]">
                        Matric Number
                        <input name="matno" value="{{ old('matno', $user->matno) }}" required pattern="(HBAF|NBAF)/(2[1-5])/[0-9]{4}" class="rounded-lg border border-[#0A2A6B]/15 px-4 py-3 font-normal uppercase text-[#2E2E2E] outline-none transition focus:border-[#F5B400] focus:ring-4 focus:ring-[#F5B400]/20">
                        @error('matno') <span class="text-xs font-black text-[#F5B400]">{{ $message }}</span> @enderror
                    </label>

                    <label class="grid gap-2 text-sm font-bold text-[#0A2A6B]">
                        Level
                        <select name="academic_level" required class="rounded-lg border border-[#0A2A6B]/15 bg-white px-4 py-3 font-normal text-[#2E2E2E] outline-none transition focus:border-[#F5B400] focus:ring-4 focus:ring-[#F5B400]/20">
                            @foreach (['ND1', 'ND2', 'ND3', 'HND1', 'HND2', 'HND3', 'GRADUATE'] as $level)
                                <option value="{{ $level }}" @selected(old('academic_level', $user->academic_level) === $level)>{{ $level }}</option>
                            @endforeach
                        </select>
                        @error('academic_level') <span class="text-xs font-black text-[#F5B400]">{{ $message }}</span> @enderror
                    </label>

                    <label class="grid gap-2 text-sm font-bold text-[#0A2A6B]">
                        Member Type
                        <select name="member_type" required class="rounded-lg border border-[#0A2A6B]/15 bg-white px-4 py-3 font-normal text-[#2E2E2E] outline-none transition focus:border-[#F5B400] focus:ring-4 focus:ring-[#F5B400]/20">
                            @foreach (['Regular', 'Part-time', 'Alumni'] as $type)
                                <option value="{{ $type }}" @selected(old('member_type', $user->member_type) === $type)>{{ $type }}</option>
                            @endforeach
                        </select>
                        @error('member_type') <span class="text-xs font-black text-[#F5B400]">{{ $message }}</span> @enderror
                    </label>

                    <label class="grid gap-2 text-sm font-bold text-[#0A2A6B] md:col-span-2">
                        Profile Photo
                        <input name="profile_photo" type="file" accept="image/jpeg,image/png,image/webp" class="rounded-lg border border-[#0A2A6B]/15 bg-white px-4 py-3 font-normal text-[#2E2E2E] outline-none transition file:mr-4 file:rounded-lg file:border-0 file:bg-[#0A2A6B] file:px-4 file:py-2 file:text-sm file:font-black file:text-white focus:border-[#F5B400] focus:ring-4 focus:ring-[#F5B400]/20">
                        <span class="text-xs font-semibold text-[#2E2E2E]/60">JPEG, PNG or WEBP only. Maximum 2MB and 2400px by 2400px.</span>
                        @error('profile_photo') <span class="text-xs font-black text-[#F5B400]">{{ $message }}</span> @enderror
                    </label>

                    <label class="grid gap-2 text-sm font-bold text-[#0A2A6B] md:col-span-2">
                        Home Address
                        <textarea name="home_address" rows="3" maxlength="1000" class="rounded-lg border border-[#0A2A6B]/15 px-4 py-3 font-normal text-[#2E2E2E] outline-none transition focus:border-[#F5B400] focus:ring-4 focus:ring-[#F5B400]/20">{{ old('home_address', $user->home_address) }}</textarea>
                        @error('home_address') <span class="text-xs font-black text-[#F5B400]">{{ $message }}</span> @enderror
                    </label>

                    <label class="grid gap-2 text-sm font-bold text-[#0A2A6B] md:col-span-2">
                        Bio
                        <textarea name="bio" rows="4" maxlength="1500" class="rounded-lg border border-[#0A2A6B]/15 px-4 py-3 font-normal text-[#2E2E2E] outline-none transition focus:border-[#F5B400] focus:ring-4 focus:ring-[#F5B400]/20">{{ old('bio', $user->bio) }}</textarea>
                        @error('bio') <span class="text-xs font-black text-[#F5B400]">{{ $message }}</span> @enderror
                    </label>
                </div>

                <div class="mt-5 grid gap-5 md:grid-cols-3">
                    <label class="grid gap-2 text-sm font-bold text-[#0A2A6B]">
                        Facebook URL
                        <input name="facebook_link" type="url" value="{{ old('facebook_link', $user->facebook_link) }}" maxlength="100" class="rounded-lg border border-[#0A2A6B]/15 px-4 py-3 font-normal text-[#2E2E2E] outline-none transition focus:border-[#F5B400] focus:ring-4 focus:ring-[#F5B400]/20">
                        @error('facebook_link') <span class="text-xs font-black text-[#F5B400]">{{ $message }}</span> @enderror
                    </label>

                    <label class="grid gap-2 text-sm font-bold text-[#0A2A6B]">
                        X URL
                        <input name="x_link" type="url" value="{{ old('x_link', $user->x_link) }}" maxlength="100" class="rounded-lg border border-[#0A2A6B]/15 px-4 py-3 font-normal text-[#2E2E2E] outline-none transition focus:border-[#F5B400] focus:ring-4 focus:ring-[#F5B400]/20">
                        @error('x_link') <span class="text-xs font-black text-[#F5B400]">{{ $message }}</span> @enderror
                    </label>

                    <label class="grid gap-2 text-sm font-bold text-[#0A2A6B]">
                        LinkedIn URL
                        <input name="linkedin_link" type="url" value="{{ old('linkedin_link', $user->linkedin_link) }}" maxlength="100" class="rounded-lg border border-[#0A2A6B]/15 px-4 py-3 font-normal text-[#2E2E2E] outline-none transition focus:border-[#F5B400] focus:ring-4 focus:ring-[#F5B400]/20">
                        @error('linkedin_link') <span class="text-xs font-black text-[#F5B400]">{{ $message }}</span> @enderror
                    </label>
                </div>

                <button type="submit" class="mt-7 rounded-lg bg-[#1FA774] px-6 py-3 text-sm font-black text-white transition hover:bg-[#198b61]">Save Profile</button>
            </form>
        </section>

        <aside class="space-y-6">
            <section class="rounded-lg bg-white p-6 shadow-sm ring-1 ring-[#0A2A6B]/10">
                <p class="text-sm font-black uppercase tracking-wide text-[#F5B400]">Security</p>
                <h2 class="mt-2 text-2xl font-black text-[#0A2A6B]">Change password</h2>

                <form action="{{ route('profile.password.update') }}" method="POST" class="mt-6 grid gap-5">
                    @csrf
                    @method('PUT')

                    <label class="grid gap-2 text-sm font-bold text-[#0A2A6B]">
                        Current Password
                        <input name="current_password" type="password" required autocomplete="current-password" class="rounded-lg border border-[#0A2A6B]/15 px-4 py-3 font-normal text-[#2E2E2E] outline-none transition focus:border-[#F5B400] focus:ring-4 focus:ring-[#F5B400]/20">
                        @error('current_password') <span class="text-xs font-black text-[#F5B400]">{{ $message }}</span> @enderror
                    </label>

                    <label class="grid gap-2 text-sm font-bold text-[#0A2A6B]">
                        New Password
                        <input name="password" type="password" required autocomplete="new-password" class="rounded-lg border border-[#0A2A6B]/15 px-4 py-3 font-normal text-[#2E2E2E] outline-none transition focus:border-[#F5B400] focus:ring-4 focus:ring-[#F5B400]/20">
                        @error('password') <span class="text-xs font-black text-[#F5B400]">{{ $message }}</span> @enderror
                    </label>

                    <label class="grid gap-2 text-sm font-bold text-[#0A2A6B]">
                        Confirm New Password
                        <input name="password_confirmation" type="password" required autocomplete="new-password" class="rounded-lg border border-[#0A2A6B]/15 px-4 py-3 font-normal text-[#2E2E2E] outline-none transition focus:border-[#F5B400] focus:ring-4 focus:ring-[#F5B400]/20">
                    </label>

                    <button type="submit" class="rounded-lg bg-[#0A2A6B] px-6 py-3 text-sm font-black text-white transition hover:bg-[#123b8d]">Update Password</button>
                </form>
            </section>

            <section class="rounded-lg bg-[#0A2A6B] p-6 text-white shadow-xl">
                <p class="text-sm font-black uppercase tracking-wide text-[#F5B400]">Account Snapshot</p>
                <dl class="mt-5 grid gap-4 text-sm">
                    <div class="flex items-center justify-between gap-4 border-b border-white/10 pb-3">
                        <dt class="text-[#F2F2F2]/70">Role</dt>
                        <dd class="font-black">{{ $user->role }}</dd>
                    </div>
                    <div class="flex items-center justify-between gap-4 border-b border-white/10 pb-3">
                        <dt class="text-[#F2F2F2]/70">Fee Paid</dt>
                        <dd class="font-black text-[#F5B400]">{{ $user->fee_paid }}</dd>
                    </div>
                    <div class="flex items-center justify-between gap-4">
                        <dt class="text-[#F2F2F2]/70">Active</dt>
                        <dd class="font-black text-[#F5B400]">{{ $user->is_active }}</dd>
                    </div>
                </dl>
            </section>
        </aside>
    </div>
@endsection
