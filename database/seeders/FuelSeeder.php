<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\FuelTank;
use App\Models\FuelPurchase;
use App\Models\FuelLog;
use App\Models\Equipment;
use App\Models\User;
use Carbon\Carbon;

class FuelSeeder extends Seeder
{
    public function run()
    {
        // 1. สร้างถังน้ำมัน (Tanks)
        $tank1 = FuelTank::create([
            'name' => 'ถังใหญ่ (ดีเซล B7)',
            'capacity' => 5000,
            'current_balance' => 0,
            'average_price' => 0,
        ]);

        $tank2 = FuelTank::create([
            'name' => 'ถังสำรอง (หลังอู่)',
            'capacity' => 2000,
            'current_balance' => 0,
            'average_price' => 0,
        ]);

        // 2. จำลองการซื้อน้ำมันเข้า (Stock In)
        // ซื้อรอบ 1: ถูกหน่อย
        $this->addStock($tank1, 2000, 29.50, Carbon::now()->subMonths(2));
        // ซื้อรอบ 2: แพงขึ้น
        $this->addStock($tank1, 1500, 31.00, Carbon::now()->subMonth());
        
        // เติมถังสำรอง
        $this->addStock($tank2, 1000, 30.00, Carbon::now()->subWeeks(3));

        // 3. จำลองการเบิกใช้ (Usage Logs)
        $equipments = Equipment::all();
        $users = User::where('role', 'staff')->get();

        // Loop สร้าง Log การเติมน้ำมัน 20 รายการ
        for ($i = 0; $i < 20; $i++) {
            $isInternal = rand(0, 1) == 1; // สุ่มว่าเติมถังบริษัท หรือ ปั๊ม
            $eq = $equipments->random();
            $user = $users->random();
            $liters = rand(20, 60);

            if ($isInternal) {
                // Case: เติมถังบริษัท (Internal)
                $tank = $tank1; // ตัดถังใหญ่
                if ($tank->current_balance >= $liters) {
                    $cost = $liters * $tank->average_price;
                    $tank->decrement('current_balance', $liters);
                    
                    FuelLog::create([
                        'equipment_id' => $eq->id,
                        'user_id' => $user->id,
                        'fuel_source' => 'internal',
                        'fuel_tank_id' => $tank->id,
                        'amount' => $cost,
                        'liters' => $liters,
                        'refill_date' => Carbon::now()->subDays(rand(1, 60)),
                        'note' => 'เติมจากถังบริษัท (Seeder)',
                    ]);
                }
            } else {
                // Case: เติมปั๊ม (External)
                FuelLog::create([
                    'equipment_id' => $eq->id,
                    'user_id' => $user->id,
                    'fuel_source' => 'external',
                    'amount' => $liters * 32.50, // สมมติราคาปั๊ม
                    'liters' => $liters,
                    'refill_date' => Carbon::now()->subDays(rand(1, 60)),
                    'note' => 'เติมปั๊ม ปตท. (Seeder)',
                ]);
            }
        }
    }

    private function addStock($tank, $liters, $price, $date)
    {
        $totalCost = $liters * $price;
        $oldValue = $tank->current_balance * $tank->average_price;
        $totalLiters = $tank->current_balance + $liters;
        $newAvg = ($oldValue + $totalCost) / $totalLiters;

        FuelPurchase::create([
            'fuel_tank_id' => $tank->id,
            'liters' => $liters,
            'price_per_liter' => $price,
            'total_cost' => $totalCost,
            'purchase_date' => $date,
            'supplier' => 'Seeder Oil Supply',
        ]);

        $tank->update([
            'current_balance' => $totalLiters,
            'average_price' => $newAvg
        ]);
    }
}