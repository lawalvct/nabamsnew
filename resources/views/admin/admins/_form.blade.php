@csrf

@if ($admin->exists)
    @method('PUT')
@endif

<div class="grid gap-5 md:grid-cols-2">
    @foreach ([
        ['firstname', 'First Name', true],
        ['lastname', 'Last Name', false],
        ['nickname', 'Nickname', false],
        ['email', 'Email Address', true],
        ['phone', 'Phone Number', true],
        ['matno', 'Admin ID / Matric Number', true],
    ] as [$field, $label, $required])
        <label class="grid gap-2 text-sm font-bold text-[#0A2A6B]">
            {{ $label }}
            <input name="{{ $field }}" value="{{ old($field, $admin->{$field}) }}" @required($required) class="rounded-lg border border-[#0A2A6B]/15 px-4 py-3 font-normal text-[#2E2E2E] outline-none transition focus:border-[#F5B400] focus:ring-4 focus:ring-[#F5B400]/20">
            @error($field) <span class="text-xs font-black text-[#F5B400]">{{ $message }}</span> @enderror
        </label>
    @endforeach

    <label class="grid gap-2 text-sm font-bold text-[#0A2A6B]">
        Active
        <select name="is_active" required class="rounded-lg border border-[#0A2A6B]/15 bg-white px-4 py-3 font-normal text-[#2E2E2E] outline-none transition focus:border-[#F5B400] focus:ring-4 focus:ring-[#F5B400]/20">
            @foreach (['Yes', 'No'] as $option)
                <option value="{{ $option }}" @selected(old('is_active', $admin->is_active ?? 'Yes') === $option)>{{ $option }}</option>
            @endforeach
        </select>
        @error('is_active') <span class="text-xs font-black text-[#F5B400]">{{ $message }}</span> @enderror
    </label>

    <label class="grid gap-2 text-sm font-bold text-[#0A2A6B]">
        Banned
        <select name="is_ban" required class="rounded-lg border border-[#0A2A6B]/15 bg-white px-4 py-3 font-normal text-[#2E2E2E] outline-none transition focus:border-[#F5B400] focus:ring-4 focus:ring-[#F5B400]/20">
            @foreach (['No', 'Yes'] as $option)
                <option value="{{ $option }}" @selected(old('is_ban', $admin->is_ban ?? 'No') === $option)>{{ $option }}</option>
            @endforeach
        </select>
        @error('is_ban') <span class="text-xs font-black text-[#F5B400]">{{ $message }}</span> @enderror
    </label>

    <label class="grid gap-2 text-sm font-bold text-[#0A2A6B]">
        {{ $admin->exists ? 'New Password' : 'Password' }}
        <input name="password" type="password" @required(! $admin->exists) autocomplete="new-password" class="rounded-lg border border-[#0A2A6B]/15 px-4 py-3 font-normal text-[#2E2E2E] outline-none transition focus:border-[#F5B400] focus:ring-4 focus:ring-[#F5B400]/20">
        @error('password') <span class="text-xs font-black text-[#F5B400]">{{ $message }}</span> @enderror
    </label>

    <label class="grid gap-2 text-sm font-bold text-[#0A2A6B]">
        Confirm Password
        <input name="password_confirmation" type="password" @required(! $admin->exists) autocomplete="new-password" class="rounded-lg border border-[#0A2A6B]/15 px-4 py-3 font-normal text-[#2E2E2E] outline-none transition focus:border-[#F5B400] focus:ring-4 focus:ring-[#F5B400]/20">
    </label>
</div>

@if ($admin->exists)
    <p class="mt-4 rounded-lg bg-[#F2F2F2] px-4 py-3 text-sm font-semibold text-[#2E2E2E]/70">
        Leave password fields empty to keep the current password. Role is fixed as Admin on this page.
    </p>
@endif

<div class="mt-7 flex flex-col gap-3 sm:flex-row">
    <button type="submit" class="rounded-lg bg-[#1FA774] px-6 py-3 text-sm font-black text-white transition hover:bg-[#198b61]">{{ $admin->exists ? 'Save Admin' : 'Create Admin' }}</button>
    <a href="{{ route('admin.admins.index') }}" class="rounded-lg border border-[#0A2A6B]/20 px-6 py-3 text-center text-sm font-black text-[#0A2A6B] transition hover:border-[#1FA774] hover:text-[#1FA774]">Cancel</a>
</div>
