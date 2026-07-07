<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">

        <title>Registration Closed | NABAMS</title>
        <link rel="icon" type="image/png" href="{{ asset('favicon.png') }}">

        @fonts

        @if (file_exists(public_path('build/manifest.json')) || file_exists(public_path('hot')))
            @vite(['resources/css/app.css', 'resources/js/app.js'])
        @else
            <script src="https://cdn.jsdelivr.net/npm/@tailwindcss/browser@4"></script>
        @endif
    </head>
    <body class="min-h-screen bg-[#F2F2F2] font-sans text-[#2E2E2E] antialiased">
        <header class="bg-[#0A2A6B] text-white">
            <div class="mx-auto flex max-w-7xl items-center justify-between px-4 py-4 sm:px-6 lg:px-8">
                <a href="{{ url('/') }}" class="flex items-center gap-3">
                    <span class="grid h-11 w-11 place-items-center overflow-hidden rounded-lg bg-white p-1">
                        <img src="{{ asset('logo.png') }}" alt="NABAMS logo" class="h-full w-full object-contain">
                    </span>
                    <span>
                        <span class="block text-lg font-black leading-none">NABAMS</span>
                        <span class="block text-xs font-semibold uppercase tracking-wide text-[#F5B400]">Membership</span>
                    </span>
                </a>
                <a href="{{ route('login') }}" class="rounded-lg bg-[#F5B400] px-4 py-2.5 text-sm font-black text-[#0A2A6B] transition hover:bg-[#ffd15c]">Login</a>
            </div>
        </header>

        <main class="mx-auto grid min-h-[calc(100vh-76px)] max-w-4xl place-items-center px-4 py-12 sm:px-6 lg:px-8">
            <section class="w-full rounded-lg bg-white p-6 text-center shadow-xl ring-1 ring-[#0A2A6B]/10 sm:p-10">
                <p class="text-sm font-black uppercase tracking-wide text-[#F5B400]">Member Registration</p>
                <h1 class="mt-3 text-3xl font-black text-[#0A2A6B] sm:text-4xl">Registration is currently closed</h1>
                <p class="mx-auto mt-4 max-w-2xl text-sm leading-7 text-[#2E2E2E]/70">
                    Public member registration has been disabled by the admin. Please contact the NABAMS admin team if you need help creating an account.
                </p>

                @if (session('registration_disabled'))
                    <p class="mx-auto mt-5 max-w-2xl rounded-lg bg-[#F5B400]/15 px-4 py-3 text-sm font-bold text-[#0A2A6B]">
                        {{ session('registration_disabled') }}
                    </p>
                @endif

                <div class="mt-7 flex flex-col justify-center gap-3 sm:flex-row">
                    <a href="{{ route('login') }}" class="rounded-lg bg-[#1FA774] px-6 py-3 text-sm font-black text-white transition hover:bg-[#198b61]">Go to Login</a>
                    <a href="{{ url('/') }}" class="rounded-lg border border-[#0A2A6B]/20 px-6 py-3 text-sm font-black text-[#0A2A6B] transition hover:border-[#1FA774] hover:text-[#1FA774]">Back Home</a>
                </div>
            </section>
        </main>
    </body>
</html>
