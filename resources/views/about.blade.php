<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="scroll-smooth">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="description" content="About NABAMS, the National Association of Business Administration and Management Students.">

        <title>About Us - NABAMS</title>
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
            <section class="relative overflow-hidden bg-[#0A2A6B] text-white">
                <div class="absolute inset-0 bg-[linear-gradient(115deg,rgba(10,42,107,0.98),rgba(10,42,107,0.9),rgba(31,167,116,0.74))]"></div>
                <div class="relative mx-auto grid max-w-7xl gap-10 px-4 py-16 sm:px-6 sm:py-20 lg:grid-cols-[1fr_0.85fr] lg:items-center lg:px-8">
                    <div>
                        <p class="inline-flex rounded-full border border-white/20 bg-white/10 px-4 py-2 text-sm font-black uppercase tracking-wide text-[#F5B400]">About NABAMS</p>
                        <h1 class="mt-6 max-w-4xl text-4xl font-black leading-tight text-white sm:text-5xl lg:text-6xl">Welcome to the National Association of Business Administration and Management Students</h1>
                        <p class="mt-6 max-w-2xl text-base leading-8 text-[#F2F2F2] sm:text-lg">
                            NABAMS is a student-focused association built to promote excellence, leadership, and professional growth among Business Administration and Management students.
                        </p>
                    </div>

                    <div class="rounded-lg border border-white/15 bg-white/10 p-5 shadow-2xl backdrop-blur">
                        <div class="rounded-lg bg-white p-4">
                            <img src="{{ asset('uploads/about/mrakingbade.jpg') }}" alt="Mr. Waliu Akingbade" class="aspect-[4/5] w-full rounded-lg object-cover">
                        </div>
                        <div class="mt-4 rounded-lg bg-[#F5B400] px-5 py-4 text-[#0A2A6B]">
                            <p class="text-sm font-black uppercase tracking-wide">HOD</p>
                            <h2 class="mt-1 text-2xl font-black">Mr. Waliu Olorunwa Akingbade</h2>
                        </div>
                    </div>
                </div>
            </section>

            <section class="bg-white py-16 sm:py-20">
                <div class="mx-auto grid max-w-7xl gap-12 px-4 sm:px-6 lg:grid-cols-[0.8fr_1.2fr] lg:px-8">
                    <aside class="lg:sticky lg:top-28 lg:self-start">
                        <p class="text-sm font-black uppercase tracking-wide text-[#F5B400]">Who We Are</p>
                        <h2 class="mt-3 text-3xl font-black text-[#0A2A6B] sm:text-4xl">A platform for learning, leadership, and opportunity.</h2>
                        <div class="mt-6 grid gap-4">
                            <div class="rounded-lg border border-[#0A2A6B]/10 bg-[#F2F2F2] p-5">
                                <p class="text-3xl font-black text-[#1FA774]">01</p>
                                <h3 class="mt-3 font-black text-[#0A2A6B]">Network</h3>
                                <p class="mt-2 text-sm leading-6 text-[#2E2E2E]/75">Connecting students with peers, educators, alumni, and professionals.</p>
                            </div>
                            <div class="rounded-lg border border-[#0A2A6B]/10 bg-[#F2F2F2] p-5">
                                <p class="text-3xl font-black text-[#1FA774]">02</p>
                                <h3 class="mt-3 font-black text-[#0A2A6B]">Grow</h3>
                                <p class="mt-2 text-sm leading-6 text-[#2E2E2E]/75">Supporting academic confidence, practical skills, and career direction.</p>
                            </div>
                        </div>
                    </aside>

                    <div class="space-y-8 text-base leading-8 text-[#2E2E2E]/78">
                        <div class="rounded-lg border border-[#0A2A6B]/10 bg-white p-6 shadow-sm">
                            <h3 class="text-2xl font-black text-[#0A2A6B]">Our Story</h3>
                            <p class="mt-4">
                                Welcome to the National Association of Business Administration and Management Students (NABAMS). Established with the vision of fostering excellence in business administration and management, NABAMS is a dynamic organization dedicated to empowering students who are pursuing these disciplines.
                            </p>
                            <p class="mt-4">
                                We believe that students grow faster when they have the right community around them. Through seminars, workshops, competitions, leadership activities, and networking events, NABAMS creates room for members to learn beyond the classroom and prepare for the evolving world of business.
                            </p>
                        </div>

                        <div class="rounded-lg border border-[#0A2A6B]/10 bg-[#F2F2F2] p-6">
                            <h3 class="text-2xl font-black text-[#0A2A6B]">What We Do</h3>
                            <p class="mt-4">
                                Our association provides platforms where students can build confidence, develop useful skills, exchange ideas, and discover opportunities for academic and professional advancement. We are committed to programmes that sharpen leadership, communication, entrepreneurship, teamwork, and problem-solving abilities.
                            </p>
                            <p class="mt-4">
                                NABAMS is driven by students, educators, and industry-minded supporters who care about the success of every member. Whether a student is just starting out or already building a clear career path, the association offers support, guidance, and meaningful participation.
                            </p>
                        </div>

                        <div class="grid gap-5 md:grid-cols-3">
                            <div class="rounded-lg bg-[#0A2A6B] p-5 text-white">
                                <h3 class="font-black text-[#F5B400]">Vision</h3>
                                <p class="mt-3 text-sm leading-6 text-[#F2F2F2]/80">To raise excellent, confident, and responsible business leaders.</p>
                            </div>
                            <div class="rounded-lg bg-[#0A2A6B] p-5 text-white">
                                <h3 class="font-black text-[#F5B400]">Mission</h3>
                                <p class="mt-3 text-sm leading-6 text-[#F2F2F2]/80">To empower students through learning, welfare, networking, and representation.</p>
                            </div>
                            <div class="rounded-lg bg-[#0A2A6B] p-5 text-white">
                                <h3 class="font-black text-[#F5B400]">Motto</h3>
                                <p class="mt-3 text-sm leading-6 text-[#F2F2F2]/80">NABAMS Leads. Others Follow.</p>
                            </div>
                        </div>

                        <div class="rounded-lg border border-[#1FA774]/20 bg-[#1FA774]/10 p-6">
                            <h3 class="text-2xl font-black text-[#0A2A6B]">Our Commitment</h3>
                            <p class="mt-4">
                                We remain committed to building an association where every member feels represented, informed, and encouraged to aim higher. NABAMS exists to make the student experience richer, more practical, and more connected to the future of business.
                            </p>
                            <a href="{{ route('register') }}" class="mt-6 inline-flex rounded-lg bg-[#1FA774] px-6 py-3 text-sm font-black text-white transition hover:bg-[#198b61]">Join NABAMS</a>
                        </div>
                    </div>
                </div>
            </section>

            @include('partials.site.registration-cta')
        </main>

        @include('partials.site.footer')
    </body>
</html>
