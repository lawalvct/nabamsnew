@extends('layouts.dashboard', ['pageTitle' => 'Resources'])

@php
    $filterClass = 'rounded-lg border border-[#0A2A6B]/15 bg-white px-4 py-3 text-sm font-normal text-[#2E2E2E] outline-none transition focus:border-[#F5B400] focus:ring-4 focus:ring-[#F5B400]/20';
@endphp

@section('content')
    <section class="rounded-lg bg-[#0A2A6B] p-6 text-white shadow-xl sm:p-8">
        <div class="flex flex-col gap-5 lg:flex-row lg:items-end lg:justify-between">
            <div>
                <p class="text-sm font-black uppercase tracking-wide text-[#F5B400]">Resources</p>
                <h1 class="mt-3 text-3xl font-black leading-tight sm:text-4xl">Manage member resources</h1>
                <p class="mt-4 max-w-3xl text-sm leading-7 text-[#F2F2F2]/80">Upload free or paid materials. Purchases appear in Transactions for verification, just like dues.</p>
            </div>
            <a href="{{ route('admin.resources.create') }}" class="inline-flex justify-center rounded-lg bg-[#F5B400] px-5 py-3 text-sm font-black text-[#0A2A6B] transition hover:bg-[#ffd15c]">Upload Resource</a>
        </div>
    </section>

    @foreach (['success' => 'border-[#1FA774]/20 bg-[#1FA774]/10', 'error' => 'border-red-200 bg-red-50'] as $key => $tone)
        @if (session($key))
            <div class="mt-6 rounded-lg border {{ $tone }} px-5 py-4 text-sm font-bold text-[#0A2A6B]">{{ session($key) }}</div>
        @endif
    @endforeach

    <section class="mt-6 grid gap-5 sm:grid-cols-2 xl:grid-cols-4">
        @foreach ([['Resources', number_format($totals['resources'])], ['Total Views', number_format($totals['views'])], ['Total Downloads', number_format($totals['downloads'])], ['Sales Revenue', '₦'.number_format($totals['revenue'])]] as [$label, $value])
            <div class="rounded-lg bg-white p-6 shadow-sm ring-1 ring-[#0A2A6B]/10">
                <p class="text-sm font-black uppercase tracking-wide text-[#F5B400]">{{ $label }}</p>
                <p class="mt-3 text-3xl font-black text-[#0A2A6B]">{{ $value }}</p>
            </div>
        @endforeach
    </section>

    <form method="GET" action="{{ route('admin.resources.index') }}" class="mt-6 flex flex-col gap-3 rounded-lg bg-white p-5 shadow-sm ring-1 ring-[#0A2A6B]/10 sm:flex-row">
        <input name="q" value="{{ $filters['q'] ?? '' }}" placeholder="Search by title" class="{{ $filterClass }} flex-1">
        <select name="access" class="{{ $filterClass }}">
            <option value="">Free &amp; paid</option>
            <option value="free" @selected(($filters['access'] ?? '') === 'free')>Free</option>
            <option value="paid" @selected(($filters['access'] ?? '') === 'paid')>Paid</option>
        </select>
        <button type="submit" class="rounded-lg bg-[#0A2A6B] px-5 py-3 text-sm font-black text-white transition hover:bg-[#0d358a]">Filter</button>
    </form>

    <section class="mt-6 overflow-hidden rounded-lg bg-white shadow-sm ring-1 ring-[#0A2A6B]/10">
        <div class="hidden overflow-x-auto lg:block">
            <table class="min-w-full divide-y divide-[#0A2A6B]/10">
                <thead class="bg-[#F2F2F2]">
                    <tr>
                        @foreach (['Resource', 'Access', 'Views', 'Downloads', 'Sales', 'Status', ''] as $heading)
                            <th class="px-5 py-3 text-left text-xs font-black uppercase tracking-wide text-[#2E2E2E]/65">{{ $heading }}</th>
                        @endforeach
                    </tr>
                </thead>
                <tbody class="divide-y divide-[#0A2A6B]/10">
                    @forelse ($resources as $resource)
                        <tr>
                            <td class="px-5 py-4">
                                <div class="flex items-center gap-3">
                                    @include('resources._file-badge', ['extension' => $resource->file_extension, 'size' => 'h-10 w-10'])
                                    <div class="min-w-0">
                                        <p class="truncate font-black text-[#0A2A6B]">{{ $resource->title }}</p>
                                        <p class="text-xs font-semibold text-[#2E2E2E]/60">{{ $resource->category ?? 'General' }} &middot; {{ $resource->level?->name ?? 'All levels' }} &middot; {{ $resource->humanFileSize() }}</p>
                                    </div>
                                </div>
                            </td>
                            <td class="px-5 py-4 text-sm font-black {{ $resource->isFree() ? 'text-[#1FA774]' : 'text-[#0A2A6B]' }}">{{ $resource->isFree() ? 'Free' : '₦'.number_format($resource->price) }}</td>
                            <td class="px-5 py-4 text-sm font-semibold">{{ number_format($resource->view_count) }}</td>
                            <td class="px-5 py-4 text-sm font-semibold">{{ number_format($resource->download_count) }}</td>
                            <td class="px-5 py-4 text-sm">
                                @if (! $resource->isFree() || $resource->purchases_count)
                                    <p class="font-black text-[#0A2A6B]">{{ $resource->purchases_count }} sold &middot; &#8358;{{ number_format((int) $resource->revenue) }}</p>
                                    @if ($resource->pending_count)
                                        <a href="{{ route('admin.payments.index', ['status' => 'pending', 'type' => 'resource', 'q' => $resource->title]) }}" class="text-xs font-black text-[#F5B400] underline">{{ $resource->pending_count }} awaiting verification</a>
                                    @endif
                                @else
                                    <span class="text-[#2E2E2E]/50">-</span>
                                @endif
                            </td>
                            <td class="px-5 py-4">
                                <span class="rounded-full px-3 py-1 text-xs font-black {{ $resource->is_published === 'Yes' ? 'bg-[#1FA774]/10 text-[#1FA774]' : 'bg-[#F2F2F2] text-[#2E2E2E]/70' }}">{{ $resource->is_published === 'Yes' ? 'Published' : 'Hidden' }}</span>
                            </td>
                            <td class="px-5 py-4">
                                <div class="flex justify-end gap-2">
                                    <a href="{{ route('resources.download', $resource) }}" class="rounded-lg border border-[#0A2A6B]/20 px-3 py-2 text-xs font-black text-[#0A2A6B] transition hover:bg-[#F2F2F2]">Download</a>
                                    <a href="{{ route('admin.resources.edit', $resource) }}" class="rounded-lg border border-[#0A2A6B]/20 px-3 py-2 text-xs font-black text-[#0A2A6B] transition hover:bg-[#0A2A6B] hover:text-white">Edit</a>
                                    <form action="{{ route('admin.resources.destroy', $resource) }}" method="POST" onsubmit="return confirm('Delete this resource and its file?');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="rounded-lg border border-[#F5B400]/50 px-3 py-2 text-xs font-black text-[#0A2A6B] transition hover:bg-[#F5B400]">Delete</button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="7" class="px-5 py-10 text-center text-sm font-semibold text-[#2E2E2E]/65">No resource uploaded yet.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="grid gap-4 p-4 lg:hidden">
            @forelse ($resources as $resource)
                <article class="rounded-lg border border-[#0A2A6B]/10 bg-[#F2F2F2] p-4">
                    <div class="flex items-start gap-3">
                        @include('resources._file-badge', ['extension' => $resource->file_extension, 'size' => 'h-10 w-10'])
                        <div class="min-w-0 flex-1">
                            <p class="font-black text-[#0A2A6B]">{{ $resource->title }}</p>
                            <p class="text-xs font-semibold text-[#2E2E2E]/60">{{ $resource->isFree() ? 'Free' : '₦'.number_format($resource->price) }} &middot; {{ number_format($resource->view_count) }} views &middot; {{ number_format($resource->download_count) }} downloads</p>
                        </div>
                    </div>
                    <div class="mt-3 flex gap-2">
                        <a href="{{ route('admin.resources.edit', $resource) }}" class="rounded-lg bg-[#0A2A6B] px-3 py-2 text-xs font-black text-white">Edit</a>
                        <a href="{{ route('resources.download', $resource) }}" class="rounded-lg bg-white px-3 py-2 text-xs font-black text-[#0A2A6B]">Download</a>
                    </div>
                </article>
            @empty
                <p class="rounded-lg bg-[#F2F2F2] p-6 text-center text-sm font-semibold text-[#2E2E2E]/65">No resource uploaded yet.</p>
            @endforelse
        </div>

        @if ($resources->hasPages())
            <div class="border-t border-[#0A2A6B]/10 px-5 py-4">{{ $resources->links() }}</div>
        @endif
    </section>
@endsection
