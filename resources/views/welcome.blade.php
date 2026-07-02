<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="scroll-smooth">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="description" content="Official homepage of the National Association of Business Administration and Management Students (NABAMS).">

        <title>NABAMS - National Association of Business Administration and Management Students</title>

        @fonts

        @if (file_exists(public_path('build/manifest.json')) || file_exists(public_path('hot')))
            @vite(['resources/css/app.css', 'resources/js/app.js'])
        @else
            <script src="https://cdn.jsdelivr.net/npm/@tailwindcss/browser@4"></script>
        @endif
    </head>
    <body class="min-h-screen bg-white font-sans text-[#2E2E2E] antialiased">
        <div class="bg-[#0A2A6B] text-white">
            <div class="mx-auto flex max-w-7xl flex-col gap-2 px-4 py-3 text-sm sm:px-6 md:flex-row md:items-center md:justify-between lg:px-8">
                <p class="font-medium">Current Session: 2024-2025</p>
                <a href="tel:07012435051" class="inline-flex w-fit items-center gap-2 rounded-full bg-white/10 px-3 py-1 text-white transition hover:bg-white/20">
                    <span class="h-2 w-2 rounded-full bg-[#F5B400]"></span>
                    Support Contact: 07012435051
                </a>
            </div>
        </div>

        <header class="sticky top-0 z-50 border-b border-white/10 bg-[#0A2A6B]/95 text-white backdrop-blur">
            <nav class="mx-auto flex max-w-7xl items-center justify-between px-4 py-4 sm:px-6 lg:px-8" aria-label="Main navigation">
                <a href="{{ url('/') }}" class="flex items-center gap-3">
                    <span class="grid h-11 w-11 place-items-center rounded-lg bg-white text-base font-black text-[#0A2A6B]">N</span>
                    <span>
                        <span class="block text-lg font-black leading-none text-white">NABAMS</span>
                        <span class="block text-xs font-semibold uppercase tracking-wide text-[#F5B400]">Leads</span>
                    </span>
                </a>

                <button
                    id="nav-toggle"
                    type="button"
                    class="inline-flex h-11 w-11 items-center justify-center rounded-lg border border-white/20 text-white lg:hidden"
                    aria-label="Toggle navigation"
                    aria-expanded="false"
                    aria-controls="mobile-menu"
                >
                    <svg data-menu-open-icon xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M4 6h16M4 12h16M4 18h16" />
                    </svg>
                    <svg data-menu-close-icon xmlns="http://www.w3.org/2000/svg" class="hidden h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M6 18 18 6M6 6l12 12" />
                    </svg>
                </button>

                <div class="hidden items-center gap-7 text-sm font-semibold text-white lg:flex">
                    <a class="transition hover:text-[#F5B400]" href="{{ url('/') }}">Home</a>
                    <a class="transition hover:text-[#F5B400]" href="{{ url('/about') }}">About</a>
                    <a class="transition hover:text-[#F5B400]" href="{{ url('/excos') }}">Excos</a>
                    <a class="transition hover:text-[#F5B400]" href="{{ url('/membership') }}">Membership</a>
                    <a class="transition hover:text-[#F5B400]" href="{{ url('/resources') }}">Resources</a>
                    <a class="transition hover:text-[#F5B400]" href="{{ url('/contest') }}">Contest</a>
                    <a class="transition hover:text-[#F5B400]" href="{{ url('/support') }}">Support</a>
                    <a class="rounded-lg bg-[#F5B400] px-4 py-2.5 font-bold text-[#0A2A6B] transition hover:bg-[#ffd15c]" href="{{ url('/login') }}">Login to Dashboard</a>
                </div>
            </nav>

            <div id="mobile-menu" class="hidden border-t border-white/10 bg-[#0A2A6B] px-4 pb-5 lg:hidden">
                <div class="mx-auto grid max-w-7xl gap-1 py-3 text-sm font-semibold text-white">
                    <a class="rounded-lg px-3 py-3 hover:bg-white/10 hover:text-[#F5B400]" href="{{ url('/') }}">Home</a>
                    <a class="rounded-lg px-3 py-3 hover:bg-white/10 hover:text-[#F5B400]" href="{{ url('/about') }}">About</a>
                    <a class="rounded-lg px-3 py-3 hover:bg-white/10 hover:text-[#F5B400]" href="{{ url('/excos') }}">Excos</a>
                    <a class="rounded-lg px-3 py-3 hover:bg-white/10 hover:text-[#F5B400]" href="{{ url('/membership') }}">Membership</a>
                    <a class="rounded-lg px-3 py-3 hover:bg-white/10 hover:text-[#F5B400]" href="{{ url('/resources') }}">Resources</a>
                    <a class="rounded-lg px-3 py-3 hover:bg-white/10 hover:text-[#F5B400]" href="{{ url('/contest') }}">Contest</a>
                    <a class="rounded-lg px-3 py-3 hover:bg-white/10 hover:text-[#F5B400]" href="{{ url('/support') }}">Support</a>
                    <a class="mt-2 rounded-lg bg-[#F5B400] px-3 py-3 text-center font-bold text-[#0A2A6B]" href="{{ url('/login') }}">Login to Dashboard</a>
                </div>
            </div>
        </header>

        <main>
            <section class="relative overflow-hidden bg-[#0A2A6B] text-white">
                <div class="absolute inset-0 opacity-30">
                    <img
                        src="https://images.unsplash.com/photo-1523580846011-d3a5bc25702b?auto=format&fit=crop&w=1800&q=80"
                        alt="Students walking on campus"
                        class="h-full w-full object-cover"
                    >
                </div>
                <div class="absolute inset-0 bg-[linear-gradient(115deg,rgba(10,42,107,0.96),rgba(10,42,107,0.82),rgba(31,167,116,0.72))]"></div>

                <div class="relative mx-auto grid min-h-[640px] max-w-7xl items-center gap-10 px-4 py-20 sm:px-6 lg:grid-cols-[1.1fr_0.9fr] lg:px-8 lg:py-24">
                    <div class="max-w-3xl">
                        <p id="hero-label" class="mb-5 inline-flex rounded-full border border-white/20 bg-white/10 px-4 py-2 text-sm font-bold uppercase tracking-wide text-[#F5B400]">National Association</p>
                        <h1 id="hero-title" class="text-4xl font-black leading-tight text-white sm:text-5xl lg:text-7xl">Welcome to NABAMS</h1>
                        <p id="hero-subtitle" class="mt-6 max-w-2xl text-lg font-semibold leading-8 text-white sm:text-2xl">National Association of Business Administration and Management Students (NABAMS)</p>
                        <p id="hero-note" class="mt-5 max-w-2xl text-base leading-7 text-[#F2F2F2] sm:text-lg">Building capable business leaders through service, learning, and student representation.</p>

                        <div class="mt-9 flex flex-col gap-3 sm:flex-row">
                            <a href="{{ url('/about') }}" class="inline-flex items-center justify-center rounded-lg bg-[#F5B400] px-6 py-3 text-sm font-black text-[#0A2A6B] transition hover:bg-[#ffd15c]">Read More</a>
                            <a href="#contact" class="inline-flex items-center justify-center rounded-lg border border-white/30 px-6 py-3 text-sm font-black text-white transition hover:bg-white/10">Contact NABAMS</a>
                        </div>

                        <div class="mt-10 flex gap-3" aria-label="Hero slide controls">
                            <button type="button" class="h-2.5 w-10 rounded-full bg-[#F5B400] transition-all" data-hero-dot="0" aria-label="Show slide 1"></button>
                            <button type="button" class="h-2.5 w-2.5 rounded-full bg-white/40 transition-all hover:bg-white/70" data-hero-dot="1" aria-label="Show slide 2"></button>
                            <button type="button" class="h-2.5 w-2.5 rounded-full bg-white/40 transition-all hover:bg-white/70" data-hero-dot="2" aria-label="Show slide 3"></button>
                        </div>
                    </div>

                    <div class="grid gap-4 rounded-lg border border-white/15 bg-white/10 p-5 shadow-2xl backdrop-blur md:grid-cols-2 lg:grid-cols-1 xl:grid-cols-2">
                        <div class="rounded-lg bg-white p-5 text-[#0A2A6B]">
                            <p class="text-sm font-bold uppercase tracking-wide text-[#F5B400]">Session</p>
                            <p class="mt-3 text-3xl font-black">2024-2025</p>
                            <p class="mt-2 text-sm leading-6 text-[#2E2E2E]/75">Current academic session for NABAMS activities and student programmes.</p>
                        </div>
                        <div class="rounded-lg bg-[#F5B400] p-5 text-[#0A2A6B]">
                            <p class="text-sm font-bold uppercase tracking-wide">Motto</p>
                            <p class="mt-3 text-3xl font-black">Leads</p>
                            <p class="mt-2 text-sm font-semibold leading-6">Others Follow</p>
                        </div>
                    </div>
                </div>
            </section>

            <section class="bg-white py-16 sm:py-20" id="about">
                <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
                    <div class="grid gap-10 lg:grid-cols-[0.9fr_1.1fr] lg:items-center">
                        <div>
                            <p class="text-sm font-black uppercase tracking-wide text-[#F5B400]">NABAMS LEADS</p>
                            <h2 class="mt-3 text-3xl font-black tracking-normal text-[#0A2A6B] sm:text-4xl">Others Follow</h2>
                            <p class="mt-5 max-w-xl text-base leading-8 text-[#2E2E2E]/75">
                                NABAMS represents Business Administration and Management students with a focus on academic growth, leadership development, welfare, and opportunities that strengthen every member's campus experience.
                            </p>
                            <a href="{{ url('/membership') }}" class="mt-7 inline-flex rounded-lg bg-[#1FA774] px-5 py-3 text-sm font-black text-white transition hover:bg-[#198b61]">Read More</a>
                        </div>

                        <div class="grid gap-4 sm:grid-cols-3">
                            <div class="rounded-lg border border-[#0A2A6B]/10 bg-[#F2F2F2] p-5">
                                <p class="text-3xl font-black text-[#1FA774]">01</p>
                                <h3 class="mt-5 font-black text-[#0A2A6B]">Leadership</h3>
                                <p class="mt-2 text-sm leading-6 text-[#2E2E2E]/75">Responsible representation for students and class interests.</p>
                            </div>
                            <div class="rounded-lg border border-[#0A2A6B]/10 bg-[#F2F2F2] p-5">
                                <p class="text-3xl font-black text-[#1FA774]">02</p>
                                <h3 class="mt-5 font-black text-[#0A2A6B]">Resources</h3>
                                <p class="mt-2 text-sm leading-6 text-[#2E2E2E]/75">Useful updates, materials, and association information.</p>
                            </div>
                            <div class="rounded-lg border border-[#0A2A6B]/10 bg-[#F2F2F2] p-5">
                                <p class="text-3xl font-black text-[#1FA774]">03</p>
                                <h3 class="mt-5 font-black text-[#0A2A6B]">Community</h3>
                                <p class="mt-2 text-sm leading-6 text-[#2E2E2E]/75">Programmes, contests, welfare, and member support.</p>
                            </div>
                        </div>
                    </div>
                </div>
            </section>

            <section class="bg-[#F2F2F2] py-16 sm:py-20">
                <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
                    <div class="max-w-2xl">
                        <p class="text-sm font-black uppercase tracking-wide text-[#F5B400]">Quick Access</p>
                        <h2 class="mt-3 text-3xl font-black text-[#0A2A6B] sm:text-4xl">Find what you need faster</h2>
                    </div>

                    <div class="mt-10 grid gap-5 md:grid-cols-2 lg:grid-cols-4">
                        <a href="{{ url('/membership') }}" class="rounded-lg bg-white p-6 shadow-sm ring-1 ring-[#0A2A6B]/10 transition hover:-translate-y-1 hover:shadow-lg">
                            <h3 class="text-lg font-black text-[#0A2A6B]">Membership</h3>
                            <p class="mt-3 text-sm leading-6 text-[#2E2E2E]/75">Register, update your profile, and stay active within the association.</p>
                        </a>
                        <a href="{{ url('/resources') }}" class="rounded-lg bg-white p-6 shadow-sm ring-1 ring-[#0A2A6B]/10 transition hover:-translate-y-1 hover:shadow-lg">
                            <h3 class="text-lg font-black text-[#0A2A6B]">Resources</h3>
                            <p class="mt-3 text-sm leading-6 text-[#2E2E2E]/75">Access student resources, news, documents, and useful updates.</p>
                        </a>
                        <a href="{{ url('/contest') }}" class="rounded-lg bg-white p-6 shadow-sm ring-1 ring-[#0A2A6B]/10 transition hover:-translate-y-1 hover:shadow-lg">
                            <h3 class="text-lg font-black text-[#0A2A6B]">Contest</h3>
                            <p class="mt-3 text-sm leading-6 text-[#2E2E2E]/75">Follow election contests and leadership participation updates.</p>
                        </a>
                        <a href="{{ url('/support') }}" class="rounded-lg bg-white p-6 shadow-sm ring-1 ring-[#0A2A6B]/10 transition hover:-translate-y-1 hover:shadow-lg">
                            <h3 class="text-lg font-black text-[#0A2A6B]">Support</h3>
                            <p class="mt-3 text-sm leading-6 text-[#2E2E2E]/75">Reach the association for help, enquiries, or student support.</p>
                        </a>
                    </div>
                </div>
            </section>

            <section id="contact" class="bg-[#F2F2F2] py-16 sm:py-20">
                <div class="mx-auto grid max-w-7xl gap-10 px-4 sm:px-6 lg:grid-cols-[0.8fr_1fr] lg:px-8">
                    <div>
                        <p class="text-sm font-black uppercase tracking-wide text-[#F5B400]">Contact</p>
                        <h2 class="mt-3 text-3xl font-black text-[#0A2A6B] sm:text-4xl">Send NABAMS a message</h2>
                        <p class="mt-4 text-base leading-8 text-[#2E2E2E]/75">Use the official details below or send a message through the contact form.</p>

                        <div class="mt-8 grid gap-5">
                            <div class="rounded-lg border border-[#0A2A6B]/10 bg-white p-5">
                                <h3 class="font-black text-[#0A2A6B]">Address</h3>
                                <p class="mt-3 leading-7 text-[#2E2E2E]/75">
                                    Ogun State Institute of Technology, Igbesa<br>
                                    Oba Adeshola Market Road<br>
                                    P.M.B 2025<br>
                                    Igbesa, Ogun State, Nigeria.
                                </p>
                            </div>
                            <div class="grid gap-5 sm:grid-cols-2">
                                <div class="rounded-lg border border-[#0A2A6B]/10 bg-white p-5">
                                    <h3 class="font-black text-[#0A2A6B]">Email</h3>
                                    <a href="mailto:ogitechnabamsleads@gmail.com" class="mt-3 block break-words text-[#2E2E2E]/75 hover:text-[#1FA774]">ogitechnabamsleads@gmail.com</a>
                                </div>
                                <div class="rounded-lg border border-[#0A2A6B]/10 bg-white p-5">
                                    <h3 class="font-black text-[#0A2A6B]">Phone</h3>
                                    <a href="tel:08091624642" class="mt-3 block text-[#2E2E2E]/75 hover:text-[#1FA774]">08091624642</a>
                                    <a href="tel:08161410427" class="mt-2 block text-[#2E2E2E]/75 hover:text-[#1FA774]">08161410427</a>
                                </div>
                            </div>
                        </div>
                    </div>

                    <form action="{{ url('/contact') }}" method="POST" class="rounded-lg bg-[#0A2A6B] p-5 shadow-xl sm:p-7">
                        @csrf
                        <div class="grid gap-5 sm:grid-cols-2">
                            <label class="grid gap-2 text-sm font-bold text-white">
                                Your Name
                                <input name="name" type="text" required class="rounded-lg border border-white/15 bg-white px-4 py-3 font-normal text-[#2E2E2E] outline-none transition placeholder:text-[#2E2E2E]/45 focus:border-[#F5B400] focus:ring-4 focus:ring-[#F5B400]/20" placeholder="Your Name">
                            </label>
                            <label class="grid gap-2 text-sm font-bold text-white">
                                Your Email
                                <input name="email" type="email" required class="rounded-lg border border-white/15 bg-white px-4 py-3 font-normal text-[#2E2E2E] outline-none transition placeholder:text-[#2E2E2E]/45 focus:border-[#F5B400] focus:ring-4 focus:ring-[#F5B400]/20" placeholder="Your Email">
                            </label>
                        </div>
                        <label class="mt-5 grid gap-2 text-sm font-bold text-white">
                            Subject
                            <input name="subject" type="text" required class="rounded-lg border border-white/15 bg-white px-4 py-3 font-normal text-[#2E2E2E] outline-none transition placeholder:text-[#2E2E2E]/45 focus:border-[#F5B400] focus:ring-4 focus:ring-[#F5B400]/20" placeholder="Subject">
                        </label>
                        <label class="mt-5 grid gap-2 text-sm font-bold text-white">
                            Message
                            <textarea name="message" rows="6" required class="rounded-lg border border-white/15 bg-white px-4 py-3 font-normal text-[#2E2E2E] outline-none transition placeholder:text-[#2E2E2E]/45 focus:border-[#F5B400] focus:ring-4 focus:ring-[#F5B400]/20" placeholder="Message"></textarea>
                        </label>
                        <button type="submit" class="mt-6 w-full rounded-lg bg-[#1FA774] px-6 py-3 text-sm font-black text-white transition hover:bg-[#198b61]">Send Message</button>
                    </form>
                </div>
            </section>
        </main>

        <footer class="bg-[#0A2A6B] text-[#F2F2F2]">
            <div class="mx-auto grid max-w-7xl gap-10 px-4 py-12 sm:px-6 md:grid-cols-[1.1fr_1.5fr_1fr] lg:px-8">
                <div>
                    <a href="{{ url('/') }}" class="text-2xl font-black text-white">NABAMS</a>
                    <p class="mt-4 max-w-sm text-sm leading-7 text-[#F2F2F2]/75">National Association of Business Administration and Management Students.</p>
                    <a href="mailto:ogitechnabamsleads@gmail.com" class="mt-4 block break-words text-sm font-semibold text-[#F5B400]">ogitechnabamsleads@gmail.com</a>
                </div>

                <div class="grid gap-8 sm:grid-cols-2">
                    <div>
                        <h3 class="text-sm font-black uppercase tracking-wide text-white">Menu</h3>
                        <div class="mt-4 grid gap-3 text-sm text-[#F2F2F2]/75">
                            <a class="hover:text-white" href="{{ url('/') }}">Home</a>
                            <a class="hover:text-white" href="{{ url('/about') }}">About us</a>
                            <a class="hover:text-white" href="{{ url('/resources') }}">Resources</a>
                            <a class="hover:text-white" href="{{ url('/membership') }}">Membership</a>
                            <a class="hover:text-white" href="{{ url('/events') }}">Events</a>
                        </div>
                    </div>
                    <div>
                        <h3 class="text-sm font-black uppercase tracking-wide text-white">Account</h3>
                        <div class="mt-4 grid gap-3 text-sm text-[#F2F2F2]/75">
                            <a class="hover:text-white" href="{{ url('/login') }}">Login</a>
                            <a class="hover:text-white" href="{{ url('/register') }}">Registration</a>
                            <a class="hover:text-white" href="{{ url('/dashboard') }}">My Profile</a>
                            <a class="hover:text-white" href="{{ url('/contest') }}">Election Contests</a>
                        </div>
                    </div>
                </div>

                <div class="rounded-lg border border-white/10 bg-white/5 p-5">
                    <h3 class="font-black text-white">Legacy 2024 Excos</h3>
                    <p class="mt-3 text-sm leading-6 text-[#F2F2F2]/75">Honouring the leaders who served the association and strengthened the NABAMS community.</p>
                </div>
            </div>
            <div class="border-t border-white/10 px-4 py-5 text-center text-sm text-[#F2F2F2]/70">
                Copyright &copy; 2024 NABAMS
            </div>
        </footer>

        <script>
            document.addEventListener('DOMContentLoaded', () => {
                const navToggle = document.getElementById('nav-toggle');
                const mobileMenu = document.getElementById('mobile-menu');
                const openIcon = document.querySelector('[data-menu-open-icon]');
                const closeIcon = document.querySelector('[data-menu-close-icon]');

                navToggle?.addEventListener('click', () => {
                    const isOpen = mobileMenu.classList.toggle('hidden') === false;

                    navToggle.setAttribute('aria-expanded', isOpen.toString());
                    openIcon?.classList.toggle('hidden', isOpen);
                    closeIcon?.classList.toggle('hidden', !isOpen);
                });

                const slides = [
                    {
                        label: 'National Association',
                        title: 'Welcome to NABAMS',
                        subtitle: 'National Association of Business Administration and Management Students (NABAMS)',
                        note: 'Building capable business leaders through service, learning, and student representation.',
                    },
                    {
                        label: '2024-2025 Session',
                        title: 'NABAMS Leads',
                        subtitle: 'Others Follow',
                        note: 'A student body focused on excellence, accountability, and opportunities that move members forward.',
                    },
                    {
                        label: 'Get Involved',
                        title: 'Join the Movement',
                        subtitle: 'Membership, resources, contests, and support for every business administration student.',
                        note: 'Stay connected with association updates, programmes, elections, and student welfare channels.',
                    },
                ];

                const label = document.getElementById('hero-label');
                const title = document.getElementById('hero-title');
                const subtitle = document.getElementById('hero-subtitle');
                const note = document.getElementById('hero-note');
                const dots = document.querySelectorAll('[data-hero-dot]');
                let activeSlide = 0;

                const showSlide = (index) => {
                    const slide = slides[index];

                    label.textContent = slide.label;
                    title.textContent = slide.title;
                    subtitle.textContent = slide.subtitle;
                    note.textContent = slide.note;

                    dots.forEach((dot, dotIndex) => {
                        const isActive = dotIndex === index;

                        dot.classList.toggle('w-10', isActive);
                        dot.classList.toggle('w-2.5', !isActive);
                        dot.classList.toggle('bg-[#F5B400]', isActive);
                        dot.classList.toggle('bg-white/40', !isActive);
                    });

                    activeSlide = index;
                };

                dots.forEach((dot) => {
                    dot.addEventListener('click', () => showSlide(Number(dot.dataset.heroDot)));
                });

                setInterval(() => showSlide((activeSlide + 1) % slides.length), 6500);
            });
        </script>
    </body>
</html>
