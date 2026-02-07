<?php

namespace App\Exports;

use App\Models\Job;
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
        // ใช้ Eager Loading (with) เพื่อให้โหลดข้อมูลที่เกี่ยวข้องมาด้วย (เช่น ลูกค้า, คนขับ)
        return Job::with(['customer', 'driver'])->get();
    }

    /**
    * 2. กำหนดหัวคอลัมน์ (Header)
    */
    public function headings(): array
    {
        return [
            'Job ID',
            'ชื่องาน / รายละเอียด',
            'ลูกค้า',
            'คนขับรถ',
            'สถานะงาน',
            'วันที่เริ่มงาน',
            'ค่าจ้าง (บาท)',
        ];
    }

    /**
    * 3. จัดรูปแบบข้อมูลในแต่ละแถว (Mapping)
    * ตรงนี้สำคัญ! ช่วยให้เราเลือกดึงเฉพาะข้อมูลที่ต้องการ หรือแปลงค่าได้
    */
    public function map($job): array
    {
        return [
            $job->id,
            $job->title ?? '-',             // ถ้าไม่มีให้ใส่ -
            $job->customer->name ?? 'ไม่ระบุ', // ดึงชื่อลูกค้าจาก Relation
            $job->driver->name ?? 'ยังไม่ระบุ', // ดึงชื่อคนขับ
            $this->getStatusLabel($job->status), // แปลง status เป็นภาษาไทย (ฟังก์ชันเขียนเองข้างล่าง)
            \Carbon\Carbon::parse($job->start_date)->format('d/m/Y'), // แปลงวันที่
            number_format($job->price, 2),  // ใส่ลูกน้ำและทศนิยม
        ];
    }

    // ฟังก์ชันช่วยแปลง Status (ตัวอย่าง)
    private function getStatusLabel($status)
    {
        return match ($status) {
            'pending' => 'รออนุมัติ',
            'approved' => 'อนุมัติแล้ว',
            'in_progress' => 'กำลังดำเนินงาน',
            'completed' => 'เสร็จสิ้น',
            'cancelled' => 'ยกเลิก',
            default => $status,
        };
    }
}