<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="scroll-smooth">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">

        <title>Dashboard - NABAMS</title>
        <link rel="icon" type="image/png" href="{{ asset('favicon.png') }}">

        @fonts

        @if (file_exists(public_path('build/manifest.json')) || file_exists(public_path('hot')))
            @vite(['resources/css/app.css', 'resources/js/app.js'])
        @else
            <script src="https://cdn.jsdelivr.net/npm/@tailwindcss/browser@4"></script>
        @endif
    </head>
    <body class="min-h-screen bg-[#F2F2F2] font-sans text-[#2E2E2E] antialiased">
        @php
            $isAdmin = strtolower((string) $user->role) === 'admin';
            $displayName = $user->name ?: $user->firstname ?: 'NABAMS Member';
            $initials = collect(explode(' ', trim($displayName)))
                ->filter()
                ->take(2)
                ->map(fn ($name) => strtoupper(substr($name, 0, 1)))
                ->implode('');

            $adminMenus = [
                ['label' => 'Academic Session', 'href' => '#academic-session', 'icon' => 'AS'],
                ['label' => 'Transactions', 'href' => '#transactions', 'icon' => 'TR'],
                ['label' => 'CMS', 'href' => '#cms', 'icon' => 'CM'],
                ['label' => 'Resources', 'href' => '#resources', 'icon' => 'RS'],
                ['label' => 'Election', 'href' => '#election', 'icon' => 'EL'],
                ['label' => 'Contest', 'href' => '#contest', 'icon' => 'CO'],
                ['label' => 'Members', 'href' => '#members', 'icon' => 'MB'],
                ['label' => 'Final Year Projects', 'href' => '#final-year-projects', 'icon' => 'FP'],
                ['label' => 'Levels', 'href' => '#levels', 'icon' => 'LV'],
                ['label' => 'Price Settings', 'href' => '#price-settings', 'icon' => 'PS'],
                ['label' => 'Admins', 'href' => '#admins', 'icon' => 'AD'],
                ['label' => 'Profile', 'href' => '#profile', 'icon' => 'PR'],
            ];

            $memberMenus = [
                ['label' => 'Dashboard', 'href' => '#dashboard', 'icon' => 'DB'],
                ['label' => 'Transactions', 'href' => '#transactions', 'icon' => 'TR'],
                ['label' => 'Resources', 'href' => '#resources', 'icon' => 'RS'],
                ['label' => 'Election', 'href' => '#election', 'icon' => 'EL'],
                ['label' => 'Contest', 'href' => '#contest', 'icon' => 'CO'],
                ['label' => 'My Project', 'href' => '#my-project', 'icon' => 'MP'],
                ['label' => 'Fees', 'href' => '#fees', 'icon' => 'FE'],
                ['label' => 'Profile', 'href' => '#profile', 'icon' => 'PR'],
            ];

            $menus = $isAdmin ? $adminMenus : $memberMenus;
            $mobileMenus = $isAdmin
                ? collect($menus)->whereIn('label', ['Academic Session', 'Transactions', 'Members', 'Resources', 'Profile'])->values()
                : collect($menus)->whereIn('label', ['Dashboard', 'Transactions', 'Resources', 'Fees', 'Profile'])->values();

            $quickStats = $isAdmin
                ? [
                    ['label' => 'Members', 'value' => '4,612', 'hint' => 'Legacy user base', 'tone' => 'green'],
                    ['label' => 'Fee Status', 'value' => 'Active', 'hint' => 'Payment tracking ready', 'tone' => 'blue'],
                    ['label' => 'Contest', 'value' => 'Open', 'hint' => 'Election module prepared', 'tone' => 'gold'],
                ]
                : [
                    ['label' => 'Role', 'value' => $user->role, 'hint' => 'Account access level', 'tone' => 'blue'],
                    ['label' => 'Membership', 'value' => $user->member_type, 'hint' => 'Registered member type', 'tone' => 'gold'],
                    ['label' => 'Fee Paid', 'value' => $user->fee_paid, 'hint' => 'Current payment status', 'tone' => $user->fee_paid === 'Yes' ? 'green' : 'blue'],
                ];
        @endphp

        <div class="min-h-screen lg:grid lg:grid-cols-[280px_1fr]">
            @include('partials.dashboard.sidebar')

            <div class="lg:col-start-2">
                @include('partials.dashboard.topbar')

                <main class="px-4 pb-28 pt-6 sm:px-6 lg:px-8 lg:pb-10">
                    @include('partials.dashboard.overview')
                    @include('partials.dashboard.stat-cards')
                    @include('partials.dashboard.workspace')
                    @include('partials.dashboard.next-step')
                </main>
            </div>
        </div>

        @include('partials.dashboard.mobile-nav')
    </body>
</html>
