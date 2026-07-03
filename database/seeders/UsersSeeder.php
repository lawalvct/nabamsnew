<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use App\Models\User;

class UsersSeeder extends Seeder
{
    public function run(): void
    {
        $members = [
            ['firstname' => 'Chukwuemeka', 'lastname' => 'Okonkwo',   'gender' => 'Male',   'dob' => '2001-03-14', 'matno' => 'HBAF/23/0001', 'level' => 'HND1', 'level_id' => 4, 'type' => 'Regular'],
            ['firstname' => 'Adaeze',      'lastname' => 'Nwosu',      'gender' => 'Female', 'dob' => '2002-07-22', 'matno' => 'HBAF/23/0002', 'level' => 'HND1', 'level_id' => 4, 'type' => 'Regular'],
            ['firstname' => 'Babatunde',   'lastname' => 'Fashola',    'gender' => 'Male',   'dob' => '2000-11-05', 'matno' => 'NBAF/22/0003', 'level' => 'HND2', 'level_id' => 5, 'type' => 'Regular'],
            ['firstname' => 'Ngozi',       'lastname' => 'Eze',        'gender' => 'Female', 'dob' => '2001-01-30', 'matno' => 'NBAF/22/0004', 'level' => 'HND2', 'level_id' => 5, 'type' => 'Regular'],
            ['firstname' => 'Emeka',       'lastname' => 'Obiora',     'gender' => 'Male',   'dob' => '2003-05-18', 'matno' => 'HBAF/24/0005', 'level' => 'ND2',  'level_id' => 2, 'type' => 'Regular'],
            ['firstname' => 'Funmilayo',   'lastname' => 'Adeyemi',    'gender' => 'Female', 'dob' => '2002-09-09', 'matno' => 'HBAF/24/0006', 'level' => 'ND2',  'level_id' => 2, 'type' => 'Regular'],
            ['firstname' => 'Oluwaseun',   'lastname' => 'Balogun',    'gender' => 'Male',   'dob' => '2001-12-25', 'matno' => 'NBAF/23/0007', 'level' => 'HND1', 'level_id' => 4, 'type' => 'Part-time'],
            ['firstname' => 'Chidinma',    'lastname' => 'Igwe',       'gender' => 'Female', 'dob' => '2000-04-17', 'matno' => 'NBAF/21/0008', 'level' => 'HND3', 'level_id' => 6, 'type' => 'Regular'],
            ['firstname' => 'Tochukwu',    'lastname' => 'Anyanwu',    'gender' => 'Male',   'dob' => '1999-08-03', 'matno' => 'HBAF/21/0009', 'level' => 'GRADUATE', 'level_id' => 7, 'type' => 'Alumni'],
            ['firstname' => 'Amaka',       'lastname' => 'Obi',        'gender' => 'Female', 'dob' => '2003-02-11', 'matno' => 'HBAF/24/0010', 'level' => 'ND1',  'level_id' => 1, 'type' => 'Regular'],
            ['firstname' => 'Segun',       'lastname' => 'Adeleke',    'gender' => 'Male',   'dob' => '2002-06-28', 'matno' => 'NBAF/23/0011', 'level' => 'ND3',  'level_id' => 3, 'type' => 'Regular'],
            ['firstname' => 'Ifeoma',      'lastname' => 'Chukwu',     'gender' => 'Female', 'dob' => '2001-10-15', 'matno' => 'NBAF/23/0012', 'level' => 'ND3',  'level_id' => 3, 'type' => 'Regular'],
            ['firstname' => 'Kayode',      'lastname' => 'Ogundimu',   'gender' => 'Male',   'dob' => '2000-03-07', 'matno' => 'HBAF/22/0013', 'level' => 'HND2', 'level_id' => 5, 'type' => 'Part-time'],
            ['firstname' => 'Blessing',    'lastname' => 'Okoro',      'gender' => 'Female', 'dob' => '2002-12-01', 'matno' => 'HBAF/23/0014', 'level' => 'HND1', 'level_id' => 4, 'type' => 'Regular'],
            ['firstname' => 'Chidi',       'lastname' => 'Nnamdi',     'gender' => 'Male',   'dob' => '2003-07-19', 'matno' => 'NBAF/24/0015', 'level' => 'ND2',  'level_id' => 2, 'type' => 'Regular'],
            ['firstname' => 'Yetunde',     'lastname' => 'Afolabi',    'gender' => 'Female', 'dob' => '2001-05-23', 'matno' => 'NBAF/22/0016', 'level' => 'HND2', 'level_id' => 5, 'type' => 'Regular'],
            ['firstname' => 'Obinna',      'lastname' => 'Uchenna',    'gender' => 'Male',   'dob' => '1999-11-12', 'matno' => 'HBAF/21/0017', 'level' => 'GRADUATE', 'level_id' => 7, 'type' => 'Alumni'],
            ['firstname' => 'Chiamaka',    'lastname' => 'Okafor',     'gender' => 'Female', 'dob' => '2002-08-06', 'matno' => 'HBAF/23/0018', 'level' => 'HND1', 'level_id' => 4, 'type' => 'Regular'],
            ['firstname' => 'Taiwo',       'lastname' => 'Olawale',    'gender' => 'Male',   'dob' => '2003-04-30', 'matno' => 'NBAF/24/0019', 'level' => 'ND1',  'level_id' => 1, 'type' => 'Regular'],
            ['firstname' => 'Nneka',       'lastname' => 'Ejike',      'gender' => 'Female', 'dob' => '2000-09-21', 'matno' => 'NBAF/22/0020', 'level' => 'HND3', 'level_id' => 6, 'type' => 'Regular'],
        ];

        foreach ($members as $i => $m) {
            User::create([
                'firstname'      => $m['firstname'],
                'lastname'       => $m['lastname'],
                'gender'         => $m['gender'],
                'dob'            => $m['dob'],
                'email'          => strtolower($m['firstname'].'.'.$m['lastname']).'@nabams.test',
                'phone'          => '080'.str_pad($i + 10000001, 8, '0', STR_PAD_LEFT),
                'matno'          => $m['matno'],
                'department'     => 'Business Administration & Management',
                'academic_level' => $m['level'],
                'level_id'       => $m['level_id'],
                'member_type'    => $m['type'],
                'password'       => Hash::make('password'),
                'is_active'      => 'Yes',
                'is_ban'         => 'No',
                'fee_paid'       => 'No',
                'role'           => 'Member',
                'user_role_id'   => 2,
            ]);
        }
    }
}
