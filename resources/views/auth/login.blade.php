<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">

        <title>Login - NABAMS</title>

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
                    <span class="grid h-12 w-12 place-items-center rounded-lg bg-white text-lg font-black text-[#0A2A6B]">N</span>
                    <span>
                        <span class="block text-xl font-black">NABAMS</span>
                        <span class="block text-sm font-semibold uppercase tracking-wide text-[#F5B400]">Leads</span>
                    </span>
                </a>

                <div class="max-w-xl">
                    <p class="text-sm font-black uppercase tracking-wide text-[#F5B400]">Member access</p>
                    <h1 class="mt-4 text-5xl font-black leading-tight">Welcome back to your dashboard.</h1>
                    <p class="mt-5 text-lg leading-8 text-[#F2F2F2]/80">Sign in to manage your NABAMS profile, membership information, contest updates, and student resources.</p>
                </div>

                <p class="text-sm text-[#F2F2F2]/70">National Association of Business Administration and Management Students</p>
            </section>

            <section class="flex items-center justify-center px-4 py-10 sm:px-6 lg:px-8">
                <div class="w-full max-w-md">
                    <div class="mb-8 lg:hidden">
                        <a href="{{ url('/') }}" class="inline-flex items-center gap-3">
                            <span class="grid h-11 w-11 place-items-center rounded-lg bg-[#0A2A6B] text-base font-black text-white">N</span>
                            <span class="text-lg font-black text-[#0A2A6B]">NABAMS</span>
                        </a>
                    </div>

                    <div class="rounded-lg bg-white p-6 shadow-xl ring-1 ring-[#0A2A6B]/10 sm:p-8">
                        <div>
                            <p class="text-sm font-black uppercase tracking-wide text-[#F5B400]">Login</p>
                            <h2 class="mt-2 text-3xl font-black text-[#0A2A6B]">Dashboard access</h2>
                        </div>

                        @if ($errors->any())
                            <div class="mt-5 rounded-lg border border-red-200 bg-red-50 p-4 text-sm text-red-700">
                                {{ $errors->first() }}
                            </div>
                        @endif

                        <form action="{{ route('login') }}" method="POST" class="mt-7 grid gap-5">
                            @csrf

                            <label class="grid gap-2 text-sm font-bold text-[#0A2A6B]">
                                Email Address
                                <input name="email" type="email" value="{{ old('email') }}" required autofocus class="rounded-lg border border-[#0A2A6B]/15 bg-white px-4 py-3 font-normal text-[#2E2E2E] outline-none transition placeholder:text-[#2E2E2E]/45 focus:border-[#F5B400] focus:ring-4 focus:ring-[#F5B400]/20" placeholder="you@example.com">
                            </label>

                            <label class="grid gap-2 text-sm font-bold text-[#0A2A6B]">
                                Password
                                <input name="password" type="password" required class="rounded-lg border border-[#0A2A6B]/15 bg-white px-4 py-3 font-normal text-[#2E2E2E] outline-none transition placeholder:text-[#2E2E2E]/45 focus:border-[#F5B400] focus:ring-4 focus:ring-[#F5B400]/20" placeholder="Your password">
                            </label>

                            <label class="flex items-center gap-3 text-sm font-semibold text-[#2E2E2E]/75">
                                <input name="remember" type="checkbox" value="1" class="h-4 w-4 rounded border-[#0A2A6B]/20 text-[#1FA774] focus:ring-[#1FA774]">
                                Remember me
                            </label>

                            <button type="submit" class="rounded-lg bg-[#1FA774] px-6 py-3 text-sm font-black text-white transition hover:bg-[#198b61]">Login to Dashboard</button>
                        </form>

                        <p class="mt-6 text-center text-sm text-[#2E2E2E]/75">
                            New member?
                            <a href="{{ route('register') }}" class="font-black text-[#0A2A6B] hover:text-[#1FA774]">Create an account</a>
                        </p>
                    </div>
                </div>
            </section>
        </main>
    </body>
</html>
