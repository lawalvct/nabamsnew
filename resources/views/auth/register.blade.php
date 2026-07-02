<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">

        <title>Register - NABAMS</title>

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
                    <span class="grid h-11 w-11 place-items-center rounded-lg bg-white text-base font-black text-[#0A2A6B]">N</span>
                    <span>
                        <span class="block text-lg font-black leading-none">NABAMS</span>
                        <span class="block text-xs font-semibold uppercase tracking-wide text-[#F5B400]">Leads</span>
                    </span>
                </a>
                <a href="{{ route('login') }}" class="rounded-lg bg-[#F5B400] px-4 py-2.5 text-sm font-black text-[#0A2A6B] transition hover:bg-[#ffd15c]">Login</a>
            </div>
        </header>

        <main class="mx-auto max-w-5xl px-4 py-10 sm:px-6 lg:px-8">
            <div class="mb-8 max-w-3xl">
                <p class="text-sm font-black uppercase tracking-wide text-[#F5B400]">Membership registration</p>
                <h1 class="mt-3 text-4xl font-black text-[#0A2A6B]">Create your NABAMS account</h1>
                <p class="mt-4 text-base leading-7 text-[#2E2E2E]/75">Register with your student details to access your dashboard and membership profile.</p>
            </div>

            @if ($errors->any())
                <div class="mb-6 rounded-lg border border-red-200 bg-red-50 p-4 text-sm text-red-700">
                    <p class="font-bold">Please correct the highlighted details.</p>
                    <ul class="mt-2 list-inside list-disc">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form action="{{ route('register') }}" method="POST" class="rounded-lg bg-white p-5 shadow-xl ring-1 ring-[#0A2A6B]/10 sm:p-8">
                @csrf

                <div class="grid gap-5 md:grid-cols-2">
                    <label class="grid gap-2 text-sm font-bold text-[#0A2A6B]">
                        First Name
                        <input name="firstname" value="{{ old('firstname') }}" type="text" required class="rounded-lg border border-[#0A2A6B]/15 px-4 py-3 font-normal text-[#2E2E2E] outline-none transition focus:border-[#F5B400] focus:ring-4 focus:ring-[#F5B400]/20">
                    </label>

                    <label class="grid gap-2 text-sm font-bold text-[#0A2A6B]">
                        Last Name
                        <input name="lastname" value="{{ old('lastname') }}" type="text" class="rounded-lg border border-[#0A2A6B]/15 px-4 py-3 font-normal text-[#2E2E2E] outline-none transition focus:border-[#F5B400] focus:ring-4 focus:ring-[#F5B400]/20">
                    </label>

                    <label class="grid gap-2 text-sm font-bold text-[#0A2A6B]">
                        Nickname
                        <input name="nickname" value="{{ old('nickname') }}" type="text" class="rounded-lg border border-[#0A2A6B]/15 px-4 py-3 font-normal text-[#2E2E2E] outline-none transition focus:border-[#F5B400] focus:ring-4 focus:ring-[#F5B400]/20">
                    </label>

                    <label class="grid gap-2 text-sm font-bold text-[#0A2A6B]">
                        Email Address
                        <input name="email" value="{{ old('email') }}" type="email" required class="rounded-lg border border-[#0A2A6B]/15 px-4 py-3 font-normal text-[#2E2E2E] outline-none transition focus:border-[#F5B400] focus:ring-4 focus:ring-[#F5B400]/20">
                    </label>

                    <label class="grid gap-2 text-sm font-bold text-[#0A2A6B]">
                        Matric Number
                        <input name="matno" value="{{ old('matno') }}" type="text" class="rounded-lg border border-[#0A2A6B]/15 px-4 py-3 font-normal text-[#2E2E2E] outline-none transition focus:border-[#F5B400] focus:ring-4 focus:ring-[#F5B400]/20">
                    </label>

                    <label class="grid gap-2 text-sm font-bold text-[#0A2A6B]">
                        Phone Number
                        <input name="phone" value="{{ old('phone') }}" type="tel" required class="rounded-lg border border-[#0A2A6B]/15 px-4 py-3 font-normal text-[#2E2E2E] outline-none transition focus:border-[#F5B400] focus:ring-4 focus:ring-[#F5B400]/20">
                    </label>

                    <label class="grid gap-2 text-sm font-bold text-[#0A2A6B]">
                        Level
                        <select name="level_id" class="rounded-lg border border-[#0A2A6B]/15 px-4 py-3 font-normal text-[#2E2E2E] outline-none transition focus:border-[#F5B400] focus:ring-4 focus:ring-[#F5B400]/20">
                            @foreach ([1 => 'ND I / 100 Level', 2 => 'ND II / 200 Level', 3 => 'HND I / 300 Level', 4 => 'HND II / 400 Level', 5 => 'Alumni'] as $value => $label)
                                <option value="{{ $value }}" @selected((string) old('level_id', '1') === (string) $value)>{{ $label }}</option>
                            @endforeach
                        </select>
                    </label>

                    <label class="grid gap-2 text-sm font-bold text-[#0A2A6B]">
                        Member Type
                        <select name="member_type" class="rounded-lg border border-[#0A2A6B]/15 px-4 py-3 font-normal text-[#2E2E2E] outline-none transition focus:border-[#F5B400] focus:ring-4 focus:ring-[#F5B400]/20">
                            @foreach (['Regular', 'Alumni', 'Part-time'] as $type)
                                <option value="{{ $type }}" @selected(old('member_type', 'Regular') === $type)>{{ $type }}</option>
                            @endforeach
                        </select>
                    </label>

                    <label class="grid gap-2 text-sm font-bold text-[#0A2A6B]">
                        Session Start
                        <input name="session_start" value="{{ old('session_start') }}" type="text" placeholder="2024" class="rounded-lg border border-[#0A2A6B]/15 px-4 py-3 font-normal text-[#2E2E2E] outline-none transition placeholder:text-[#2E2E2E]/45 focus:border-[#F5B400] focus:ring-4 focus:ring-[#F5B400]/20">
                    </label>

                    <label class="grid gap-2 text-sm font-bold text-[#0A2A6B]">
                        Session End
                        <input name="session_end" value="{{ old('session_end') }}" type="text" placeholder="2025" class="rounded-lg border border-[#0A2A6B]/15 px-4 py-3 font-normal text-[#2E2E2E] outline-none transition placeholder:text-[#2E2E2E]/45 focus:border-[#F5B400] focus:ring-4 focus:ring-[#F5B400]/20">
                    </label>

                    <label class="grid gap-2 text-sm font-bold text-[#0A2A6B]">
                        Password
                        <input name="password" type="password" required class="rounded-lg border border-[#0A2A6B]/15 px-4 py-3 font-normal text-[#2E2E2E] outline-none transition focus:border-[#F5B400] focus:ring-4 focus:ring-[#F5B400]/20">
                    </label>

                    <label class="grid gap-2 text-sm font-bold text-[#0A2A6B]">
                        Confirm Password
                        <input name="password_confirmation" type="password" required class="rounded-lg border border-[#0A2A6B]/15 px-4 py-3 font-normal text-[#2E2E2E] outline-none transition focus:border-[#F5B400] focus:ring-4 focus:ring-[#F5B400]/20">
                    </label>
                </div>

                <label class="mt-5 grid gap-2 text-sm font-bold text-[#0A2A6B]">
                    Expectation Message
                    <textarea name="expectation_msg" rows="5" class="rounded-lg border border-[#0A2A6B]/15 px-4 py-3 font-normal text-[#2E2E2E] outline-none transition focus:border-[#F5B400] focus:ring-4 focus:ring-[#F5B400]/20">{{ old('expectation_msg') }}</textarea>
                </label>

                <div class="mt-7 flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
                    <a href="{{ route('login') }}" class="text-sm font-bold text-[#0A2A6B] hover:text-[#1FA774]">Already have an account?</a>
                    <button type="submit" class="rounded-lg bg-[#1FA774] px-6 py-3 text-sm font-black text-white transition hover:bg-[#198b61]">Create Account</button>
                </div>
            </form>
        </main>
    </body>
</html>
