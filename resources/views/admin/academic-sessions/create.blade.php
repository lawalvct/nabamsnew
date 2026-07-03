@extends('layouts.dashboard', ['pageTitle' => 'Create Academic Session'])

@section('content')
    <section class="mx-auto max-w-4xl rounded-lg bg-white p-6 shadow-sm ring-1 ring-[#0A2A6B]/10 sm:p-8">
        <p class="text-sm font-black uppercase tracking-wide text-[#F5B400]">Academic Session</p>
        <h1 class="mt-3 text-3xl font-black text-[#0A2A6B]">Create academic session</h1>
        <p class="mt-4 text-sm leading-7 text-[#2E2E2E]/70">The first session you create will automatically become the current active session.</p>

        <form action="{{ route('admin.academic-sessions.store') }}" method="POST" class="mt-8">
            @include('admin.academic-sessions._form', ['buttonLabel' => 'Create Session'])
        </form>
    </section>
@endsection
