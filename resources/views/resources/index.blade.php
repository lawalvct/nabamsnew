@extends('layouts.dashboard', ['pageTitle' => 'Resources'])

@php
    $filterClass = 'rounded-lg border border-[#0A2A6B]/15 bg-white px-4 py-3 text-sm font-normal text-[#2E2E2E] outline-none transition focus:border-[#F5B400] focus:ring-4 focus:ring-[#F5B400]/20';
@endphp

@section('content')
    <section class="rounded-lg bg-[#0A2A6B] p-6 text-white shadow-xl sm:p-8">
        <p class="text-sm font-black uppercase tracking-wide text-[#F5B400]">Resources</p>
        <h1 class="mt-3 text-3xl font-black leading-tight sm:text-4xl">Study materials &amp; downloads</h1>
        <p class="mt-4 max-w-3xl text-sm leading-7 text-[#F2F2F2]/80">Lecture notes, past questions, templates and more. Free resources download instantly; paid ones unlock after your payment is verified.</p>
    </section>

    <form method="GET" action="{{ route('resources.index') }}" class="mt-6 grid gap-4 rounded-lg bg-white p-5 shadow-sm ring-1 ring-[#0A2A6B]/10 sm:grid-cols-2 lg:grid-cols-5">
        <input name="q" value="{{ $filters['q'] ?? '' }}" placeholder="Search resources" class="{{ $filterClass }} lg:col-span-2">
        <select name="category" class="{{ $filterClass }}">
            <option value="">All categories</option>
            @foreach ($categories as $category)
                <option value="{{ $category }}" @selected(($filters['category'] ?? '') === $category)>{{ $category }}</option>
            @endforeach
        </select>
        <select name="level_id" class="{{ $filterClass }}">
            <option value="">All levels</option>
            @foreach ($levels as $level)
                <option value="{{ $level->id }}" @selected((string) ($filters['level_id'] ?? '') === (string) $level->id)>{{ $level->name }}</option>
            @endforeach
        </select>
        <div class="flex gap-2">
            <select name="access" class="{{ $filterClass }} flex-1">
                <option value="">Any price</option>
                <option value="free" @selected(($filters['access'] ?? '') === 'free')>Free</option>
                <option value="paid" @selected(($filters['access'] ?? '') === 'paid')>Paid</option>
                <option value="owned" @selected(($filters['access'] ?? '') === 'owned')>Purchased</option>
            </select>
            <button type="submit" class="rounded-lg bg-[#0A2A6B] px-4 py-3 text-sm font-black text-white transition hover:bg-[#0d358a]">Go</button>
        </div>
    </form>

    <section class="mt-6 grid gap-5 sm:grid-cols-2 xl:grid-cols-3">
        @forelse ($resources as $resource)
            @php
                $owned = $ownedIds->contains($resource->id);
            @endphp
            <a href="{{ route('resources.show', $resource) }}" class="group flex flex-col rounded-lg bg-white p-5 shadow-sm ring-1 ring-[#0A2A6B]/10 transition hover:-translate-y-0.5 hover:shadow-lg hover:ring-[#F5B400]">
                <div class="flex items-start gap-4">
                    @include('resources._file-badge', ['extension' => $resource->file_extension])
                    <div class="min-w-0 flex-1">
                        <h2 class="line-clamp-2 font-black text-[#0A2A6B] group-hover:text-[#1FA774]">{{ $resource->title }}</h2>
                        <p class="mt-1 text-xs font-semibold text-[#2E2E2E]/60">{{ $resource->category ?? 'General' }} &middot; {{ $resource->level?->name ?? 'All levels' }} &middot; {{ $resource->humanFileSize() }}</p>
                    </div>
                </div>
                @if ($resource->description)
                    <p class="mt-3 line-clamp-2 text-sm leading-6 text-[#2E2E2E]/70">{{ $resource->description }}</p>
                @endif
                <div class="mt-auto flex items-center justify-between gap-3 pt-4">
                    <span class="text-xs font-semibold text-[#2E2E2E]/55">{{ number_format($resource->view_count) }} views &middot; {{ number_format($resource->download_count) }} downloads</span>
                    @if ($resource->isFree())
                        <span class="rounded-full bg-[#1FA774]/10 px-3 py-1 text-xs font-black text-[#1FA774]">Free</span>
                    @elseif ($owned)
                        <span class="rounded-full bg-[#0A2A6B]/10 px-3 py-1 text-xs font-black text-[#0A2A6B]">Purchased</span>
                    @else
                        <span class="rounded-full bg-[#F5B400]/20 px-3 py-1 text-xs font-black text-[#0A2A6B]">&#8358;{{ number_format($resource->price) }}</span>
                    @endif
                </div>
            </a>
        @empty
            <div class="rounded-lg bg-white p-8 text-center shadow-sm ring-1 ring-[#0A2A6B]/10 sm:col-span-2 xl:col-span-3">
                <p class="text-sm font-black uppercase tracking-wide text-[#F5B400]">Nothing here yet</p>
                <h2 class="mt-3 text-2xl font-black text-[#0A2A6B]">No resources match your search.</h2>
            </div>
        @endforelse
    </section>

    @if ($resources->hasPages())
        <div class="mt-6">{{ $resources->links() }}</div>
    @endif
@endsection
