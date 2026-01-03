<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // ⚠️ ล้างข้อมูลเก่าแบบหมดจด (Disable Foreign Key Check)
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        
        // Truncate tables
        \App\Models\User::truncate();
        \App\Models\Customer::truncate();
        \App\Models\Equipment::truncate();
        \App\Models\Booking::truncate();
        \App\Models\FuelLog::truncate();
        \App\Models\MaintenanceLog::truncate();
        \App\Models\FuelTank::truncate();      // เพิ่มใหม่
        \App\Models\FuelPurchase::truncate();  // เพิ่มใหม่
        
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');

        $this->command->info('🚀 เริ่มต้นการ Seed ข้อมูลระบบ Agri-Equip...');

        // เรียกใช้ Seeder ย่อยทีละตัวตามลำดับ
        $this->call([
            UserSeeder::class,       // 1. สร้างคน
            CustomerSeeder::class,   // 2. สร้างลูกค้า
            EquipmentSeeder::class,  // 3. สร้างรถ
            FuelSeeder::class,       // 4. สร้างระบบน้ำมัน (ถัง+สต็อก+ประวัติเติม)
            BookingSeeder::class,    // 5. สร้างงาน (Jobs)
            MaintenanceSeeder::class,// 6. สร้างประวัติซ่อม
        ]);

        $this->command->info('✅ เสร็จสิ้น! ข้อมูลตัวอย่างพร้อมใช้งานแล้วครับ');
    }
}