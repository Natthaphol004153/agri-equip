<?php

namespace App\Http\Controllers\Api; // ต้องมี Api ต่อท้าย

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Booking;           // Import Model ใบงาน
use App\Models\TaskActivity;      // Import Model Log การทำงาน
use App\Enums\BookingStatus;      // Import Enum สถานะงาน
use App\Enums\EquipmentStatus;    // Import Enum สถานะรถ
use Carbon\Carbon;                // Import ตัวจัดการเวลา

class StaffController extends Controller
{
    // 1. ดูงานของฉัน (My Jobs)
    // URL: GET /api/staff/my-jobs?staff_id=2
    public function myJobs(Request $request)
    {
        // รับค่า staff_id มาจาก App (ในที่นี้คือ Postman Params)
        $staffId = $request->query('staff_id');

        if (!$staffId) {
            return response()->json(['error' => 'กรุณาระบุ staff_id'], 400);
        }

        // ดึงงานที่ได้รับมอบหมาย + สถานะเป็น "จอง" หรือ "กำลังทำ"
        $jobs = Booking::with(['customer', 'equipment'])
            ->where('assigned_staff_id', $staffId)
            ->whereIn('status', [BookingStatus::SCHEDULED, BookingStatus::IN_PROGRESS])
            ->orderBy('scheduled_start', 'asc')
            ->get();

        return response()->json($jobs);
    }

    // 2. กดเริ่มงาน (Check-in)
    // URL: POST /api/staff/jobs/{id}/start
    public function startJob(Request $request, $id)
    {
        // หาใบงานตาม ID
        $booking = Booking::findOrFail($id);
        
        // เช็คว่าเป็นงานของคนนี้จริงมั้ย (Optional Security)
        if ($request->user_id && $booking->assigned_staff_id != $request->user_id) {
            return response()->json(['error' => 'งานนี้ไม่ได้มอบหมายให้คุณ'], 403);
        }

        // 1. อัปเดตสถานะงาน -> IN_PROGRESS
        $booking->update([
            'status' => BookingStatus::IN_PROGRESS,
            'actual_start' => Carbon::now()
        ]);

        // 2. อัปเดตสถานะรถ -> IN_USE (กำลังใช้งาน)
        if ($booking->equipment) {
            $booking->equipment()->update(['current_status' => EquipmentStatus::IN_USE]);
        }

        // 3. บันทึก Log การกดปุ่ม
        TaskActivity::create([
            'booking_id' => $id,
            'user_id' => $request->user_id, // รับจาก Body
            'activity_type' => 'check_in',
            'location_lat' => $request->lat, // รับจาก Body
            'location_lng' => $request->lng, // รับจาก Body
            'description' => 'พนักงานเริ่มปฏิบัติงาน'
        ]);

        return response()->json(['message' => 'เริ่มงานแล้ว! สถานะเปลี่ยนเป็น IN_PROGRESS']);
    }

    // 3. จบงาน (Finish) + อัปรูป
    // URL: POST /api/staff/jobs/{id}/finish
    public function finishJob(Request $request, $id)
    {
        // Validate ว่าต้องมีรูปภาพแนบมาด้วย และต้องระบุเลขหน้าปัด
        $request->validate([
            'images' => 'required|array',     // ต้องเป็น Array (หลายรูป)
            'images.*' => 'image|max:10240',  // แต่ละรูปห้ามเกิน 10MB
            'meter_reading' => 'required|numeric|min:0' // ✅ บังคับส่งเลขหน้าปัด
        ]);

        $booking = Booking::findOrFail($id);
        
        // เช็คว่าเลขหน้าปัดใหม่ ต้องไม่น้อยกว่าเลขก่อนเริ่ม
        $meterBeforeStart = (float) ($booking->meter_before_start ?? 0);
        $meterReading = (float) $request->input('meter_reading');
        if ($meterReading < $meterBeforeStart) {
            return response()->json(['error' => 'เลขหน้าปัดต้องไม่น้อยกว่าค่าเริ่มต้นก่อนทำงาน'], 422);
        }

        // 1. Upload รูปภาพลง Server
        $imagePaths = [];
        if ($request->hasFile('images')) {
            foreach ($request->file('images') as $file) {
                // บันทึกไฟล์ลง storage/app/public/job_evidence
                $path = $file->store('job_evidence', 'public');
                $imagePaths[] = $path;
            }
        }

        // 2. เปลี่ยนสถานะงาน -> รอตรวจสอบ (COMPLETED_PENDING) พร้อมบันทึกเลขไมล์
        $booking->update([
            'status' => BookingStatus::COMPLETED_PENDING,
            'actual_end' => Carbon::now(),
            'meter_reading' => $meterReading
        ]);

        // 3. คืนรถ -> AVAILABLE (พร้อมรับงานต่อทันที)
        if ($booking->equipment) {
            $booking->equipment()->update(['current_status' => EquipmentStatus::AVAILABLE]);
        }

        // 4. บันทึก Log พร้อม Path รูปภาพ
        TaskActivity::create([
            'booking_id' => $id,
            'user_id' => $request->user_id,
            'activity_type' => 'finished',
            'image_paths' => $imagePaths, // Laravel จะแปลงเป็น JSON ให้อัตโนมัติ (เพราะ Cast ไว้ใน Model)
            'description' => $request->note ?? 'งานเสร็จสิ้น'
        ]);

        return response()->json(['message' => 'ส่งงานเรียบร้อย รอตรวจสอบความถูกต้อง']);
    }
    // ดึงงานของพนักงานคนนั้นๆ (My Jobs)
    public function getMyJobs($staffId)
    {
        $jobs = \App\Models\Booking::with(['customer', 'equipment'])
            ->where('assigned_staff_id', $staffId)
            ->whereIn('status', ['scheduled', 'in_progress']) // เอาเฉพาะงานที่ "รอทำ" กับ "กำลังทำ"
            ->orderBy('scheduled_start', 'asc')
            ->get();

        return response()->json($jobs);
    }
}