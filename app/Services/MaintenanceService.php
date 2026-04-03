<?php
namespace App\Services;

use App\Models\MaintenanceLog;
use App\Models\Equipment;
use App\Enums\EquipmentStatus;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class MaintenanceService
{
    // 1. แจ้งซ่อม (รถเสีย/เข้าอู่) -> เปลี่ยนสถานะรถเป็น BREAKDOWN/MAINTENANCE
    public function reportIssue(array $data)
    {
        return DB::transaction(function () use ($data) {
            // สร้าง Log การซ่อม
            $log = MaintenanceLog::create($data);

            // อัปเดตสถานะเครื่องจักรทันที!
            $equipment = Equipment::findOrFail($data['equipment_id']);
            
            if ($data['maintenance_type'] === 'corrective') {
                $equipment->current_status = EquipmentStatus::BREAKDOWN; // เสียฉุกเฉิน
            } else {
                $equipment->current_status = EquipmentStatus::MAINTENANCE; // ซ่อมตามรอบ
            }
            
            $equipment->save();

            return $log;
        });
    }

    // 2. ปิดงานซ่อม (ซ่อมเสร็จ) -> เปลี่ยนสถานะรถกลับเป็น AVAILABLE
    public function completeMaintenance($logId, array $data)
    {
        return DB::transaction(function () use ($logId, $data) {
            $log = MaintenanceLog::findOrFail($logId);
            
            // อัปเดตข้อมูลการซ่อม (ราคา, วันที่เสร็จ)
            $log->update([
                'total_cost' => $data['total_cost'],
                'service_provider' => $data['service_provider'] ?? null,
                'completion_date' => Carbon::now(),
                'reset_counter' => $data['reset_counter'] ?? false,
                'service_meter_reading' => !empty($data['reset_counter']) ? $log->equipment->getCurrentMeterValue() : null,
            ]);

            // ดึงรถที่เกี่ยวข้อง
            $equipment = $log->equipment;

            // reset_counter เก็บไว้เป็นประวัติการซ่อมใหญ่เท่านั้น (ไม่ล้างเลขมิเตอร์จริง)

            // สำคัญที่สุด: ปลดล็อกรถกลับมาว่าง
            $equipment->current_status = EquipmentStatus::AVAILABLE;
            $equipment->save();

            return $log;
        });
    }
}