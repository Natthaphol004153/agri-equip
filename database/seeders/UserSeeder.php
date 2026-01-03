<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run()
    {
        // 1. สร้าง Admin
        User::firstOrCreate(
            ['username' => 'admin'],
            [
                'name' => 'Administrator (Main)',
                'email' => 'admin@agritech.com',
                'password' => Hash::make('password'), // Pass: password
                'role' => 'admin',
            ]
        );

        // 2. สร้าง Staff (ทีมงาน)
        $staffs = [
            ['name' => 'ช่างสมชาย (Senior)', 'username' => 'somchai', 'pin' => '1111'],
            ['name' => 'ช่างวิชัย (Junior)', 'username' => 'wichai',  'pin' => '2222'],
            ['name' => 'คนขับยอดชาย',       'username' => 'yodchai', 'pin' => '3333'],
            ['name' => 'คนขับสมศักดิ์',       'username' => 'somsak',  'pin' => '4444'],
            ['name' => 'ธุรการสาวสวย',       'username' => 'admin_asst', 'pin' => '5555'],
        ];

        foreach ($staffs as $s) {
            User::firstOrCreate(
                ['username' => $s['username']],
                [
                    'name' => $s['name'],
                    'email' => $s['username'] . '@agritech.com',
                    'password' => Hash::make('password'),
                    'role' => 'staff',
                    'pin' => Hash::make($s['pin']),
                ]
            );
        }
    }
}