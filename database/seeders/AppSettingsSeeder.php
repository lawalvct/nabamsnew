<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class AppSettingsSeeder extends Seeder
{
    public function run(): void
    {
        $settings = [
            [
                'name'          => 'Nomba Client ID',
                'slug'          => 'nombaClientID',
                'value'         => '927a757f-fffd-4d2d-9cbd-9a40cf82b8ec',
                'setting_group' => 'payment',
            ],
            [
                'name'          => 'Nomba Private Key',
                'slug'          => 'nombaPrivatekey',
                'value'         => '/HzQs97dVyvtF4rWAxIqHx3LXh/aJERFuqR/KBZrd83WqE2e/z...',
                'setting_group' => 'payment',
            ],
            [
                'name'          => 'Nomba Account ID',
                'slug'          => 'nombaAccountID',
                'value'         => '522ac9b8-6c22-41ba-8a12-f547008cceb3',
                'setting_group' => 'payment',
            ],
            [
                'name'          => 'Election',
                'slug'          => 'election',
                'value'         => 'On',
                'setting_group' => 'general',
            ],
            [
                'name'          => 'Member Registration',
                'slug'          => 'registration',
                'value'         => 'On',
                'setting_group' => 'general',
            ],
            [
                'name'          => 'Contest Time',
                'slug'          => 'contest',
                'value'         => 'Off',
                'setting_group' => 'general',
            ],
            [
                'name'          => 'Email Address',
                'slug'          => 'email',
                'value'         => 'admin@email.com',
                'setting_group' => 'contact',
            ],
            [
                'name'          => 'Mobile',
                'slug'          => 'mobile',
                'value'         => '9874563210',
                'setting_group' => 'contact',
            ],
            [
                'name'          => 'Address',
                'slug'          => 'address',
                'value'         => '216, Regal Indl Estate, Acharya Dhonde Marg, Sewri...',
                'setting_group' => 'contact',
            ],
        ];

        foreach ($settings as $setting) {
            DB::table('app_settings')->insertOrIgnore([
                'name'          => $setting['name'],
                'slug'          => $setting['slug'],
                'value'         => $setting['value'],
                'setting_group' => $setting['setting_group'],
                'active'        => 'Yes',
                'created_at'    => now(),
                'updated_at'    => now(),
            ]);
        }
    }
}
