<?php
namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Carbon\Carbon;

class StoreBookingRequest extends FormRequest
{
    public function authorize()
    {
        return true; // หรือเช็ค Permission สิทธิ์ผู้ใช้งานตรงนี้
    }

    public function rules()
    {
        return [
            'customer_id' => 'required|exists:customers,id',
            'equipment_id' => 'required|exists:equipment,id',
            'assigned_staff_id' => 'nullable|exists:users,id',
            'scheduled_start' => 'required|date|after:now',
            'scheduled_end' => 'required|date|after:scheduled_start|before_or_equal:' . Carbon::parse($this->scheduled_start)->addDays(30)->format('Y-m-d H:i:s'),
            // 'total_price' => 'required|numeric' ❌ ลบออก เพราะ Backend คำนวณเองแล้ว
            'payment_method' => 'nullable|string', // ✅ อาจจะรับวิธีจ่ายมาด้วย
        ];
    }

    // กำหนดข้อความแจ้งเตือนภาษาไทย (ทางเลือก)
    public function messages()
    {
        return [
            'scheduled_end.before_or_equal' => 'ไม่สามารถจองล่วงหน้าเกิน 30 วันได้',
            'scheduled_start.after' => 'เวลาเริ่มจองต้องเป็นเวลาในอนาคต',
        ];
    }
}