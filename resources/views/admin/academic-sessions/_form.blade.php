@csrf

<div class="grid gap-5 sm:grid-cols-2">
    <label class="grid gap-2 text-sm font-bold text-[#0A2A6B]">
        Session Name
        <input name="name" value="{{ old('name', $academicSession->name) }}" required placeholder="2025-2026" class="rounded-lg border border-[#0A2A6B]/15 bg-white px-4 py-3 font-normal text-[#2E2E2E] outline-none transition focus:border-[#F5B400] focus:ring-4 focus:ring-[#F5B400]/20">
        @error('name')
            <span class="text-sm font-bold text-[#F5B400]">{{ $message }}</span>
        @enderror
    </label>

    <label class="grid gap-2 text-sm font-bold text-[#0A2A6B]">
        Status
        <select name="is_active" class="rounded-lg border border-[#0A2A6B]/15 bg-white px-4 py-3 font-normal text-[#2E2E2E] outline-none transition focus:border-[#F5B400] focus:ring-4 focus:ring-[#F5B400]/20">
            <option value="Yes" @selected(old('is_active', $academicSession->is_active ?? 'Yes') === 'Yes')>Active</option>
            <option value="No" @selected(old('is_active', $academicSession->is_active ?? 'Yes') === 'No')>Inactive</option>
        </select>
        @error('is_active')
            <span class="text-sm font-bold text-[#F5B400]">{{ $message }}</span>
        @enderror
    </label>

    <label class="grid gap-2 text-sm font-bold text-[#0A2A6B]">
        Start Year
        <input name="starts_at_year" type="number" value="{{ old('starts_at_year', $academicSession->starts_at_year) }}" required placeholder="2025" class="rounded-lg border border-[#0A2A6B]/15 bg-white px-4 py-3 font-normal text-[#2E2E2E] outline-none transition focus:border-[#F5B400] focus:ring-4 focus:ring-[#F5B400]/20">
        @error('starts_at_year')
            <span class="text-sm font-bold text-[#F5B400]">{{ $message }}</span>
        @enderror
    </label>

    <label class="grid gap-2 text-sm font-bold text-[#0A2A6B]">
        End Year
        <input name="ends_at_year" type="number" value="{{ old('ends_at_year', $academicSession->ends_at_year) }}" required placeholder="2026" class="rounded-lg border border-[#0A2A6B]/15 bg-white px-4 py-3 font-normal text-[#2E2E2E] outline-none transition focus:border-[#F5B400] focus:ring-4 focus:ring-[#F5B400]/20">
        @error('ends_at_year')
            <span class="text-sm font-bold text-[#F5B400]">{{ $message }}</span>
        @enderror
    </label>
</div>

@php
    $currentChecked = old('is_current') !== null
        ? old('is_current') === '1'
        : ($academicSession->is_current === 'Yes' || ! $hasCurrentSession);
@endphp

<label class="mt-6 flex items-start gap-3 rounded-lg border border-[#1FA774]/20 bg-[#1FA774]/10 p-4 text-sm font-bold text-[#0A2A6B]">
    <input name="is_current" type="checkbox" value="1" @checked($currentChecked) class="mt-1 h-4 w-4 rounded border-[#0A2A6B]/20 text-[#1FA774]">
    <span>
        Set as current active session
        <span class="mt-1 block font-normal leading-6 text-[#2E2E2E]/70">If selected, every other academic session will automatically stop being current.</span>
    </span>
</label>

<div class="mt-7 flex flex-col gap-3 sm:flex-row">
    <button type="submit" class="inline-flex justify-center rounded-lg bg-[#1FA774] px-6 py-3 text-sm font-black text-white transition hover:bg-[#198b61]">{{ $buttonLabel }}</button>
    <a href="{{ route('admin.academic-sessions.index') }}" class="inline-flex justify-center rounded-lg border border-[#0A2A6B]/20 px-6 py-3 text-sm font-black text-[#0A2A6B] transition hover:border-[#1FA774] hover:text-[#1FA774]">Cancel</a>
</div>
