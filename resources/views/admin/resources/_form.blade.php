@csrf

@php
    $inputClass = 'rounded-lg border border-[#0A2A6B]/15 bg-white px-4 py-3 font-normal text-[#2E2E2E] outline-none transition focus:border-[#F5B400] focus:ring-4 focus:ring-[#F5B400]/20';
    $allowed = collect(\App\Support\SecureUpload::RESOURCE_EXTENSIONS)->reject(fn ($ext) => $ext === 'jpeg');
@endphp

<div class="grid gap-5 sm:grid-cols-2">
    <label class="grid gap-2 text-sm font-bold text-[#0A2A6B] sm:col-span-2">
        Title
        <input name="title" value="{{ old('title', $resource->title) }}" required maxlength="150" placeholder="BAM 211 Past Questions (2020-2025)" class="{{ $inputClass }}">
        @error('title')<span class="text-sm font-bold text-red-600">{{ $message }}</span>@enderror
    </label>

    <label class="grid gap-2 text-sm font-bold text-[#0A2A6B] sm:col-span-2">
        Description <span class="font-normal text-[#2E2E2E]/60">(optional)</span>
        <textarea name="description" rows="4" maxlength="2000" class="{{ $inputClass }}">{{ old('description', $resource->description) }}</textarea>
        @error('description')<span class="text-sm font-bold text-red-600">{{ $message }}</span>@enderror
    </label>

    <label class="grid gap-2 text-sm font-bold text-[#0A2A6B]">
        Category
        <select name="category" class="{{ $inputClass }}">
            <option value="">General</option>
            @foreach ($categories as $category)
                <option value="{{ $category }}" @selected(old('category', $resource->category) === $category)>{{ $category }}</option>
            @endforeach
        </select>
        @error('category')<span class="text-sm font-bold text-red-600">{{ $message }}</span>@enderror
    </label>

    <label class="grid gap-2 text-sm font-bold text-[#0A2A6B]">
        Level
        <select name="level_id" class="{{ $inputClass }}">
            <option value="">All levels</option>
            @foreach ($levels as $level)
                <option value="{{ $level->id }}" @selected((string) old('level_id', $resource->level_id) === (string) $level->id)>{{ $level->name }}</option>
            @endforeach
        </select>
        @error('level_id')<span class="text-sm font-bold text-red-600">{{ $message }}</span>@enderror
    </label>

    <label class="grid gap-2 text-sm font-bold text-[#0A2A6B]">
        Access
        <select name="access" class="{{ $inputClass }}" data-access>
            <option value="free" @selected(old('access', $resource->access) === 'free')>Free download</option>
            <option value="paid" @selected(old('access', $resource->access) === 'paid')>Paid (members purchase)</option>
        </select>
        @error('access')<span class="text-sm font-bold text-red-600">{{ $message }}</span>@enderror
    </label>

    <label class="grid gap-2 text-sm font-bold text-[#0A2A6B]" data-price>
        Price (&#8358;)
        <input name="price" type="number" min="0" value="{{ old('price', $resource->price ?: '') }}" placeholder="1000" class="{{ $inputClass }}">
        @error('price')<span class="text-sm font-bold text-red-600">{{ $message }}</span>@enderror
    </label>

    <label class="grid gap-2 text-sm font-bold text-[#0A2A6B] sm:col-span-2">
        File {{ $resource->exists ? '(leave empty to keep the current file)' : '' }}
        <input name="file" type="file" @required(! $resource->exists) accept="{{ $allowed->map(fn ($ext) => '.'.$ext)->implode(',') }},.jpeg" class="rounded-lg border border-dashed border-[#0A2A6B]/30 bg-[#F2F2F2] px-4 py-6 text-sm font-normal file:mr-4 file:rounded-lg file:border-0 file:bg-[#0A2A6B] file:px-4 file:py-2 file:text-sm file:font-black file:text-white">
        <span class="text-xs font-normal text-[#2E2E2E]/60">
            Allowed: {{ strtoupper($allowed->implode(', ')) }} &middot; max {{ $maxUploadMb }}MB.
            Files are scanned: PDFs with scripts, Office files with macros and disguised files are rejected; images are re-encoded.
        </span>
        @if ($resource->exists)
            <span class="text-xs font-semibold text-[#0A2A6B]">Current: {{ strtoupper($resource->file_extension) }} &middot; {{ $resource->humanFileSize() }}</span>
        @endif
        @error('file')<span class="text-sm font-bold text-red-600">{{ $message }}</span>@enderror
    </label>

    <label class="grid gap-2 text-sm font-bold text-[#0A2A6B]">
        Status
        <select name="is_published" class="{{ $inputClass }}">
            <option value="Yes" @selected(old('is_published', $resource->is_published) === 'Yes')>Published (visible to members)</option>
            <option value="No" @selected(old('is_published', $resource->is_published) === 'No')>Hidden</option>
        </select>
        @error('is_published')<span class="text-sm font-bold text-red-600">{{ $message }}</span>@enderror
    </label>
</div>

<div class="mt-7 flex flex-col gap-3 sm:flex-row">
    <button type="submit" class="inline-flex justify-center rounded-lg bg-[#1FA774] px-6 py-3 text-sm font-black text-white transition hover:bg-[#198b61]">{{ $buttonLabel }}</button>
    <a href="{{ route('admin.resources.index') }}" class="inline-flex justify-center rounded-lg border border-[#0A2A6B]/20 px-6 py-3 text-sm font-black text-[#0A2A6B] transition hover:border-[#1FA774] hover:text-[#1FA774]">Cancel</a>
</div>

<script>
    (() => {
        const access = document.querySelector('[data-access]');
        const price = document.querySelector('[data-price]');
        const sync = () => price.classList.toggle('hidden', access.value !== 'paid');
        access?.addEventListener('change', sync);
        sync();
    })();
</script>
