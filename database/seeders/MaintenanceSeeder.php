<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\MaintenanceLog;
use App\Models\Equipment;
use Carbon\Carbon;

class MaintenanceSeeder extends Seeder
{
    public function run()
    {
        $equipments = Equipment::all();

        foreach ($equipments as $eq) {
            // สุ่มสร้างประวัติซ่อม 1-3 รายการต่อคัน
            $logsCount = rand(1, 3);
            
            for ($i = 0; $i < $logsCount; $i++) {
                $date = Carbon::now()->subDays(rand(10, 100));
                
                MaintenanceLog::create([
                    'equipment_id' => $eq->id,
                    'maintenance_type' => rand(0, 1) ? 'preventive' : 'corrective',
                    'description' => 'เช็คระยะตามกำหนด / เปลี่ยนถ่ายน้ำมันเครื่อง',
                    'status' => 'completed',
                    
                    // ✅ แก้ไข: เปลี่ยนจาก cost เป็น total_cost
                    'total_cost' => rand(500, 5000), 
                    
                    // ✅ แก้ไข: เปลี่ยนจาก technician_name เป็น service_provider
                    'service_provider' => 'ช่างศูนย์บริการ (Seeder)', 
                    
                    'maintenance_date' => $date,
                    'completion_date' => $date->copy()->addHours(4),
                ]);
            }
        }
    }
}