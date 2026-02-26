<?php
namespace App\Services;

use App\Models\Booking;
use App\Models\Equipment;
use App\Models\Maintenance;
use App\Models\Leave;
use Carbon\Carbon;
use Exception;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class BookingService
{
    // --- ฟังก์ชันหลัก: สร้างการจอง ---
    public function createBooking(array $data)
    {
        return DB::transaction(function () use ($data) {
            $start = Carbon::parse($data['scheduled_start']);
            $end = Carbon::parse($data['scheduled_end']);
            $equipmentId = $data['equipment_id'];
            $staffId = $data['assigned_staff_id'] ?? null;

            // ✅ ดึงข้อมูล Equipment ก่อนเพื่อเอามาคำนวณราคาและเช็คสถานะ
            $equipment = Equipment::find($equipmentId);
            if (!$equipment) {
                throw new Exception('ไม่พบข้อมูลเครื่องจักร');
            }

            // ✅ ให้ Backend คำนวณราคาเอง (ป้องกันหน้าเว็บแก้ราคามา)
            $days = $start->diffInDays($end) ?: 1; 
            // สมมติว่า Equipment model มีคอลัมน์ daily_rate (แก้เป็นชื่อคอลัมน์ที่คุณใช้จริงได้เลย)
            $calculatedPrice = ($equipment->daily_rate ?? 0) * $days; 
            $data['total_price'] = $calculatedPrice;

            // 1. ตรวจสอบเครื่องจักร (ส่ง Object ไปเลย จะได้ไม่ต้อง Query ซ้ำ)
            $equipmentCheck = $this->checkEquipmentAvailability($equipment, $start, $end);
            if (!$equipmentCheck['available']) {
                throw new Exception($equipmentCheck['message']);
            }

            // 2. ตรวจสอบพนักงาน
            if ($staffId) {
                $staffCheck = $this->checkStaffAvailability($staffId, $start, $end);
                if (!$staffCheck['available']) {
                    throw new Exception($staffCheck['message']);
                }
            }

            // 3. สร้างเลข Job Number (ปลอดภัยจาก Race Condition)
            $data['job_number'] = $this->generateJobNumber();

            // 4. กำหนดสถานะเริ่มต้น
            $data['status'] = 'scheduled';
            $data['payment_status'] = 'pending'; // รอชำระเงิน
            $data['deposit_amount'] = $data['total_price'] * 0.30; // มัดจำ 30%
            $data['payment_method'] = $data['payment_method'] ?? null; 

            // 5. บันทึกและส่งกลับคืน
            return Booking::create($data);
        });
    }

    // --- เช็คเครื่องจักร ---
    // ✅ รับเป็น Object Equipment แทน ID เพื่อลดการ Query ฐานข้อมูลซ้ำซ้อน
    public function checkEquipmentAvailability($equipment, $start, $end)
    {
        $status = $equipment->current_status;
        if (is_object($status) && property_exists($status, 'value')) {
            $status = $status->value;
        } elseif (is_object($status)) {
            $status = (string) $status;
        }

        if (in_array($status, ['out_of_service', 'retired', 'inactive'])) {
            return ['available' => false, 'message' => 'เครื่องจักรนี้ถูกระงับการใช้งาน (' . $status . ')'];
        }

        $isBooked = Booking::where('equipment_id', $equipment->id)
            ->whereNotIn('status', ['cancelled', 'completed'])
            ->where(function ($query) use ($start, $end) {
                $query->where('scheduled_start', '<', $end)
                    ->where('scheduled_end', '>', $start);
            })->exists();

        if ($isBooked) {
            return ['available' => false, 'message' => 'ช่วงเวลานี้มีลูกค้าท่านอื่นจองเครื่องจักรแล้ว'];
        }

        $startColumn = Schema::hasColumn('maintenances', 'start_date') ? 'start_date' : 'created_at';

        $isUnderMaintenance = Maintenance::where('equipment_id', $equipment->id)
            ->where(function ($query) use ($start, $end, $startColumn) {
                $query->whereNull('completion_date') 
                    ->orWhere(function ($q) use ($start, $end, $startColumn) {
                        $q->whereNotNull('completion_date') 
                            ->where($startColumn, '<', $end)
                            ->where('completion_date', '>', $start);
                    });
            })->exists();

        if ($isUnderMaintenance) {
            return ['available' => false, 'message' => 'เครื่องจักรติดภารกิจซ่อมบำรุงในช่วงเวลานี้'];
        }

        return ['available' => true];
    }

    // --- เช็คพนักงาน (โค้ดเดิมของคุณ ถูกต้องแล้ว) ---
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

        if (class_exists(Leave::class)) {
            $isOnLeave = Leave::where('user_id', $staffId)
                ->where('status', 'approved')
                ->where(function ($query) use ($start, $end) {
                    $query->where('start_date', '<', $end)
                        ->where('end_date', '>', $start);
                })->exists();

            if ($isOnLeave) {
                return ['available' => false, 'message' => 'พนักงานลางานในช่วงเวลานั้น'];
            }
        }

        return ['available' => true];
    }

    // --- สร้างเลข Job (โค้ดเดิมของคุณ ถูกต้องแล้ว) ---
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
}