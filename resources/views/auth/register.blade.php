<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">

        <title>NABAMS Membership Registration</title>
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

        <main class="mx-auto max-w-7xl px-4 py-8 sm:px-6 lg:px-8">
            <section class="grid gap-8 lg:grid-cols-[0.82fr_1.18fr] lg:items-start">
                <aside class="rounded-lg bg-[#0A2A6B] text-white shadow-xl lg:sticky lg:top-6">
                    <button id="aside-toggle" type="button" class="flex w-full items-center justify-between p-6 sm:p-8 lg:cursor-default lg:pointer-events-none" aria-expanded="false" aria-controls="aside-body">
                        <span>
                            <p class="text-sm font-black uppercase tracking-wide text-[#F5B400]">NABAMS Leads</p>
                            <h1 class="mt-1 text-2xl font-black leading-tight sm:text-3xl lg:text-5xl lg:mt-3">NABAMS Membership Registration</h1>
                        </span>
                        <svg id="aside-chevron" class="ml-4 h-5 w-5 shrink-0 transition-transform duration-300 lg:hidden" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5" aria-hidden="true">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7"/>
                        </svg>
                    </button>

                    <div id="aside-body" class="hidden px-6 pb-6 sm:px-8 sm:pb-8 lg:block">
                        <p class="text-base leading-8 text-[#F2F2F2]/80">Create your official member account for Business Administration & Management updates, resources, contests, and dashboard access.</p>

                        <div class="mt-8 grid gap-3 text-sm">
                            <div class="rounded-lg border border-white/10 bg-white/10 p-4">
                                <p class="font-black text-[#F5B400]">1. Personal Info</p>
                                <p class="mt-1 text-[#F2F2F2]/75">Name, gender, date of birth, phone, and email.</p>
                            </div>
                            <div class="rounded-lg border border-white/10 bg-white/10 p-4">
                                <p class="font-black text-[#F5B400]">2. Academic Info</p>
                                <p class="mt-1 text-[#F2F2F2]/75">Matric number, department, level, and member type.</p>
                            </div>
                            <div class="rounded-lg border border-white/10 bg-white/10 p-4">
                                <p class="font-black text-[#F5B400]">3. Account Security</p>
                                <p class="mt-1 text-[#F2F2F2]/75">Password plus a simple security question.</p>
                            </div>
                        </div>
                    </div>
                </aside>

                <form id="registration-form" action="{{ route('register') }}" method="POST" enctype="multipart/form-data" class="rounded-lg bg-white p-5 shadow-xl ring-1 ring-[#0A2A6B]/10 sm:p-8">
                    @csrf
                    <input type="text" name="website" value="" tabindex="-1" autocomplete="off" class="absolute -left-[9999px] h-px w-px opacity-0" aria-hidden="true">

                    @if ($errors->any())
                        <div class="mb-6 rounded-lg border border-[#F5B400]/40 bg-[#F5B400]/10 p-4 text-sm text-[#0A2A6B]">
                            <p class="font-black">Please correct the highlighted details.</p>
                        </div>
                    @endif

                    <section>
                        <div class="border-b border-[#0A2A6B]/10 pb-4">
                            <p class="text-sm font-black uppercase tracking-wide text-[#F5B400]">Personal Info</p>
                            <h2 class="mt-2 text-2xl font-black text-[#0A2A6B]">Personal Information</h2>
                        </div>

                        <div class="mt-6 grid gap-5 md:grid-cols-2">
                            <label class="grid gap-2 text-sm font-bold text-[#0A2A6B] md:col-span-2">
                                Full Name
                                <input name="full_name" value="{{ old('full_name') }}" type="text" required class="rounded-lg border border-[#0A2A6B]/15 px-4 py-3 font-normal text-[#2E2E2E] outline-none transition placeholder:text-[#2E2E2E]/45 focus:border-[#F5B400] focus:ring-4 focus:ring-[#F5B400]/20" placeholder="Surname Firstname Othername">
                                @error('full_name') <span class="text-xs font-black text-[#F5B400]">{{ $message }}</span> @enderror
                            </label>

                            <label class="grid gap-2 text-sm font-bold text-[#0A2A6B]">
                                Gender
                                <select name="gender" required class="rounded-lg border border-[#0A2A6B]/15 px-4 py-3 font-normal text-[#2E2E2E] outline-none transition focus:border-[#F5B400] focus:ring-4 focus:ring-[#F5B400]/20">
                                    <option value="">Select gender</option>
                                    @foreach (['Male', 'Female', 'Other'] as $gender)
                                        <option value="{{ $gender }}" @selected(old('gender') === $gender)>{{ $gender }}</option>
                                    @endforeach
                                </select>
                                @error('gender') <span class="text-xs font-black text-[#F5B400]">{{ $message }}</span> @enderror
                            </label>

                            <div class="grid gap-2 text-sm font-bold text-[#0A2A6B]">
                                Date of Birth
                                <input type="hidden" id="dob" name="dob" value="{{ old('dob') }}">
                                <div class="grid grid-cols-3 gap-2">
                                    <select id="dob_day" class="rounded-lg border border-[#0A2A6B]/15 px-3 py-3 font-normal text-[#2E2E2E] outline-none transition focus:border-[#F5B400] focus:ring-4 focus:ring-[#F5B400]/20">
                                        <option value="">Day</option>
                                        @for ($d = 1; $d <= 31; $d++)
                                            <option value="{{ $d }}">{{ $d }}</option>
                                        @endfor
                                    </select>
                                    <select id="dob_month" class="rounded-lg border border-[#0A2A6B]/15 px-3 py-3 font-normal text-[#2E2E2E] outline-none transition focus:border-[#F5B400] focus:ring-4 focus:ring-[#F5B400]/20">
                                        <option value="">Month</option>
                                        @foreach (['January','February','March','April','May','June','July','August','September','October','November','December'] as $i => $month)
                                            <option value="{{ $i + 1 }}">{{ $month }}</option>
                                        @endforeach
                                    </select>
                                    <select id="dob_year" class="rounded-lg border border-[#0A2A6B]/15 px-3 py-3 font-normal text-[#2E2E2E] outline-none transition focus:border-[#F5B400] focus:ring-4 focus:ring-[#F5B400]/20">
                                        <option value="">Year</option>
                                        @for ($y = date('Y'); $y >= 1990; $y--)
                                            <option value="{{ $y }}">{{ $y }}</option>
                                        @endfor
                                    </select>
                                </div>
                                @error('dob') <span class="text-xs font-black text-[#F5B400]">{{ $message }}</span> @enderror
                            </div>

                            <label class="grid gap-2 text-sm font-bold text-[#0A2A6B]">
                                Phone Number
                                <input name="phone" value="{{ old('phone') }}" type="tel" required class="rounded-lg border border-[#0A2A6B]/15 px-4 py-3 font-normal text-[#2E2E2E] outline-none transition placeholder:text-[#2E2E2E]/45 focus:border-[#F5B400] focus:ring-4 focus:ring-[#F5B400]/20" placeholder="08012345678">
                                @error('phone') <span class="text-xs font-black text-[#F5B400]">{{ $message }}</span> @enderror
                            </label>

                            <label class="grid gap-2 text-sm font-bold text-[#0A2A6B]">
                                Email Address
                                <input name="email" value="{{ old('email') }}" type="email" required class="rounded-lg border border-[#0A2A6B]/15 px-4 py-3 font-normal text-[#2E2E2E] outline-none transition placeholder:text-[#2E2E2E]/45 focus:border-[#F5B400] focus:ring-4 focus:ring-[#F5B400]/20" placeholder="you@example.com">
                                @error('email') <span class="text-xs font-black text-[#F5B400]">{{ $message }}</span> @enderror
                            </label>
                        </div>
                    </section>

                    <section class="mt-10">
                        <div class="border-b border-[#0A2A6B]/10 pb-4">
                            <p class="text-sm font-black uppercase tracking-wide text-[#F5B400]">Academic Info</p>
                            <h2 class="mt-2 text-2xl font-black text-[#0A2A6B]">Academic Information</h2>
                        </div>

                        <div class="mt-6 grid gap-5 md:grid-cols-2">
                            <label class="grid gap-2 text-sm font-bold text-[#0A2A6B]">
                                Matric Number
                                <input name="matno" value="{{ old('matno') }}" type="text" required class="rounded-lg border border-[#0A2A6B]/15 px-4 py-3 font-normal uppercase text-[#2E2E2E] outline-none transition placeholder:text-[#2E2E2E]/45 focus:border-[#F5B400] focus:ring-4 focus:ring-[#F5B400]/20" placeholder="Your matric number">
                                @error('matno') <span class="text-xs font-black text-[#F5B400]">{{ $message }}</span> @enderror
                            </label>

                            <label class="grid gap-2 text-sm font-bold text-[#0A2A6B]">
                                Department
                                <input name="department" value="Business Administration & Management" type="text" readonly class="rounded-lg border border-[#0A2A6B]/15 bg-[#F2F2F2] px-4 py-3 font-normal text-[#2E2E2E] outline-none">
                                @error('department') <span class="text-xs font-black text-[#F5B400]">{{ $message }}</span> @enderror
                            </label>

                            <label class="grid gap-2 text-sm font-bold text-[#0A2A6B]">
                                Level
                                <select name="academic_level" required class="rounded-lg border border-[#0A2A6B]/15 px-4 py-3 font-normal text-[#2E2E2E] outline-none transition focus:border-[#F5B400] focus:ring-4 focus:ring-[#F5B400]/20">
                                    <option value="">Select level</option>
                                    @foreach (['ND1', 'ND2', 'ND3', 'HND1', 'HND2', 'HND3', 'GRADUATE'] as $level)
                                        <option value="{{ $level }}" @selected(old('academic_level') === $level)>{{ $level }}</option>
                                    @endforeach
                                </select>
                                @error('academic_level') <span class="text-xs font-black text-[#F5B400]">{{ $message }}</span> @enderror
                            </label>

                            <label class="grid gap-2 text-sm font-bold text-[#0A2A6B]">
                                Member Type
                                <select name="member_type" required class="rounded-lg border border-[#0A2A6B]/15 px-4 py-3 font-normal text-[#2E2E2E] outline-none transition focus:border-[#F5B400] focus:ring-4 focus:ring-[#F5B400]/20">
                                    <option value="Regular" @selected(old('member_type', 'Regular') === 'Regular')>Regular</option>
                                    <option value="Part-time" @selected(old('member_type') === 'Part-time')>Part-Time</option>
                                    <option value="Alumni" @selected(old('member_type') === 'Alumni')>Alumni</option>
                                </select>
                                @error('member_type') <span class="text-xs font-black text-[#F5B400]">{{ $message }}</span> @enderror
                            </label>
                        </div>
                    </section>

                    <section class="mt-10">
                        <div class="border-b border-[#0A2A6B]/10 pb-4">
                            <p class="text-sm font-black uppercase tracking-wide text-[#F5B400]">Security</p>
                            <h2 class="mt-2 text-2xl font-black text-[#0A2A6B]">Security Question</h2>
                        </div>

                        <div class="mt-6 grid gap-5 md:grid-cols-2">
                            <div class="rounded-lg border border-[#0A2A6B]/10 bg-[#F2F2F2] p-5">
                                <p class="text-sm font-bold text-[#2E2E2E]/70">Security Question</p>
                                <p class="mt-2 text-3xl font-black text-[#0A2A6B]">{{ $securityQuestion }}</p>
                            </div>

                            <label class="grid gap-2 text-sm font-bold text-[#0A2A6B]">
                                Your Answer
                                <input id="security_answer" name="security_answer" value="{{ old('security_answer') }}" type="text" inputmode="numeric" pattern="[0-9]{1,2}" maxlength="2" required class="rounded-lg border border-[#0A2A6B]/15 px-4 py-3 font-normal text-[#2E2E2E] outline-none transition placeholder:text-[#2E2E2E]/45 focus:border-[#F5B400] focus:ring-4 focus:ring-[#F5B400]/20" placeholder="Answer" data-expected="{{ (int) session('registration_security_answer') }}">
                                <span id="security_answer_error" class="hidden text-xs font-black text-[#F5B400]"></span>
                                @error('security_answer') <span class="text-xs font-black text-[#F5B400]">{{ $message }}</span> @enderror
                            </label>
                        </div>
                    </section>

                    <section class="mt-10">
                        <div class="border-b border-[#0A2A6B]/10 pb-4">
                            <p class="text-sm font-black uppercase tracking-wide text-[#F5B400]">Account</p>
                            <h2 class="mt-2 text-2xl font-black text-[#0A2A6B]">Account Information</h2>
                        </div>

                        <div class="mt-6 grid gap-5 md:grid-cols-2">
                            <label class="grid gap-2 text-sm font-bold text-[#0A2A6B]">
                                Password
                                <input name="password" type="password" required class="rounded-lg border border-[#0A2A6B]/15 px-4 py-3 font-normal text-[#2E2E2E] outline-none transition focus:border-[#F5B400] focus:ring-4 focus:ring-[#F5B400]/20">
                                @error('password') <span class="text-xs font-black text-[#F5B400]">{{ $message }}</span> @enderror
                            </label>

                            <label class="grid gap-2 text-sm font-bold text-[#0A2A6B]">
                                Confirm Password
                                <input name="password_confirmation" type="password" required class="rounded-lg border border-[#0A2A6B]/15 px-4 py-3 font-normal text-[#2E2E2E] outline-none transition focus:border-[#F5B400] focus:ring-4 focus:ring-[#F5B400]/20">
                            </label>
                        </div>
                    </section>

                    <section class="mt-10">
                        <div class="border-b border-[#0A2A6B]/10 pb-4">
                            <p class="text-sm font-black uppercase tracking-wide text-[#F5B400]">Optional</p>
                            <h2 class="mt-2 text-2xl font-black text-[#0A2A6B]">Optional Fields</h2>
                        </div>

                        <div class="mt-6 grid gap-5 md:grid-cols-2">
                            <label class="grid gap-2 text-sm font-bold text-[#0A2A6B]">
                                Passport Photograph
                                <input name="passport_photograph" type="file" accept="image/*" class="rounded-lg border border-[#0A2A6B]/15 px-4 py-3 text-sm font-normal text-[#2E2E2E] file:mr-4 file:rounded-lg file:border-0 file:bg-[#0A2A6B] file:px-4 file:py-2 file:text-sm file:font-black file:text-white focus:border-[#F5B400] focus:ring-4 focus:ring-[#F5B400]/20">
                                @error('passport_photograph') <span class="text-xs font-black text-[#F5B400]">{{ $message }}</span> @enderror
                            </label>

                            <label class="grid gap-2 text-sm font-bold text-[#0A2A6B]">
                                WhatsApp Number
                                <input name="whatsapp_number" value="{{ old('whatsapp_number') }}" type="tel" class="rounded-lg border border-[#0A2A6B]/15 px-4 py-3 font-normal text-[#2E2E2E] outline-none transition placeholder:text-[#2E2E2E]/45 focus:border-[#F5B400] focus:ring-4 focus:ring-[#F5B400]/20" placeholder="Optional">
                                @error('whatsapp_number') <span class="text-xs font-black text-[#F5B400]">{{ $message }}</span> @enderror
                            </label>

                            <label class="grid gap-2 text-sm font-bold text-[#0A2A6B] md:col-span-2">
                                Home Address
                                <textarea name="home_address" rows="4" class="rounded-lg border border-[#0A2A6B]/15 px-4 py-3 font-normal text-[#2E2E2E] outline-none transition placeholder:text-[#2E2E2E]/45 focus:border-[#F5B400] focus:ring-4 focus:ring-[#F5B400]/20" placeholder="Optional">{{ old('home_address') }}</textarea>
                                @error('home_address') <span class="text-xs font-black text-[#F5B400]">{{ $message }}</span> @enderror
                            </label>
                        </div>
                    </section>

                    <div class="mt-8 flex flex-col gap-3 border-t border-[#0A2A6B]/10 pt-6 sm:flex-row sm:items-center sm:justify-between">
                        <a href="{{ route('login') }}" class="text-sm font-black text-[#0A2A6B] transition hover:text-[#1FA774]">Already registered?</a>
                        <button id="registration-submit" type="submit" class="inline-flex items-center justify-center gap-3 rounded-lg bg-[#1FA774] px-7 py-3 text-sm font-black text-white transition hover:bg-[#198b61]">
                            <svg id="registration-spinner" class="hidden h-5 w-5 animate-spin" viewBox="0 0 24 24" fill="none" aria-hidden="true">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                <path class="opacity-90" fill="currentColor" d="M4 12a8 8 0 0 1 8-8v4a4 4 0 0 0-4 4H4z"></path>
                            </svg>
                            <span id="registration-submit-text">Create Account</span>
                        </button>
                    </div>
                </form>
            </section>
        </main>

        <script>
            document.addEventListener('DOMContentLoaded', () => {
                const toggle = document.getElementById('aside-toggle');
                const body = document.getElementById('aside-body');
                const chevron = document.getElementById('aside-chevron');

                toggle?.addEventListener('click', () => {
                    const open = body.classList.toggle('hidden');
                    toggle.setAttribute('aria-expanded', String(!open));
                    chevron.style.transform = open ? '' : 'rotate(180deg)';
                });

                // DOB dropdowns
                const dobHidden = document.getElementById('dob');
                const dobDay = document.getElementById('dob_day');
                const dobMonth = document.getElementById('dob_month');
                const dobYear = document.getElementById('dob_year');

                const assembleDob = () => {
                    const d = dobDay.value, m = dobMonth.value, y = dobYear.value;
                    dobHidden.value = (d && m && y)
                        ? `${y}-${String(m).padStart(2,'0')}-${String(d).padStart(2,'0')}`
                        : '';
                };

                // Restore old value into dropdowns
                if (dobHidden.value) {
                    const parts = dobHidden.value.split('-');
                    if (parts.length === 3) {
                        dobYear.value  = parseInt(parts[0], 10);
                        dobMonth.value = parseInt(parts[1], 10);
                        dobDay.value   = parseInt(parts[2], 10);
                    }
                }

                [dobDay, dobMonth, dobYear].forEach(el => el.addEventListener('change', assembleDob));

                const form = document.getElementById('registration-form');
                const button = document.getElementById('registration-submit');
                const spinner = document.getElementById('registration-spinner');
                const buttonText = document.getElementById('registration-submit-text');
                const securityInput = document.getElementById('security_answer');
                const securityError = document.getElementById('security_answer_error');

                securityInput?.addEventListener('input', () => {
                    securityError.classList.add('hidden');
                    securityInput.classList.remove('border-red-400');
                });

                form?.addEventListener('submit', (e) => {
                    // DOB validation
                    if (!dobDay.value || !dobMonth.value || !dobYear.value) {
                        e.preventDefault();
                        dobDay.classList.add('border-red-400');
                        dobMonth.classList.add('border-red-400');
                        dobYear.classList.add('border-red-400');
                        dobDay.focus();
                        return;
                    }
                    const selected = new Date(dobYear.value, dobMonth.value - 1, dobDay.value);
                    if (selected >= new Date()) {
                        e.preventDefault();
                        dobYear.classList.add('border-red-400');
                        dobYear.focus();
                        return;
                    }
                    [dobDay, dobMonth, dobYear].forEach(el => el.classList.remove('border-red-400'));
                    const expected = parseInt(securityInput.dataset.expected, 10);
                    const given = parseInt(securityInput.value.trim(), 10);

                    if (isNaN(given) || given !== expected) {
                        e.preventDefault();
                        securityError.textContent = 'Incorrect answer. Please try again.';
                        securityError.classList.remove('hidden');
                        securityInput.classList.add('border-red-400');
                        securityInput.focus();
                        return;
                    }

                    button.disabled = true;
                    button.classList.add('cursor-not-allowed', 'opacity-80');
                    spinner.classList.remove('hidden');
                    buttonText.textContent = 'Creating Account';
                });
            });
        </script>
    </body>
</html>
