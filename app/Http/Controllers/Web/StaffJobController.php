<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Booking;
use App\Models\Equipment;
use App\Models\MaintenanceLog;
use App\Services\LineMessagingApi;
use App\Services\PromptPayService;
use App\Services\EasySlipSDK;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Carbon\Carbon;

class StaffJobController extends Controller
{
    /**
     * Display a listing of the assigned jobs.
     */
    public function index()
    {
        // ดึงงานที่ได้รับมอบหมาย (Scheduled & In Progress)
        $myJobs = Booking::with(['customer', 'equipment'])
            ->where('assigned_staff_id', Auth::id())
            ->whereIn('status', ['scheduled', 'in_progress'])
            ->orderBy('scheduled_start', 'asc')
            ->get();

        $qrCodes = [];
        $promptPayNo = env('PROMPTPAY_NUMBER');

        // สร้าง QR Code สำหรับงานที่กำลังดำเนินการ (In Progress) เพื่อเตรียมเก็บเงินหน้างาน
        foreach ($myJobs as $job) {
            if ($job->status == 'in_progress') {
                $balance = $job->total_price - $job->deposit_amount;
                // สร้าง QR เฉพาะกรณียอดคงเหลือ > 0
                if ($balance > 0 && $promptPayNo) {
                    try {
                        $qrCodes[$job->id] = PromptPayService::generatePayload($promptPayNo, $balance);
                    } catch (\Exception $e) {
                        Log::error("QR Generation Error: " . $e->getMessage());
                    }
                }
            }
        }

        // ประวัติงานล่าสุด 5 รายการ
        $historyJobs = Booking::with(['customer'])
            ->where('assigned_staff_id', Auth::id())
            ->whereIn('status', ['completed', 'completed_pending_approval'])
            ->latest('actual_end')
            ->take(5)
            ->get();

        $equipments = Equipment::where('deleted_at', null)->get();

        return view('staff.jobs.index', compact('myJobs', 'historyJobs', 'equipments', 'qrCodes'));
    }

    /**
     * Display the specified job details.
     */
    public function show($id)
    {
        $job = Booking::with(['customer', 'equipment'])->findOrFail($id);

        // Security Check: ห้ามดูงานคนอื่น
        if ($job->assigned_staff_id != Auth::id()) {
            abort(403, 'Access Denied: You are not authorized to view this job.');
        }

        $balance = $job->total_price - $job->deposit_amount;
        $qrData = null;
        $promptPayNo = env('PROMPTPAY_NUMBER');

        if ($balance > 0 && $promptPayNo) {
            try {
                $qrData = PromptPayService::generatePayload($promptPayNo, $balance);
            } catch (\Exception $e) { }
        }

        return view('staff.jobs.show', compact('job', 'qrData', 'balance'));
    }

