<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="scroll-smooth">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="description" content="Official homepage of the National Association of Business Administration and Management Students (NABAMS).">

        <title>NABAMS - National Association of Business Administration and Management Students</title>
        <link rel="icon" type="image/png" href="{{ asset('favicon.png') }}">

        @fonts

        @if (file_exists(public_path('build/manifest.json')) || file_exists(public_path('hot')))
            @vite(['resources/css/app.css', 'resources/js/app.js'])
        @else
            <script src="https://cdn.jsdelivr.net/npm/@tailwindcss/browser@4"></script>
        @endif
    </head>
    <body class="min-h-screen bg-white font-sans text-[#2E2E2E] antialiased">
        @include('partials.site.topbar')
        @include('partials.site.navbar')

        <main>
            @include('partials.site.header')

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

            @include('partials.site.registration-cta')

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

        @include('partials.site.footer')
        @include('partials.site.welcome-modal')

        <script>
            document.addEventListener('DOMContentLoaded', () => {
                const slides = [
                    {
                        label: 'National Association',
                        title: 'Welcome to NABAMS',
                        subtitle: 'National Association of Business Administration and Management Students (NABAMS)',
                        note: 'Building capable business leaders through service, learning, and student representation.',
                    },
                    {
                        label: '2025-2026 Session',
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
                    {
                        label: 'Membership Registration',
                        title: 'Create Your Account',
                        subtitle: 'Register with NABAMS and continue to your student dashboard.',
                        note: 'Keep your academic profile active, access member resources, and stay close to association opportunities.',
                    },
                ];

                const label = document.getElementById('hero-label');
                const title = document.getElementById('hero-title');
                const subtitle = document.getElementById('hero-subtitle');
                const note = document.getElementById('hero-note');
                const heroImage = document.getElementById('hero-image');
                const dots = document.querySelectorAll('[data-hero-dot]');
                let activeSlide = 0;
                let heroImages = [];

                try {
                    heroImages = JSON.parse(heroImage?.dataset.heroImages || '[]');
                } catch (error) {
                    heroImages = [];
                }

                const showHeroImage = (index) => {
                    if (!heroImage || heroImages.length === 0) {
                        return;
                    }

                    const nextImage = heroImages[index % heroImages.length];

                    if (heroImage.getAttribute('src') === nextImage) {
                        return;
                    }

                    heroImage.classList.add('opacity-0');

                    setTimeout(() => {
                        heroImage.src = nextImage;
                        heroImage.classList.remove('opacity-0');
                    }, 250);
                };

                const showSlide = (index) => {
                    const slide = slides[index % slides.length];

                    label.textContent = slide.label;
                    title.textContent = slide.title;
                    subtitle.textContent = slide.subtitle;
                    note.textContent = slide.note;
                    showHeroImage(index);

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

                setInterval(() => showSlide((activeSlide + 1) % Math.max(dots.length, slides.length)), 6500);

                const welcomeModal = document.getElementById('welcome-modal');
                const closeWelcomeModalButtons = document.querySelectorAll('[data-close-welcome-modal]');
                const showWelcomeModal = () => {
                    welcomeModal?.classList.remove('hidden');
                    welcomeModal?.classList.add('flex');
                };
                const closeWelcomeModal = () => {
                    welcomeModal?.classList.add('hidden');
                    welcomeModal?.classList.remove('flex');
                };

                setTimeout(showWelcomeModal, 3000);

                closeWelcomeModalButtons.forEach((button) => {
                    button.addEventListener('click', closeWelcomeModal);
                });

                welcomeModal?.addEventListener('click', (event) => {
                    if (event.target === welcomeModal) {
                        closeWelcomeModal();
                    }
                });

                document.addEventListener('keydown', (event) => {
                    if (event.key === 'Escape') {
                        closeWelcomeModal();
                    }
                });
            });
        </script>
    </body>
</html>
