@extends('layouts.dashboard', ['pageTitle' => 'Edit Academic Session'])

@section('content')
    <section class="mx-auto max-w-4xl rounded-lg bg-white p-6 shadow-sm ring-1 ring-[#0A2A6B]/10 sm:p-8">
        <p class="text-sm font-black uppercase tracking-wide text-[#F5B400]">Academic Session</p>
        <h1 class="mt-3 text-3xl font-black text-[#0A2A6B]">Edit {{ $academicSession->name }}</h1>
        <p class="mt-4 text-sm leading-7 text-[#2E2E2E]/70">If this session remains the only current session, the system will keep it current automatically.</p>

        <form action="{{ route('admin.academic-sessions.update', $academicSession) }}" method="POST" class="mt-8">
            @method('PUT')
            @include('admin.academic-sessions._form', ['buttonLabel' => 'Save Changes'])
        </form>
    </section>
@endsection
