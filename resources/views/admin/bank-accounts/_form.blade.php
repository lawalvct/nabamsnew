@csrf

@php
    $inputClass = 'rounded-lg border border-[#0A2A6B]/15 bg-white px-4 py-3 font-normal text-[#2E2E2E] outline-none transition focus:border-[#F5B400] focus:ring-4 focus:ring-[#F5B400]/20';
@endphp

<div class="grid gap-5 sm:grid-cols-2">
    <label class="grid gap-2 text-sm font-bold text-[#0A2A6B]">
        Bank Name
        <input name="bank_name" value="{{ old('bank_name', $bankAccount->bank_name) }}" required maxlength="100" placeholder="First Bank of Nigeria" class="{{ $inputClass }}">
        @error('bank_name')
            <span class="text-sm font-bold text-[#F5B400]">{{ $message }}</span>
        @enderror
    </label>

    <label class="grid gap-2 text-sm font-bold text-[#0A2A6B]">
        Account Number
        <input name="account_number" value="{{ old('account_number', $bankAccount->account_number) }}" required inputmode="numeric" maxlength="24" placeholder="0123456789" class="{{ $inputClass }} tracking-wider">
        @error('account_number')
            <span class="text-sm font-bold text-[#F5B400]">{{ $message }}</span>
        @enderror
    </label>

    <label class="grid gap-2 text-sm font-bold text-[#0A2A6B] sm:col-span-2">
        Account Name
        <input name="account_name" value="{{ old('account_name', $bankAccount->account_name) }}" required maxlength="150" placeholder="NABAMS Federal Polytechnic Chapter" class="{{ $inputClass }}">
        @error('account_name')
            <span class="text-sm font-bold text-[#F5B400]">{{ $message }}</span>
        @enderror
    </label>

    <label class="grid gap-2 text-sm font-bold text-[#0A2A6B] sm:col-span-2">
        Payment Instructions <span class="font-normal text-[#2E2E2E]/60">(optional)</span>
        <input name="instructions" value="{{ old('instructions', $bankAccount->instructions) }}" maxlength="255" placeholder="Use your matric number as the transfer narration." class="{{ $inputClass }}">
        @error('instructions')
            <span class="text-sm font-bold text-[#F5B400]">{{ $message }}</span>
        @enderror
    </label>

    <label class="grid gap-2 text-sm font-bold text-[#0A2A6B]">
        Display Order
        <input name="sort_order" type="number" min="0" max="999" value="{{ old('sort_order', $bankAccount->sort_order ?? 0) }}" class="{{ $inputClass }}">
        <span class="text-xs font-normal text-[#2E2E2E]/60">Lower numbers show first to members.</span>
        @error('sort_order')
            <span class="text-sm font-bold text-[#F5B400]">{{ $message }}</span>
        @enderror
    </label>

    <label class="grid gap-2 text-sm font-bold text-[#0A2A6B]">
        Status
        <select name="is_active" class="{{ $inputClass }}">
            <option value="Yes" @selected(old('is_active', $bankAccount->is_active ?? 'Yes') === 'Yes')>Active (visible to members)</option>
            <option value="No" @selected(old('is_active', $bankAccount->is_active ?? 'Yes') === 'No')>Inactive (hidden)</option>
        </select>
        @error('is_active')
            <span class="text-sm font-bold text-[#F5B400]">{{ $message }}</span>
        @enderror
    </label>
</div>

<div class="mt-7 flex flex-col gap-3 sm:flex-row">
    <button type="submit" class="inline-flex justify-center rounded-lg bg-[#1FA774] px-6 py-3 text-sm font-black text-white transition hover:bg-[#198b61]">{{ $buttonLabel }}</button>
    <a href="{{ route('admin.bank-accounts.index') }}" class="inline-flex justify-center rounded-lg border border-[#0A2A6B]/20 px-6 py-3 text-sm font-black text-[#0A2A6B] transition hover:border-[#1FA774] hover:text-[#1FA774]">Cancel</a>
</div>
