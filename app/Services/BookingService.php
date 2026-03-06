<?php

namespace App\Services;

use App\Models\Booking;
use App\Models\Equipment;
use App\Models\MaintenanceLog; // ✅ เปลี่ยนเป็น MaintenanceLog
use App\Models\Leave;
use Carbon\Carbon;
use Exception;
use Illuminate\Support\Facades\DB;

class BookingService
{
    // --- ฟังก์ชันหลัก: สร้างการจอง ---
    public function createBooking(array $data)
    {
        return DB::transaction(function () use ($data) {
            $start = $this->normalizeBookingDate($data['scheduled_start']);
            $end = $this->normalizeBookingDate($data['scheduled_end']);
            $equipmentId = $data['equipment_id'];
            $staffId = $data['assigned_staff_id'] ?? null;

            $data['scheduled_start'] = $start->format('Y-m-d H:i:s');
            $data['scheduled_end'] = $end->format('Y-m-d H:i:s');

            // 1. ดึงข้อมูล Equipment
            $equipment = Equipment::find($equipmentId);
            if (!$equipment) {
                throw new Exception('ไม่พบข้อมูลเครื่องจักร');
            }

            // 🟢 2. ปรับ Logic การคำนวณราคาใหม่ (ใช้ระบบพื้นที่ไร่)
            // ใช้พื้นที่ (จากฟอร์ม) x ราคาต่อไร่ (จากเครื่องจักร)
            $area = $data['actual_area'] ?? ($data['estimated_area'] ?? 0);
            $pricePerRai = $equipment->price_per_rai ?? 0;
            
            $data['price_per_rai_at_booking'] = $pricePerRai; // Snapshot ราคาไว้
            $data['total_price'] = $area * $pricePerRai; // คำนวณยอดเงินรวม

            // 3. ตรวจสอบเครื่องจักร
            $equipmentCheck = $this->checkEquipmentAvailability($equipment, $start, $end);
            if (!$equipmentCheck['available']) {
                throw new Exception($equipmentCheck['message']);
            }

            // 4. ตรวจสอบพนักงาน
            if ($staffId) {
                $staffCheck = $this->checkStaffAvailability($staffId, $start, $end);
                if (!$staffCheck['available']) {
                    throw new Exception($staffCheck['message']);
                }
            }

            // 5. สร้างเลข Job Number
            $data['job_number'] = $this->generateJobNumber();

            // 6. กำหนดสถานะเริ่มต้น
            $data['status'] = $data['status'] ?? 'scheduled';
            $data['payment_status'] = $data['payment_status'] ?? 'pending';
            
            // คำนวณมัดจำ (30% ของยอดรวมที่คำนวณใหม่)
            if (!isset($data['deposit_amount'])) {
                $data['deposit_amount'] = $data['total_price'] * 0.30;
            }

            // 7. บันทึกข้อมูล
            return Booking::create($data);
        });
    }

    // --- เช็คเครื่องจักร ---
    public function checkEquipmentAvailability($equipment, $start, $end)
    {
        $status = $equipment->current_status;

        // เช็คสถานะตัวรถเบื้องต้น
        if (in_array($status, ['out_of_service', 'retired', 'inactive'])) {
            return ['available' => false, 'message' => 'เครื่องจักรนี้ถูกระงับการใช้งานในระบบ'];
        }

        // เช็คคิวงานซ้อน
        $isBooked = Booking::where('equipment_id', $equipment->id)
            ->whereNotIn('status', ['cancelled', 'completed'])
            ->where(function ($query) use ($start, $end) {
                $query->where('scheduled_start', '<', $end)
                    ->where('scheduled_end', '>', $start);
            })->exists();

        if ($isBooked) {
            return ['available' => false, 'message' => 'ช่วงเวลานี้มีลูกค้าท่านอื่นจองเครื่องจักรแล้ว'];
        }

        // 🟢 แก้ไข: เช็คคิวซ่อมบำรุงจาก MaintenanceLog
        // เช็คว่ารถติดคิวซ่อมที่ "ยังซ่อมไม่เสร็จ" หรือ "มีคิวซ่อมทับเวลาจอง" หรือไม่
        $isUnderMaintenance = MaintenanceLog::where('equipment_id', $equipment->id)
            ->where(function ($query) use ($start, $end) {
                // กรณีที่ 1: กำลังซ่อมอยู่ (สถานะไม่ใช่ completed)
                $query->where('status', '!=', 'completed')
                // กรณีที่ 2: มีตารางซ่อมทับกับช่วงเวลาที่จอง
                ->orWhere(function ($q) use ($start, $end) {
                    $q->where('maintenance_date', '<', $end)
                      ->where('completion_date', '>', $start);
                });
            })->exists();

        if ($isUnderMaintenance) {
            return ['available' => false, 'message' => 'เครื่องจักรติดภารกิจซ่อมบำรุงในช่วงเวลานี้'];
        }

        return ['available' => true];
    }

    // --- เช็คพนักงาน ---
    public function checkStaffAvailability($staffId, $start, $end)
    {
        $isBusy = Booking::where('assigned_staff_id', $staffId)
            ->whereNotIn('status', ['cancelled', 'completed'])
            ->where(function ($query) use ($start, $end) {
                $query->where('scheduled_start', '<', $end)
                    ->where('scheduled_end', '>', $start);
            })->exists();

        if ($isBusy) {
            return ['available' => false, 'message' => 'พนักงานรายนี้ติดภารกิจขับรถคันอื่นในช่วงเวลาดังกล่าว'];
        }

        // เช็คการลา (ถ้ามี Model Leave)
        $isOnLeave = Leave::where('user_id', $staffId)
            ->where('status', 'approved')
            ->where(function ($query) use ($start, $end) {
                $query->where('start_date', '<', $end)
                    ->where('end_date', '>', $start);
            })->exists();

        if ($isOnLeave) {
            return ['available' => false, 'message' => 'พนักงานลางานในช่วงเวลานั้น'];
        }

        return ['available' => true];
    }

    // --- สร้างเลข Job ---
    private function generateJobNumber()
    {
        $prefix = 'JOB-' . Carbon::now()->format('Ymd') . '-';

        $latest = Booking::where('job_number', 'like', $prefix . '%')
            ->orderBy('id', 'desc')
            ->lockForUpdate()
            ->first();

        if (!$latest) {
            return $prefix . '001';
        }

        $number = intval(substr($latest->job_number, -3)) + 1;
        return $prefix . str_pad($number, 3, '0', STR_PAD_LEFT);
    }

    private function normalizeBookingDate($value): Carbon
    {
        $date = $value instanceof Carbon ? $value->copy() : Carbon::parse($value);

        if ($date->year >= 2400) {
            $date->subYears(543);
        }

        return $date;
    }
}