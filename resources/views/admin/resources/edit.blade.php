@extends('layouts.dashboard', ['pageTitle' => 'Edit Resource'])

@section('content')
    <section class="mx-auto max-w-4xl rounded-lg bg-white p-6 shadow-sm ring-1 ring-[#0A2A6B]/10 sm:p-8">
        <p class="text-sm font-black uppercase tracking-wide text-[#F5B400]">Resources</p>
        <h1 class="mt-3 text-3xl font-black text-[#0A2A6B]">Edit {{ $resource->title }}</h1>
        <p class="mt-4 text-sm leading-7 text-[#2E2E2E]/70">Price changes apply to new purchases only. Members who already bought it keep access.</p>

        <form action="{{ route('admin.resources.update', $resource) }}" method="POST" enctype="multipart/form-data" class="mt-8">
            @method('PUT')
            @include('admin.resources._form', ['buttonLabel' => 'Save Changes'])
        </form>
    </section>
@endsection
