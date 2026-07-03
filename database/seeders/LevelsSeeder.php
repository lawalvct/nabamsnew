<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class LevelsSeeder extends Seeder
{
    public function run(): void
    {
        $levels = ['ND1', 'ND2', 'ND3', 'HND1', 'HND2', 'HND3', 'GRADUATE'];

        foreach ($levels as $order => $name) {
            DB::table('levels')->insertOrIgnore([
                'name'       => $name,
                'sort_order' => $order + 1,
                'is_active'  => 'Yes',
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }
}
