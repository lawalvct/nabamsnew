@csrf

@php
    $inputClass = 'rounded-lg border border-[#0A2A6B]/15 bg-white px-4 py-3 font-normal text-[#2E2E2E] outline-none transition focus:border-[#F5B400] focus:ring-4 focus:ring-[#F5B400]/20';
@endphp

<div class="grid gap-5 sm:grid-cols-2">
    <label class="grid gap-2 text-sm font-bold text-[#0A2A6B]">
        Price Name
        <input name="name" value="{{ old('name', $priceSetting->name) }}" required maxlength="100" placeholder="Association Dues" class="{{ $inputClass }}">
        @error('name')
            <span class="text-sm font-bold text-[#F5B400]">{{ $message }}</span>
        @enderror
    </label>

    <label class="grid gap-2 text-sm font-bold text-[#0A2A6B]">
        Amount (&#8358;)
        <input name="amount" type="number" min="0" step="1" value="{{ old('amount', $priceSetting->amount) }}" required placeholder="5000" class="{{ $inputClass }}">
        @error('amount')
            <span class="text-sm font-bold text-[#F5B400]">{{ $message }}</span>
        @enderror
    </label>

    <label class="grid gap-2 text-sm font-bold text-[#0A2A6B]">
        Academic Session
        <select name="academic_session_id" required class="{{ $inputClass }}">
            <option value="">Select session</option>
            @foreach ($academicSessions as $session)
                <option value="{{ $session->id }}" @selected((string) old('academic_session_id', $priceSetting->academic_session_id) === (string) $session->id)>
                    {{ $session->name }}{{ $session->is_current === 'Yes' ? ' (Current)' : '' }}
                </option>
            @endforeach
        </select>
        @error('academic_session_id')
            <span class="text-sm font-bold text-[#F5B400]">{{ $message }}</span>
        @enderror
    </label>

    <label class="grid gap-2 text-sm font-bold text-[#0A2A6B]">
        Level
        <select name="level_id" required class="{{ $inputClass }}">
            <option value="">Select level</option>
            @foreach ($levels as $level)
                <option value="{{ $level->id }}" @selected((string) old('level_id', $priceSetting->level_id) === (string) $level->id)>{{ $level->name }}</option>
            @endforeach
        </select>
        @error('level_id')
            <span class="text-sm font-bold text-[#F5B400]">{{ $message }}</span>
        @enderror
    </label>

    <label class="grid gap-2 text-sm font-bold text-[#0A2A6B]">
        Semester
        <select name="semester" required class="{{ $inputClass }}">
            @foreach ($semesters as $semester)
                <option value="{{ $semester }}" @selected(old('semester', $priceSetting->semester ?? 'First') === $semester)>{{ $semester }} Semester</option>
            @endforeach
        </select>
        @error('semester')
            <span class="text-sm font-bold text-[#F5B400]">{{ $message }}</span>
        @enderror
    </label>

    <label class="grid gap-2 text-sm font-bold text-[#0A2A6B]">
        Status
        <select name="is_active" class="{{ $inputClass }}">
            <option value="Yes" @selected(old('is_active', $priceSetting->is_active ?? 'Yes') === 'Yes')>Active</option>
            <option value="No" @selected(old('is_active', $priceSetting->is_active ?? 'Yes') === 'No')>Inactive</option>
        </select>
        @error('is_active')
            <span class="text-sm font-bold text-[#F5B400]">{{ $message }}</span>
        @enderror
    </label>
</div>

@if ($academicSessions->isEmpty() || $levels->isEmpty())
    <div class="mt-6 rounded-lg border border-[#F5B400]/40 bg-[#F5B400]/15 px-5 py-4 text-sm font-bold text-[#0A2A6B]">
        You need at least one academic session and one level before you can set a price.
    </div>
@endif

<div class="mt-7 flex flex-col gap-3 sm:flex-row">
    <button type="submit" class="inline-flex justify-center rounded-lg bg-[#1FA774] px-6 py-3 text-sm font-black text-white transition hover:bg-[#198b61]">{{ $buttonLabel }}</button>
    <a href="{{ route('admin.price-settings.index') }}" class="inline-flex justify-center rounded-lg border border-[#0A2A6B]/20 px-6 py-3 text-sm font-black text-[#0A2A6B] transition hover:border-[#1FA774] hover:text-[#1FA774]">Cancel</a>
</div>
