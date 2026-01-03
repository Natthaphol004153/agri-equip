<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Booking;
use App\Models\Customer;
use App\Models\Equipment;
use App\Models\User;
use Carbon\Carbon;

class BookingSeeder extends Seeder
{
    public function run()
    {
        $customers = Customer::all();
        $equipments = Equipment::all();
        $staffs = User::where('role', 'staff')->get();

        if ($customers->isEmpty()) return;

        $startDate = Carbon::now()->subDays(60);
        $endDate = Carbon::now()->addDays(7);

        for ($date = $startDate->copy(); $date <= $endDate; $date->addDay()) {
            
            // 60% โอกาสที่จะมีงานในแต่ละวัน
            if (rand(1, 100) <= 60) {
                $jobsCount = rand(1, 3); // วันละ 1-3 งาน
                
                for ($i = 0; $i < $jobsCount; $i++) {
                    $eq = $equipments->random();
                    $cus = $customers->random();
                    $staff = $staffs->random();

                    $startHour = rand(8, 14);
                    $duration = rand(3, 8);
                    
                    $start = $date->copy()->setTime($startHour, 0);
                    $end = $start->copy()->addHours($duration);

                    $isPast = $date->lessThan(Carbon::now());
                    $status = $isPast ? 'completed' : 'scheduled';
                    $totalPrice = $duration * $eq->hourly_rate;

                    Booking::create([
                        'job_number' => 'JOB-' . $date->format('ymd') . '-' . rand(1000, 9999),
                        'customer_id' => $cus->id,
                        'equipment_id' => $eq->id,
                        'assigned_staff_id' => $staff->id,
                        'scheduled_start' => $start,
                        'scheduled_end' => $end,
                        'actual_start' => $isPast ? $start : null,
                        'actual_end' => $isPast ? $end : null,
                        'status' => $status,
                        'total_price' => $totalPrice,
                        'deposit_amount' => $totalPrice * 0.3,
                        'note' => 'Auto Generated Job',
                    ]);

                    // ถ้าเป็นอดีต ให้เพิ่มชั่วโมงทำงานให้รถด้วย
                    if ($isPast) {
                        $eq->increment('current_hours', $duration);
                    }
                }
            }
        }
    }
}