    /**
     * Start the job logic (Time tracking & Notification).
     */
   public function startWork(Request $request, $id)
    {
        // ดึงข้อมูลงานพร้อมลูกค้าและเครื่องจักร
        $job = Booking::with(['equipment', 'customer'])
            ->where('id', $id)
            ->where('assigned_staff_id', Auth::id())
            ->firstOrFail();

        // บันทึกเวลาปัจจุบัน (ใน DB เก็บเป็น UTC หรือตาม Config App)
        $startTime = Carbon::now();

        $job->update([
            'status' => 'in_progress',
            'actual_start' => $startTime,
        ]);

        // 🔵 ส่งแจ้งเตือน Line (แต่งสวย + Fix Timezone)
        try {
            // แปลงเป็นเวลาไทยเพื่อการแสดงผลที่ถูกต้อง
            $thaiTime = $startTime->copy()->setTimezone('Asia/Bangkok');
            
            $customerName = $job->customer->name ?? 'ไม่ระบุ';
            $customerPhone = $job->customer->phone ?? '-';
            $equipmentName = $job->equipment->name ?? 'ไม่ระบุ';
            $staffName = Auth::user()->name;
            
            $msg = "🔔 แจ้งเริ่มปฏิบัติงาน (Start)\n" .
                   "➖➖➖➖➖➖➖➖➖➖\n" .
                   "🆔 Job No: {$job->job_number}\n" .
                   "🚜 เครื่องจักร: {$equipmentName}\n" .
                   "👤 ลูกค้า: {$customerName}\n" .
                   "📞 เบอร์โทร: {$customerPhone}\n" .
                   "👷 พนักงาน: {$staffName}\n" .
                   "📅 วันที่: " . $thaiTime->format('d/m/Y') . "\n" .
                   "⏰ เวลาเริ่ม: " . $thaiTime->format('H:i น.') . "\n" .
                   "➖➖➖➖➖➖➖➖➖➖\n" .
                   "🚀 สถานะ: กำลังปฏิบัติงาน";
            
            LineMessagingApi::send($msg);
        } catch (\Exception $e) {
            Log::error("Line Notification Error: " . $e->getMessage());
        }

        if ($request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => 'บันทึกเวลาเริ่มงานเรียบร้อย',
                'job_id' => $job->id,
                'new_status' => 'in_progress'
            ]);
        }

        return back()->with('success', 'บันทึกเวลาเริ่มงานเรียบร้อย');
    }

    /**
     * Finish the job logic (Payment verification, Image upload, Status update).
     */
   public function finishWork(Request $request, $id)
    {
        Log::info("Job Finish Process Initiated: Job ID {$id}");

        $job = Booking::with(['equipment', 'customer'])
            ->where('id', $id)
            ->where('assigned_staff_id', Auth::id())
            ->firstOrFail();

        $balance = $job->total_price - $job->deposit_amount;

        // --- (Logic Validation & Image Upload เดิมของคุณ) ---
        $isAlreadyPaid = in_array($job->payment_status, ['paid', 'pending_approval']) || 
                         ($job->payment_status == 'deposit_paid' && $balance <= 0);

        $rules = [
            'job_image' => 'required|image|max:10240',
            'note' => 'nullable|string',
        ];

        if (!$isAlreadyPaid && $balance > 0) {
            $rules['payment_method'] = 'required|in:transfer,cash';
            if ($request->payment_method == 'transfer') {
                $rules['payment_proof'] = 'required|image|max:10240';
            }
        }

        $request->validate($rules);

        // --- (Logic บันทึกข้อมูล) ---
        $endTime = Carbon::now();
        
        // เตรียมข้อมูลอัปเดต
        $updateData = [
            'status' => 'completed_pending_approval',
            'actual_end' => $endTime,
            'note' => $request->note,
        ];

        if ($request->hasFile('job_image')) {
            $updateData['image_path'] = $request->file('job_image')->store('job_evidence', 'public');
        }

        // จัดการสถานะการเงิน
        $paymentMethodText = "ไม่ระบุ"; // เอาไว้โชว์ในไลน์
        if ($isAlreadyPaid) {
            $paymentMethodText = "✅ ชำระครบแล้ว (Pre-paid)";
        } else {
            if ($request->payment_method == 'cash') {
                $updateData['payment_status'] = 'paid'; // รับเงินสด = จ่ายแล้ว
                $paymentMethodText = "💵 เงินสด (Cash)";
            } else {
                // โอนเงิน = รอตรวจสอบ (หรือ Paid ถ้ามีระบบเช็คสลิปอัตโนมัติ)
                // ในที่นี้สมมติว่าให้เป็น pending_approval หรือ paid ตาม Logic เดิม
                $updateData['payment_status'] = 'paid'; 
                $paymentMethodText = "📱 โอนเงิน (แนบสลิป)";
            }
            $updateData['payment_method'] = $request->payment_method;
            
            if ($request->hasFile('payment_proof')) {
                $updateData['payment_proof'] = $request->file('payment_proof')->store('payments', 'public');
            }
        }

        $job->update($updateData);

        // 🔔 ส่งแจ้งเตือน Line (แต่งสวย + Fix Timezone)
        try {
            // คำนวณเวลาไทย
            $thaiEndTime = $endTime->copy()->setTimezone('Asia/Bangkok');
            
            // คำนวณระยะเวลาที่ใช้ (ชั่วโมง นาที)
            $durationStr = "-";
            if ($job->actual_start) {
                $start = Carbon::parse($job->actual_start);
                $diff = $start->diff($endTime); // ใช้ $endTime ที่ยังไม่แปลง timezone ก็ได้เพราะค่าต่างเท่ากัน
                $durationStr = "{$diff->h} ชม. {$diff->i} นาที";
            }

            $customerName = $job->customer->name ?? 'ไม่ระบุ';
            $equipmentName = $job->equipment->name ?? 'ไม่ระบุ';
            $staffName = Auth::user()->name;
            $totalPrice = number_format($job->total_price, 2);

            $msg = "🏁 แจ้งปิดงาน (Job Completed)\n" .
                   "➖➖➖➖➖➖➖➖➖➖\n" .
                   "🆔 Job No: {$job->job_number}\n" .
                   "👤 ลูกค้า: {$customerName}\n" .
                   "🚜 เครื่องจักร: {$equipmentName}\n" .
                   "⏱ เวลาเสร็จ: " . $thaiEndTime->format('H:i น.') . " (รวม {$durationStr})\n" .
                   "💰 ยอดเงิน: {$totalPrice} บาท\n" .
                   "💸 การชำระ: {$paymentMethodText}\n" .
                   "👷 พนักงาน: {$staffName}\n" .
                   "➖➖➖➖➖➖➖➖➖➖\n" .
                   "🎉 สถานะ: ปฏิบัติงานเสร็จสิ้น";
            
            LineMessagingApi::send($msg);
        } catch (\Exception $e) { 
            Log::error("Line Notification Error: " . $e->getMessage());
        }

        if ($request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => 'บันทึกข้อมูลเสร็จสิ้น',
                'job_id' => $job->id,
                'new_status' => 'completed'
            ]);
        }

        return back()->with('success', "บันทึกข้อมูลเรียบร้อยแล้ว");
    }

    /**
     * Show history of completed jobs.
     */
    public function history()
    {
        $historyJobs = Booking::with(['customer', 'equipment'])
            ->where('assigned_staff_id', Auth::id())
            ->whereIn('status', ['completed', 'completed_pending_approval'])
            ->latest('actual_end')
            ->paginate(15); 

        return view('staff.jobs.history', compact('historyJobs'));
    }

    /**
     * Staff Dashboard logic.
     */
    public function dashboard()
    {
        $userId = Auth::id();
        
        $counts = [
            'in_progress' => Booking::where('assigned_staff_id', $userId)->where('status', 'in_progress')->count(),
            'scheduled' => Booking::where('assigned_staff_id', $userId)->where('status', 'scheduled')->count(),
            'completed' => Booking::where('assigned_staff_id', $userId)
                ->whereIn('status', ['completed', 'completed_pending_approval'])
                ->whereMonth('actual_end', Carbon::now()->month)
                ->whereYear('actual_end', Carbon::now()->year)
                ->count(),
        ];

        // งานด่วน หรือ งานที่กำลังทำอยู่
        $urgentJobs = Booking::with(['customer', 'equipment'])
            ->where('assigned_staff_id', $userId)
            ->where(function ($q) {
                $q->where('status', 'in_progress')
                    ->orWhere(function ($sub) {
                        $sub->where('status', 'scheduled')
                            ->whereDate('scheduled_start', Carbon::today());
                    });
            })
            ->orderByRaw("FIELD(status, 'in_progress', 'scheduled')") // ให้ in_progress ขึ้นก่อน
            ->orderBy('scheduled_start', 'asc')
            ->limit(10)
            ->get();

        return view('staff.dashboard', compact('counts', 'urgentJobs'));
    }

    /**
     * Submit a general maintenance report.
     */
    public function reportGeneral(Request $request)
    {
        $request->validate([
            'equipment_id' => 'required|exists:equipment,id',
            'description' => 'required|string',
            'image' => 'nullable|image|max:10240'
        ]);

        $imagePath = null;
        if ($request->hasFile('image')) {
            $imagePath = $request->file('image')->store('maintenance_reports', 'public');
        }

        // สร้าง Log การซ่อม
        MaintenanceLog::create([
            'equipment_id' => $request->equipment_id,
            // 'reported_by' => Auth::id(), // ⚠️ Commented out: Database ไม่มี field นี้ ถ้าต้องการใช้ต้องเพิ่ม Migration ก่อน
            'description' => $request->description,
            'image_url' => $imagePath, // ✅ แก้ไข: เปลี่ยนจาก image_path เป็น image_url ตาม DB
            'maintenance_date' => now(),
            'status' => 'pending',
            'total_cost' => 0 // ✅ แก้ไข: เปลี่ยนจาก cost เป็น total_cost ตาม DB
        ]);

        // อัปเดตสถานะรถเป็น Maintenance
        Equipment::where('id', $request->equipment_id)->update(['current_status' => 'maintenance']);

        return back()->with('success', 'บันทึกข้อมูลการแจ้งซ่อมเรียบร้อยแล้ว สถานะอุปกรณ์ถูกเปลี่ยนเป็น Maintenance');
    }

    /**
     * List maintenance logs reported by this staff.
     */
    public function maintenanceIndex()
    {
        $myMaintenanceLogs = MaintenanceLog::with('equipment')
            // ->where('reported_by', Auth::id()) // ⚠️ Commented out: Database ไม่มี field นี้
            ->latest()
            ->limit(20)
            ->get();

        return view('staff.maintenance.index', compact('myMaintenanceLogs'));
    }

    /**
     * Show form to create maintenance report.
     */
    public function createReport()
    {
        $equipments = Equipment::all();
        return view('staff.maintenance.create', compact('equipments'));
    }

    /**
     * Store maintenance report (Wrapper).
     */
    public function storeReport(Request $request)
    {
        return $this->reportGeneral($request);
    }

    /**
     * Report issue from specific job context (Wrapper).
     */
    public function reportIssue(Request $request, $jobId)
    {
        // อาจจะมีการเก็บ job_id ลง maintenance log ในอนาคตถ้าตารางรองรับ
        return $this->reportGeneral($request);
    }
}