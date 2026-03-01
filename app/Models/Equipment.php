<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Equipment extends Model
{
    use HasFactory, SoftDeletes;

    // ระบุชื่อตารางให้ชัดเจน (บางที Laravel อาจหาเป็น equipments เติม s)
    protected $table = 'equipment';

    protected $fillable = [
        'equipment_code',
        'name',
        'type',
        'custom_type_name',
        'equipment_group',
        'tracking_type', // <-- เพิ่ม tracking_type
        'registration_number',
        'current_hours',
        'current_kilometers', // <-- เพิ่ม current_kilometers
        'maintenance_hour_threshold',
        'maintenance_km_threshold', // <-- เพิ่ม maintenance_km_threshold
        'hourly_rate',
        'current_status',
        'price_per_rai',
        'image_path'
    ];

    // ความสัมพันธ์กับใบซ่อม (ใช้ในหน้า Maintenance)
    public function activeMaintenance()
    {
        return $this->hasOne(MaintenanceLog::class)->whereNull('completion_date')->latest();
    }
    public function getMaintenanceStatusAttribute()
    {
        // ถ้าไม่ได้ตั้งค่าซ่อมบำรุงไว้ ให้ถือว่าปกติ
        if (!$this->maintenance_hour_threshold) {
            return 'ok';
        }

        // คำนวณชั่วโมงที่เหลือ
        $remaining = $this->maintenance_hour_threshold - $this->current_hours;

        if ($remaining <= 0) {
            return 'overdue'; // 🔴 เลยกำหนดแล้ว (ใช้เกิน)
        }

        if ($remaining <= 10) {
            return 'soon'; // 🟡 เหลือวิ่งได้อีกไม่ถึง 10 ชม. (เตือนล่วงหน้า)
        }

        return 'ok'; // 🟢 ปกติ
    }
}