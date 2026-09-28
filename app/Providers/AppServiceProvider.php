<?php

namespace App\Providers;

use App\Models\Payment;
use App\Support\PaymentRequirement;
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

            $pendingPayments = $isAdmin ? once(fn () => Payment::query()->where('status', Payment::STATUS_PENDING)->count()) : 0;
            $paymentRequirement = $isAdmin ? null : (request()->attributes->get('paymentRequirement') ?? PaymentRequirement::for($user));

            $adminMenus = [
                ['label' => 'Dashboard', 'href' => route('dashboard'), 'icon' => 'dashboard', 'active' => request()->routeIs('dashboard')],
                ['label' => 'Academic Session', 'href' => route('admin.academic-sessions.index'), 'icon' => 'academic-session', 'active' => request()->routeIs('admin.academic-sessions.*')],
                ['label' => 'Transactions', 'href' => route('admin.payments.index'), 'icon' => 'transactions', 'active' => request()->routeIs('admin.payments.*'), 'badge' => $pendingPayments],
                ['label' => 'CMS', 'href' => '#cms', 'icon' => 'cms', 'active' => false],
                ['label' => 'Resources', 'href' => '#resources', 'icon' => 'resources', 'active' => false],
                ['label' => 'Election', 'href' => route('admin.election.positions.index'), 'icon' => 'election', 'active' => request()->routeIs('admin.election.*')],
                ['label' => 'Contest', 'href' => '#contest', 'icon' => 'contest', 'active' => false],
                ['label' => 'Members', 'href' => route('admin.members.index'), 'icon' => 'members', 'active' => request()->routeIs('admin.members.*')],
                ['label' => 'Final Year Projects', 'href' => '#final-year-projects', 'icon' => 'projects', 'active' => false],
                ['label' => 'Levels', 'href' => '#levels', 'icon' => 'levels', 'active' => false],
                ['label' => 'Price Settings', 'href' => route('admin.price-settings.index'), 'icon' => 'price-settings', 'active' => request()->routeIs('admin.price-settings.*')],
                ['label' => 'Bank Accounts', 'href' => route('admin.bank-accounts.index'), 'icon' => 'bank-accounts', 'active' => request()->routeIs('admin.bank-accounts.*')],
                ['label' => 'Settings', 'href' => route('admin.settings.edit'), 'icon' => 'settings', 'active' => request()->routeIs('admin.settings.*')],
                ['label' => 'Admins', 'href' => route('admin.admins.index'), 'icon' => 'admins', 'active' => request()->routeIs('admin.admins.*')],
                ['label' => 'Profile', 'href' => route('profile.edit'), 'icon' => 'profile', 'active' => request()->routeIs('profile.*')],
            ];

            $memberMenus = [
                ['label' => 'Dashboard', 'href' => route('dashboard'), 'icon' => 'dashboard', 'active' => request()->routeIs('dashboard')],
                ['label' => 'Transactions', 'href' => route('payments.index'), 'icon' => 'transactions', 'active' => request()->routeIs('payments.index')],
                ['label' => 'Resources', 'href' => '#resources', 'icon' => 'resources', 'active' => false],
                ['label' => 'Election', 'href' => route('election.index'), 'icon' => 'election', 'active' => request()->routeIs('election.*')],
                ['label' => 'Contest', 'href' => '#contest', 'icon' => 'contest', 'active' => false],
                ['label' => 'My Project', 'href' => '#my-project', 'icon' => 'projects', 'active' => false],
                ['label' => 'Fees', 'href' => route('payments.create'), 'icon' => 'fees', 'active' => request()->routeIs('payments.create')],
                ['label' => 'Profile', 'href' => route('profile.edit'), 'icon' => 'profile', 'active' => request()->routeIs('profile.*')],
            ];

            $menus = $isAdmin ? $adminMenus : $memberMenus;
            $mobileMenus = $isAdmin
                ? collect($menus)->whereIn('label', ['Dashboard', 'Transactions', 'Election', 'Members', 'Profile'])->values()
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
                    ['label' => 'Fee Paid', 'value' => $paymentRequirement->satisfied ? 'Yes' : 'No', 'hint' => $paymentRequirement->periodLabel() ?: 'Current payment status', 'tone' => $paymentRequirement->satisfied ? 'green' : 'blue'],
                ];

            $view->with(compact(
                'user',
                'isAdmin',
                'displayName',
                'initials',
                'menus',
                'mobileMenus',
                'quickStats',
                'paymentRequirement',
            ));
        });
    }
}
