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
        $job = Booking::with('equipment')
            ->where('id', $id)
            ->where('assigned_staff_id', Auth::id())
            ->firstOrFail();

        $startTime = Carbon::now();

        $job->update([
            'status' => 'in_progress',
            'actual_start' => $startTime,
        ]);

        // 🔵 ส่งแจ้งเตือน Line: เริ่มงาน
        try {
            $msg = "🔵 [JOB STARTED]\n" .
                   "------------------------\n" .
                   "📋 Job No: {$job->job_number}\n" .
                   "👤 Staff: " . Auth::user()->name . "\n" .
                   "⏰ Time: " . $startTime->format('d/m/Y H:i') . "\n" .
                   "------------------------\n" .
                   "สถานะ: เริ่มปฏิบัติงาน";
            
            LineMessagingApi::send($msg);
        } catch (\Exception $e) {
            Log::error("Line Notification Error: " . $e->getMessage());
        }

        if ($request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => 'บันทึกเวลาเริ่มงานเรียบร้อย (Job Started)',
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

        $job = Booking::with('equipment')
            ->where('id', $id)
            ->where('assigned_staff_id', Auth::id())
            ->firstOrFail();

        $balance = $job->total_price - $job->deposit_amount;

        // 1. 🔍 เช็คสถานะก่อน: จ่ายหรือยัง?
        // (รวมถึงกรณีรอยืนยันสลิป หรือจ่ายมัดจำเต็มจำนวนแล้ว)
        $isAlreadyPaid = in_array($job->payment_status, ['paid', 'pending_approval']) || 
                         ($job->payment_status == 'deposit_paid' && $balance <= 0);

        // 2. 🛡️ กำหนด Validation Rules แบบ Dynamic
        $rules = [
            'job_image' => 'required|image|max:10240', // รูปผลงาน (บังคับเสมอ)
            'note' => 'nullable|string',
        ];

        // ถ้า "ยังไม่จ่าย" และ "มียอดค้าง" -> ต้องระบุวิธีจ่าย
        if (!$isAlreadyPaid && $balance > 0) {
            $rules['payment_method'] = 'required|in:transfer,cash';
            
            // ถ้าเลือก "โอนจ่าย" -> ต้องแนบสลิป
            if ($request->payment_method == 'transfer') {
                $rules['payment_proof'] = 'required|image|max:10240';
            }
        }

        $request->validate($rules);

        $transRef = null;
        $paymentStatus = $job->payment_status; // ค่าเริ่มต้น (ถ้าจ่ายแล้วก็ใช้ค่าเดิม)

        // 3. 💸 ตรวจสอบการชำระเงิน (เฉพาะกรณีที่ต้องจ่ายเพิ่ม)
        if (!$isAlreadyPaid && $balance > 0) {
            
            // กรณี A: จ่ายเงินสด (Cash) -> จบเลย รับเงินกับมือ
            if ($request->payment_method == 'cash') {
                $paymentStatus = 'paid';
            } 
            // กรณี B: โอนจ่าย (Transfer) -> ตรวจสอบสลิป
            elseif ($request->payment_method == 'transfer' && $request->hasFile('payment_proof')) {
                
                Log::info("Payment Verification: Verifying Slip with EasySlip...");
                
                // เรียกใช้ EasySlip SDK
                $sdk = new EasySlipSDK();
                $imageFile = $request->file('payment_proof');
                $result = $sdk->verify($imageFile);

                Log::info("Payment Verification Result", $result); 

                // ⚠️ Case 1: API ตรวจสอบไม่ได้
                if (!$result['success']) {
                    $errorMsg = '⚠️ ตรวจสอบสลิปไม่สำเร็จ: ' . ($result['message'] ?? 'Unknown Error');
                    if ($request->ajax()) return response()->json(['success' => false, 'message' => $errorMsg]);
                    return back()->with('error', $errorMsg);
                }

                $slipData = $result['data'];
                $slipAmount = $slipData['amount'];
                $transRef = $slipData['ref'] ?? null;

                // ⛔ Case 2: ตรวจสอบสลิปซ้ำ (ป้องกันโกง)
                if ($transRef) {
                    $isDuplicate = Booking::where('payment_trans_ref', $transRef)
                        ->where('id', '!=', $id)
                        ->exists();

                    if ($isDuplicate) {
                        $errorMsg = "⛔ สลิปนี้ถูกใช้ไปแล้ว (Ref: {$transRef})";
                        Log::warning("Fraud Alert: Duplicate Slip", ['user' => Auth::id(), 'ref' => $transRef]);
                        
                        if ($request->ajax()) return response()->json(['success' => false, 'message' => $errorMsg]);
                        return back()->with('error', $errorMsg);
                    }
                }

                // ⚠️ Case 3: ยอดเงินไม่ครบ
                if ($slipAmount < $balance) {
                    $errorMsg = "⚠️ ยอดโอนไม่ครบ (โอนมา: " . number_format($slipAmount, 2) . " / ต้องจ่าย: " . number_format($balance, 2) . ")";
                    
                    if ($request->ajax()) return response()->json(['success' => false, 'message' => $errorMsg]);
                    return back()->with('error', $errorMsg);
                }

                // ✅ ถ้าผ่านทุกด่าน -> ถือว่าจ่ายแล้ว (Verified)
                $paymentStatus = 'paid'; 
                Log::info("Payment Verified: Amount {$slipAmount}, Ref {$transRef}");
            }
        }

        // 4. 📂 อัปโหลดไฟล์
        $paymentProofPath = $job->payment_proof; // ใช้ของเดิมถ้ามี
        if ($request->hasFile('payment_proof')) {
            $paymentProofPath = $request->file('payment_proof')->store('payments', 'public');
        }

        $imagePath = null;
        if ($request->hasFile('job_image')) {
            $imagePath = $request->file('job_image')->store('job_evidence', 'public');
        }

        // 5. 💾 อัปเดตข้อมูลลงฐานข้อมูล
        $endTime = Carbon::now();
        
        $updateData = [
            'status' => 'completed_pending_approval', // สถานะงาน: เสร็จแล้ว (รอแอดมินปิดจ็อบ)
            'actual_end' => $endTime,
            'image_path' => $imagePath,
            'note' => $request->note,
        ];

        // อัปเดตข้อมูลการเงินเฉพาะเมื่อมีการจ่ายใหม่
        if (!$isAlreadyPaid && $balance > 0) {
            $updateData['payment_status'] = $paymentStatus; // paid หรือ pending_approval
            $updateData['payment_method'] = $request->payment_method;
            $updateData['payment_proof'] = $paymentProofPath;
            $updateData['payment_trans_ref'] = $transRef;
        }

        $job->update($updateData);

        // 6. 🔔 ส่งแจ้งเตือน Line
        try {
            $paymentText = $isAlreadyPaid ? "ชำระแล้ว (ก่อนหน้า)" : ($request->payment_method == 'cash' ? "เงินสด (รับหน้างาน)" : "โอนเงิน (ตรวจสอบแล้ว)");
            
            $lineMsg = "✅ [JOB COMPLETED]\n" .
                       "------------------------\n" .
                       "📋 Job No: {$job->job_number}\n" .
                       "👤 Staff: " . Auth::user()->name . "\n" .
                       "🏁 End Time: " . $endTime->format('H:i') . "\n" .
                       "💰 Payment: {$paymentText}\n" .
                       "------------------------\n" .
                       "สถานะ: ปฏิบัติงานเสร็จสิ้น รออนุมัติ";
            LineMessagingApi::send($lineMsg);
        } catch (\Exception $e) { 
            Log::error("Line Notification Error: " . $e->getMessage());
        }

        if ($request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => '✅ บันทึกข้อมูลการปฏิบัติงานเสร็จสิ้น',
                'job_id' => $job->id,
                'new_status' => 'completed'
            ]);
        }

        return back()->with('success', "บันทึกข้อมูลการปฏิบัติงานเรียบร้อยแล้ว");
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