<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MaintenanceLog extends Model
{
    use HasFactory;

    // (Optional) กำหนดชื่อตารางให้ชัวร์
    protected $table = 'maintenance_logs';

    // ✅ เอา cost และ technician_name ออกไปแล้ว เพราะเราใช้ total_cost กับ service_provider แทน
    protected $fillable = [
        'equipment_id',
        'booking_id',
        'maintenance_type',
        'description',
        'status',
        'total_cost',
        'service_provider',
        'reset_counter',
        'image_url',
        'maintenance_date',
        'completion_date',
    ];

    // ✅ เพิ่มส่วนนี้: แปลงค่าให้เป็น Object โดยอัตโนมัติ (ทำให้เรียกใช้งานวันที่และ Checkbox ได้ง่ายขึ้น)
    protected $casts = [
        'maintenance_date' => 'datetime',
        'completion_date' => 'datetime',
        'reset_counter' => 'boolean',
    ];

    // ความสัมพันธ์: การซ่อมนี้เป็นของรถคันไหน
    public function equipment()
    {
        return $this->belongsTo(Equipment::class);
    }

    // ความสัมพันธ์: ถ้าการซ่อมนี้พังตอนกำลังทำงานจองคิวไหน
    public function booking()
    {
        return $this->belongsTo(Booking::class);
    }
}