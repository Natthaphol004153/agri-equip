<?php

namespace App\Exports;

// ✅ แก้ไข: เปลี่ยนจาก Job เป็น Booking
use App\Models\Booking; 

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;

class JobsExport implements FromCollection, WithHeadings, WithMapping, ShouldAutoSize
{
    /**
    * 1. ดึงข้อมูลจาก Database
    */
    public function collection()
    {
        // ✅ แก้ไข: ใช้ Booking:: แทน Job::
        // และ relation ชื่อ assignedStaff (ตามที่คุณใช้ใน Controller)
        return Booking::with(['customer', 'assignedStaff'])->get();
    }

    /**
    * 2. กำหนดหัวคอลัมน์ (Header)
    */
    public function headings(): array
    {
        return [
            'Job No.',
            'ลูกค้า',
            'เบอร์โทร',
            'คนขับรถ',
            'ประเภทรถ',
            'สถานะงาน',
            'วันที่เริ่มงาน',
            'ค่าจ้าง (บาท)',
        ];
    }

    /**
    * 3. จัดรูปแบบข้อมูล (Mapping)
    */
    public function map($booking): array // เปลี่ยนตัวแปรเป็น $booking ให้สื่อความหมาย
    {
        return [
            $booking->job_number ?? $booking->id, // ถ้ามี job_number ให้ใช้ ถ้าไม่มีใช้ id
            $booking->customer->name ?? 'ไม่ระบุ',
            $booking->customer->phone ?? '-',
            $booking->assignedStaff->name ?? 'ยังไม่ระบุ', // เช็คชื่อ relation ให้ตรงกับ Model (assignedStaff)
            $booking->equipment->name ?? '-',
            $this->getStatusLabel($booking->status),
            \Carbon\Carbon::parse($booking->scheduled_start)->format('d/m/Y H:i'), // ปรับให้แสดงเวลาด้วย
            number_format($booking->total_price, 2),
        ];
    }

    // แปลง Status เป็นภาษาไทย
    private function getStatusLabel($status)
    {
        return match ($status) {
            'pending' => 'รออนุมัติ',
            'approved' => 'อนุมัติแล้ว',
            'deposit_paid' => 'มัดจำแล้ว',
            'scheduled' => 'นัดหมายแล้ว',
            'in_progress' => 'กำลังดำเนินงาน',
            'completed' => 'เสร็จสิ้น',
            'cancelled' => 'ยกเลิก',
            default => $status,
        };
    }
}