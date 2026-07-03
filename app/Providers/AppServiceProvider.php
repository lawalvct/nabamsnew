<?php

namespace App\Providers;

use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        View::composer([
            'layouts.dashboard',
            'dashboard',
            'admin.*',
            'partials.dashboard.*',
        ], function ($view): void {
            $user = auth()->user();

            if (! $user) {
                return;
            }

            $isAdmin = strtolower((string) $user->role) === 'admin';
            $displayName = $user->name ?: $user->firstname ?: 'NABAMS Member';
            $initials = collect(explode(' ', trim($displayName)))
                ->filter()
                ->take(2)
                ->map(fn ($name) => strtoupper(substr($name, 0, 1)))
                ->implode('');

            $adminMenus = [
                ['label' => 'Dashboard', 'href' => route('dashboard'), 'icon' => 'dashboard', 'active' => request()->routeIs('dashboard')],
                ['label' => 'Academic Session', 'href' => route('admin.academic-sessions.index'), 'icon' => 'academic-session', 'active' => request()->routeIs('admin.academic-sessions.*')],
                ['label' => 'Transactions', 'href' => '#transactions', 'icon' => 'transactions', 'active' => false],
                ['label' => 'CMS', 'href' => '#cms', 'icon' => 'cms', 'active' => false],
                ['label' => 'Resources', 'href' => '#resources', 'icon' => 'resources', 'active' => false],
                ['label' => 'Election', 'href' => route('admin.election.positions.index'), 'icon' => 'election', 'active' => request()->routeIs('admin.election.*')],
                ['label' => 'Contest', 'href' => '#contest', 'icon' => 'contest', 'active' => false],
                ['label' => 'Members', 'href' => '#members', 'icon' => 'members', 'active' => false],
                ['label' => 'Final Year Projects', 'href' => '#final-year-projects', 'icon' => 'projects', 'active' => false],
                ['label' => 'Levels', 'href' => '#levels', 'icon' => 'levels', 'active' => false],
                ['label' => 'Price Settings', 'href' => '#price-settings', 'icon' => 'price-settings', 'active' => false],
                ['label' => 'Settings', 'href' => route('admin.settings.edit'), 'icon' => 'settings', 'active' => request()->routeIs('admin.settings.*')],
                ['label' => 'Admins', 'href' => '#admins', 'icon' => 'admins', 'active' => false],
                ['label' => 'Profile', 'href' => route('profile.edit'), 'icon' => 'profile', 'active' => request()->routeIs('profile.*')],
            ];

            $memberMenus = [
                ['label' => 'Dashboard', 'href' => route('dashboard'), 'icon' => 'dashboard', 'active' => request()->routeIs('dashboard')],
                ['label' => 'Transactions', 'href' => '#transactions', 'icon' => 'transactions', 'active' => false],
                ['label' => 'Resources', 'href' => '#resources', 'icon' => 'resources', 'active' => false],
                ['label' => 'Election', 'href' => route('election.index'), 'icon' => 'election', 'active' => request()->routeIs('election.*')],
                ['label' => 'Contest', 'href' => '#contest', 'icon' => 'contest', 'active' => false],
                ['label' => 'My Project', 'href' => '#my-project', 'icon' => 'projects', 'active' => false],
                ['label' => 'Fees', 'href' => '#fees', 'icon' => 'fees', 'active' => false],
                ['label' => 'Profile', 'href' => route('profile.edit'), 'icon' => 'profile', 'active' => request()->routeIs('profile.*')],
            ];

            $menus = $isAdmin ? $adminMenus : $memberMenus;
            $mobileMenus = $isAdmin
                ? collect($menus)->whereIn('label', ['Dashboard', 'Academic Session', 'Election', 'Members', 'Profile'])->values()
                : collect($menus)->whereIn('label', ['Dashboard', 'Resources', 'Election', 'Fees', 'Profile'])->values();

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

            $view->with(compact(
                'user',
                'isAdmin',
                'displayName',
                'initials',
                'menus',
                'mobileMenus',
                'quickStats',
            ));
        });
    }
}
