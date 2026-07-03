@csrf

<div class="grid gap-5 sm:grid-cols-2">
    <label class="grid gap-2 text-sm font-bold text-[#0A2A6B]">
        Academic Session
        <select name="academic_session_id" required class="rounded-lg border border-[#0A2A6B]/15 bg-white px-4 py-3 font-normal text-[#2E2E2E] outline-none transition focus:border-[#F5B400] focus:ring-4 focus:ring-[#F5B400]/20">
            <option value="">Select session</option>
            @foreach ($academicSessions as $session)
                <option value="{{ $session->id }}" @selected((int) old('academic_session_id', $position->academic_session_id) === $session->id)>{{ $session->name }}</option>
            @endforeach
        </select>
        @error('academic_session_id') <span class="text-xs font-black text-[#F5B400]">{{ $message }}</span> @enderror
    </label>

    <label class="grid gap-2 text-sm font-bold text-[#0A2A6B]">
        Position Name
        <input name="name" value="{{ old('name', $position->name) }}" required class="rounded-lg border border-[#0A2A6B]/15 px-4 py-3 font-normal text-[#2E2E2E] outline-none transition focus:border-[#F5B400] focus:ring-4 focus:ring-[#F5B400]/20" placeholder="President">
        @error('name') <span class="text-xs font-black text-[#F5B400]">{{ $message }}</span> @enderror
    </label>

    <label class="grid gap-2 text-sm font-bold text-[#0A2A6B]">
        Form Amount
        <input name="form_amount" type="number" min="0" value="{{ old('form_amount', $position->form_amount ?? 0) }}" required class="rounded-lg border border-[#0A2A6B]/15 px-4 py-3 font-normal text-[#2E2E2E] outline-none transition focus:border-[#F5B400] focus:ring-4 focus:ring-[#F5B400]/20">
        @error('form_amount') <span class="text-xs font-black text-[#F5B400]">{{ $message }}</span> @enderror
    </label>

    <label class="grid gap-2 text-sm font-bold text-[#0A2A6B]">
        Display Order
        <input name="sort_order" type="number" min="1" value="{{ old('sort_order', $position->sort_order ?? 1) }}" required class="rounded-lg border border-[#0A2A6B]/15 px-4 py-3 font-normal text-[#2E2E2E] outline-none transition focus:border-[#F5B400] focus:ring-4 focus:ring-[#F5B400]/20">
        @error('sort_order') <span class="text-xs font-black text-[#F5B400]">{{ $message }}</span> @enderror
    </label>

    <label class="grid gap-2 text-sm font-bold text-[#0A2A6B] sm:col-span-2">
        Status
        <select name="is_active" class="rounded-lg border border-[#0A2A6B]/15 bg-white px-4 py-3 font-normal text-[#2E2E2E] outline-none transition focus:border-[#F5B400] focus:ring-4 focus:ring-[#F5B400]/20">
            <option value="Yes" @selected(old('is_active', $position->is_active ?? 'Yes') === 'Yes')>Active</option>
            <option value="No" @selected(old('is_active', $position->is_active ?? 'Yes') === 'No')>Inactive</option>
        </select>
    </label>
</div>

<div class="mt-7 flex flex-col gap-3 sm:flex-row">
    <button type="submit" class="rounded-lg bg-[#1FA774] px-6 py-3 text-sm font-black text-white transition hover:bg-[#198b61]">{{ $buttonLabel }}</button>
    <a href="{{ route('admin.election.positions.index') }}" class="rounded-lg border border-[#0A2A6B]/20 px-6 py-3 text-center text-sm font-black text-[#0A2A6B] transition hover:border-[#1FA774] hover:text-[#1FA774]">Cancel</a>
</div>
