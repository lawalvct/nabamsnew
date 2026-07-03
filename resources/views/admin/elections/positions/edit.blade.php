@extends('layouts.dashboard', ['pageTitle' => 'Edit Election Position'])

@section('content')
    @include('admin.elections.partials.nav')

    <section class="mx-auto max-w-4xl rounded-lg bg-white p-6 shadow-sm ring-1 ring-[#0A2A6B]/10 sm:p-8">
        <p class="text-sm font-black uppercase tracking-wide text-[#F5B400]">Election Position</p>
        <h1 class="mt-3 text-3xl font-black text-[#0A2A6B]">Edit {{ $position->name }}</h1>
        <form action="{{ route('admin.election.positions.update', $position) }}" method="POST" class="mt-8">
            @method('PUT')
            @include('admin.elections.positions._form', ['buttonLabel' => 'Save Changes'])
        </form>
    </section>
@endsection
