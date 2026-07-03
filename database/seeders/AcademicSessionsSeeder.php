<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class AcademicSessionsSeeder extends Seeder
{
    public function run(): void
    {
        $sessions = [
            [
                'name'           => '2024-2025',
                'starts_at_year' => 2024,
                'ends_at_year'   => 2025,
                'is_current'     => 'No',
                'is_active'      => 'Yes',
            ],
            [
                'name'           => '2025-2026',
                'starts_at_year' => 2025,
                'ends_at_year'   => 2026,
                'is_current'     => 'Yes',
                'is_active'      => 'Yes',
            ],
        ];

        foreach ($sessions as $session) {
            DB::table('academic_sessions')->insertOrIgnore([
                'name'           => $session['name'],
                'starts_at_year' => $session['starts_at_year'],
                'ends_at_year'   => $session['ends_at_year'],
                'is_current'     => $session['is_current'],
                'is_active'      => $session['is_active'],
                'created_at'     => now(),
                'updated_at'     => now(),
            ]);
        }
    }
}
