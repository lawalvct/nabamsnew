@csrf

<div class="grid gap-5 sm:grid-cols-2">
    <label class="grid gap-2 text-sm font-bold text-[#0A2A6B]">
        Registered Member
        <select name="user_id" required class="rounded-lg border border-[#0A2A6B]/15 bg-white px-4 py-3 font-normal text-[#2E2E2E] outline-none transition focus:border-[#F5B400] focus:ring-4 focus:ring-[#F5B400]/20">
            <option value="">Select member</option>
            @foreach ($members as $member)
                <option value="{{ $member->id }}" @selected((int) old('user_id', $aspirant->user_id) === $member->id)>{{ $member->name }} · {{ $member->matno ?? $member->email }}</option>
            @endforeach
        </select>
        @error('user_id') <span class="text-xs font-black text-[#F5B400]">{{ $message }}</span> @enderror
    </label>

    <label class="grid gap-2 text-sm font-bold text-[#0A2A6B]">
        Academic Session
        <select name="academic_session_id" required class="rounded-lg border border-[#0A2A6B]/15 bg-white px-4 py-3 font-normal text-[#2E2E2E] outline-none transition focus:border-[#F5B400] focus:ring-4 focus:ring-[#F5B400]/20">
            <option value="">Select session</option>
            @foreach ($academicSessions as $session)
                <option value="{{ $session->id }}" @selected((int) old('academic_session_id', $aspirant->academic_session_id) === $session->id)>{{ $session->name }}</option>
            @endforeach
        </select>
        @error('academic_session_id') <span class="text-xs font-black text-[#F5B400]">{{ $message }}</span> @enderror
    </label>

    <label class="grid gap-2 text-sm font-bold text-[#0A2A6B]">
        Display Name
        <input name="name" value="{{ old('name', $aspirant->name) }}" class="rounded-lg border border-[#0A2A6B]/15 px-4 py-3 font-normal text-[#2E2E2E] outline-none transition focus:border-[#F5B400] focus:ring-4 focus:ring-[#F5B400]/20" placeholder="Leave blank to use member name">
        @error('name') <span class="text-xs font-black text-[#F5B400]">{{ $message }}</span> @enderror
    </label>

    <label class="grid gap-2 text-sm font-bold text-[#0A2A6B]">
        Screening Status
        <select name="screening_status" class="rounded-lg border border-[#0A2A6B]/15 bg-white px-4 py-3 font-normal text-[#2E2E2E] outline-none transition focus:border-[#F5B400] focus:ring-4 focus:ring-[#F5B400]/20">
            @foreach (['pending', 'approved', 'rejected'] as $status)
                <option value="{{ $status }}" @selected(old('screening_status', $aspirant->screening_status ?? 'pending') === $status)>{{ ucfirst($status) }}</option>
            @endforeach
        </select>
    </label>

    <label class="grid gap-2 text-sm font-bold text-[#0A2A6B] sm:col-span-2">
        Payment Status For Selected Positions
        <select name="payment_status" class="rounded-lg border border-[#0A2A6B]/15 bg-white px-4 py-3 font-normal text-[#2E2E2E] outline-none transition focus:border-[#F5B400] focus:ring-4 focus:ring-[#F5B400]/20">
            @foreach (['pending', 'approved'] as $status)
                <option value="{{ $status }}" @selected(old('payment_status', $aspirant->positions->first()?->pivot?->payment_status ?? 'pending') === $status)>{{ ucfirst($status) }}</option>
            @endforeach
        </select>
    </label>
</div>

<div class="mt-6 rounded-lg border border-[#0A2A6B]/10 bg-[#F2F2F2] p-5">
    <p class="text-sm font-black uppercase tracking-wide text-[#F5B400]">Positions</p>
    <div class="mt-4 grid gap-3 sm:grid-cols-2">
        @php
            $selectedPositions = collect(old('positions', $aspirant->positions->pluck('id')->all()))->map(fn ($id) => (int) $id)->all();
        @endphp
        @foreach ($positions as $position)
            <label class="flex items-start gap-3 rounded-lg bg-white p-4 text-sm font-bold text-[#0A2A6B] ring-1 ring-[#0A2A6B]/10">
                <input type="checkbox" name="positions[]" value="{{ $position->id }}" @checked(in_array($position->id, $selectedPositions, true)) class="mt-1 h-4 w-4 rounded border-[#0A2A6B]/20 text-[#1FA774]">
                <span>{{ $position->name }} <span class="block text-xs font-semibold text-[#2E2E2E]/60">{{ $position->academicSession?->name }} · ₦{{ number_format($position->form_amount) }}</span></span>
            </label>
        @endforeach
    </div>
    @error('positions') <span class="mt-3 block text-xs font-black text-[#F5B400]">{{ $message }}</span> @enderror
</div>

<label class="mt-6 grid gap-2 text-sm font-bold text-[#0A2A6B]">
    Manifesto
    <textarea name="manifesto" rows="5" class="rounded-lg border border-[#0A2A6B]/15 px-4 py-3 font-normal text-[#2E2E2E] outline-none transition focus:border-[#F5B400] focus:ring-4 focus:ring-[#F5B400]/20" placeholder="Short manifesto or note">{{ old('manifesto', $aspirant->manifesto) }}</textarea>
</label>

<div class="mt-7 flex flex-col gap-3 sm:flex-row">
    <button type="submit" class="rounded-lg bg-[#1FA774] px-6 py-3 text-sm font-black text-white transition hover:bg-[#198b61]">{{ $buttonLabel }}</button>
    <a href="{{ route('admin.election.aspirants.index') }}" class="rounded-lg border border-[#0A2A6B]/20 px-6 py-3 text-center text-sm font-black text-[#0A2A6B] transition hover:border-[#1FA774] hover:text-[#1FA774]">Cancel</a>
</div>
