@extends('layouts.dashboard', ['pageTitle' => 'Create Election Aspirant'])

@section('content')
    @include('admin.elections.partials.nav')

    <section class="mx-auto max-w-5xl rounded-lg bg-white p-6 shadow-sm ring-1 ring-[#0A2A6B]/10 sm:p-8">
        <p class="text-sm font-black uppercase tracking-wide text-[#F5B400]">Election Aspirant</p>
        <h1 class="mt-3 text-3xl font-black text-[#0A2A6B]">Create aspirant</h1>
        <form action="{{ route('admin.election.aspirants.store') }}" method="POST" class="mt-8">
            @include('admin.elections.aspirants._form', ['buttonLabel' => 'Create Aspirant'])
        </form>
    </section>
@endsection
