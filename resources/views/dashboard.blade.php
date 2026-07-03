@extends('layouts.dashboard', ['pageTitle' => 'Dashboard'])

@section('content')
    @include('partials.dashboard.overview')
    @include('partials.dashboard.stat-cards')
    @include('partials.dashboard.workspace')
    @include('partials.dashboard.next-step')
@endsection
