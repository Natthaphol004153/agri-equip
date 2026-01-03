<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Equipment;

class EquipmentSeeder extends Seeder
{
    public function run()
    {
        $equipments = [
            [
                'name' => 'รถไถ Kubota L5018 (คันที่ 1)', 'code' => 'TR-001', 
                'type' => 'tractor', 'rate' => 500, 'maintenance' => 500
            ],
            [
                'name' => 'รถไถ Kubota L5018 (คันที่ 2)', 'code' => 'TR-002', 
                'type' => 'tractor', 'rate' => 500, 'maintenance' => 500
            ],
            [
                'name' => 'รถเกี่ยวข้าว Yanmar YH850', 'code' => 'HV-001', 
                'type' => 'harvester', 'rate' => 1200, 'maintenance' => 300
            ],
            [
                'name' => 'โดรนพ่นยา DJI Agras T30', 'code' => 'DR-001', 
                'type' => 'drone', 'rate' => 800, 'maintenance' => 100
            ],
            [
                'name' => 'รถขุดเล็ก (Backhoe) PC30', 'code' => 'EX-001', 
                'type' => 'excavator', 'rate' => 1500, 'maintenance' => 600
            ],
        ];

        foreach ($equipments as $e) {
            Equipment::firstOrCreate(
                ['equipment_code' => $e['code']],
                [
                    'name' => $e['name'],
                    'type' => $e['type'],
                    'hourly_rate' => $e['rate'],
                    'maintenance_hour_threshold' => $e['maintenance'],
                    'current_hours' => rand(50, $e['maintenance'] - 50),
                    'current_status' => 'available',
                ]
            );
        }
    }
}