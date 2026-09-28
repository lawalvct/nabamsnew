<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">

        <title>Forgot Password - NABAMS</title>
        <link rel="icon" type="image/png" href="{{ asset('favicon.png') }}">

        @fonts

        @if (file_exists(public_path('build/manifest.json')) || file_exists(public_path('hot')))
            @vite(['resources/css/app.css', 'resources/js/app.js'])
        @else
            <script src="https://cdn.jsdelivr.net/npm/@tailwindcss/browser@4"></script>
        @endif
    </head>
    <body class="min-h-screen bg-[#F2F2F2] font-sans text-[#2E2E2E] antialiased">
        <main class="grid min-h-screen lg:grid-cols-[0.95fr_1.05fr]">
            <section class="hidden bg-[#0A2A6B] px-10 py-12 text-white lg:flex lg:flex-col lg:justify-between">
                <a href="{{ url('/') }}" class="flex items-center gap-3">
                    <span class="grid h-12 w-12 place-items-center overflow-hidden rounded-lg bg-white p-1">
                        <img src="{{ asset('logo.png') }}" alt="NABAMS logo" class="h-full w-full object-contain">
                    </span>
                    <span>
                        <span class="block text-xl font-black">NABAMS</span>
                        <span class="block text-sm font-semibold uppercase tracking-wide text-[#F5B400]">Leads</span>
                    </span>
                </a>

                <div class="max-w-xl">
                    <p class="text-sm font-black uppercase tracking-wide text-[#F5B400]">Password recovery</p>
                    <h1 class="mt-4 text-5xl font-black leading-tight">Reset your dashboard password.</h1>
                    <p class="mt-5 text-lg leading-8 text-[#F2F2F2]/80">Enter your account email address and we will send you a secure link to choose a new password.</p>
                </div>

                <p class="text-sm text-[#F2F2F2]/70">National Association of Business Administration and Management Students</p>
            </section>

            <section class="flex items-center justify-center px-4 py-10 sm:px-6 lg:px-8">
                <div class="w-full max-w-md">
                    <div class="mb-8 lg:hidden">
                        <a href="{{ url('/') }}" class="inline-flex items-center gap-3">
                            <span class="grid h-11 w-11 place-items-center overflow-hidden rounded-lg bg-white p-1 shadow-sm ring-1 ring-[#0A2A6B]/10">
                                <img src="{{ asset('logo.png') }}" alt="NABAMS logo" class="h-full w-full object-contain">
                            </span>
                            <span class="text-lg font-black text-[#0A2A6B]">NABAMS</span>
                        </a>
                    </div>

                    <div class="rounded-lg bg-white p-6 shadow-xl ring-1 ring-[#0A2A6B]/10 sm:p-8">
                        <div>
                            <p class="text-sm font-black uppercase tracking-wide text-[#F5B400]">Forgot Password</p>
                            <h2 class="mt-2 text-3xl font-black text-[#0A2A6B]">Request reset link</h2>
                            <p class="mt-3 text-sm leading-6 text-[#2E2E2E]/70">We will email you a link that lets you set a new password.</p>
                        </div>

                        @if (session('status'))
                            <div class="mt-5 rounded-lg border border-[#1FA774]/20 bg-[#1FA774]/10 p-4 text-sm text-[#0A2A6B]">
                                {{ session('status') }}
                            </div>
                        @endif

                        @if ($errors->any())
                            <div class="mt-5 rounded-lg border border-red-200 bg-red-50 p-4 text-sm text-red-700">
                                {{ $errors->first() }}
                            </div>
                        @endif

                        <form action="{{ route('password.email') }}" method="POST" class="mt-7 grid gap-5">
                            @csrf

                            <label class="grid gap-2 text-sm font-bold text-[#0A2A6B]">
                                Email Address
                                <input name="email" type="email" value="{{ old('email') }}" required autofocus class="rounded-lg border border-[#0A2A6B]/15 bg-white px-4 py-3 font-normal text-[#2E2E2E] outline-none transition placeholder:text-[#2E2E2E]/45 focus:border-[#F5B400] focus:ring-4 focus:ring-[#F5B400]/20" placeholder="you@example.com">
                            </label>

                            <button type="submit" class="rounded-lg bg-[#1FA774] px-6 py-3 text-sm font-black text-white transition hover:bg-[#198b61]">Send Reset Link</button>
                        </form>

                        <p class="mt-6 text-center text-sm text-[#2E2E2E]/75">
                            Remembered your password?
                            <a href="{{ route('login') }}" class="font-black text-[#0A2A6B] hover:text-[#1FA774]">Back to login</a>
                        </p>
                    </div>
                </div>
            </section>
        </main>
    </body>
</html>
