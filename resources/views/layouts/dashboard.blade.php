<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="scroll-smooth">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">

        <title>{{ $pageTitle ?? 'Dashboard' }} - NABAMS</title>
        <link rel="icon" type="image/png" href="{{ asset('favicon.png') }}">

        @fonts

        @if (file_exists(public_path('build/manifest.json')) || file_exists(public_path('hot')))
            @vite(['resources/css/app.css', 'resources/js/app.js'])
        @else
            <script src="https://cdn.jsdelivr.net/npm/@tailwindcss/browser@4"></script>
        @endif
    </head>
    <body class="min-h-screen bg-[#F2F2F2] font-sans text-[#2E2E2E] antialiased">
        <div class="min-h-screen lg:grid lg:grid-cols-[280px_1fr]">
            @include('partials.dashboard.sidebar')

            <div class="lg:col-start-2">
                @include('partials.dashboard.topbar')

                <main class="px-4 pb-28 pt-6 sm:px-6 lg:px-8 lg:pb-10">
                    @yield('content')
                </main>
            </div>
        </div>

        @include('partials.dashboard.mobile-nav')
    </body>
</html>
