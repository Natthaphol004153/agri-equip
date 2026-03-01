<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Booking extends Model
{
    use SoftDeletes, HasFactory;

    protected $fillable = [
        'job_number',
        'customer_id',
        'equipment_id',
        'assigned_staff_id',
        'scheduled_start',
        'scheduled_end',
        'actual_start',
        'meter_before_start',
        'actual_end',
        'status',
        
        // --- 🌾 ส่วนข้อมูลพื้นที่ (ไร่) ---
        'estimated_area',             // ✅ เพิ่ม: พื้นที่ประเมินเบื้องต้น
        'actual_area',                // ✅ เพิ่ม: พื้นที่ที่ทำได้จริง
        'price_per_rai_at_booking',    // ✅ บันทึกเรทราคา ณ วันที่จอง (Snapshot)

        // --- 💰 ส่วนการเงินและการชำระเงิน ---
        'total_price',
        'deposit_amount',
        'payment_status',
        'payment_method',
        'payment_proof',
        'payment_trans_ref',
        'meter_reading',
        'image_path',
        'note'
    ];

    protected $casts = [
        'scheduled_start' => 'datetime',
        'scheduled_end' => 'datetime',
        'actual_start' => 'datetime',
        'actual_end' => 'datetime',
        'meter_before_start' => 'decimal:2',
        'deposit_amount' => 'decimal:2',
        'total_price' => 'decimal:2',
        'estimated_area' => 'decimal:2', // ✅ Cast ให้เป็นตัวเลขทศนิยม
        'actual_area' => 'decimal:2',    // ✅ Cast ให้เป็นตัวเลขทศนิยม
        'meter_reading' => 'decimal:2',
        'price_per_rai_at_booking' => 'decimal:2',
    ];

    // --- Relationships ---

    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }

    public function equipment()
    {
        return $this->belongsTo(Equipment::class);
    }

    public function assignedStaff() 
    {
        return $this->belongsTo(User::class, 'assigned_staff_id');
    }

    /**
     * ✅ เพิ่ม: ความสัมพันธ์กับประวัติการซ่อมบำรุง
     * กรณีรถเสียระหว่างทำงานนี้ (MaintenanceLog)
     */
    public function maintenanceLogs()
    {
        return $this->hasMany(MaintenanceLog::class);
    }

    public function activities()
    {
        return $this->hasMany(TaskActivity::class);
    }
}