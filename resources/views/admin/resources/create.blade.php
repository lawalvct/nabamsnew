@extends('layouts.dashboard', ['pageTitle' => 'Upload Resource'])

@section('content')
    <section class="mx-auto max-w-4xl rounded-lg bg-white p-6 shadow-sm ring-1 ring-[#0A2A6B]/10 sm:p-8">
        <p class="text-sm font-black uppercase tracking-wide text-[#F5B400]">Resources</p>
        <h1 class="mt-3 text-3xl font-black text-[#0A2A6B]">Upload a resource</h1>
        <p class="mt-4 text-sm leading-7 text-[#2E2E2E]/70">Free resources can be downloaded by any member with dashboard access. Paid resources use the same bank transfer + verification process as dues.</p>

        <form action="{{ route('admin.resources.store') }}" method="POST" enctype="multipart/form-data" class="mt-8">
            @include('admin.resources._form', ['buttonLabel' => 'Upload Resource'])
        </form>
    </section>
@endsection
