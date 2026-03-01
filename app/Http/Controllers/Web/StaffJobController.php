<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Booking;
use App\Models\Equipment;
use App\Models\MaintenanceLog;
use App\Services\PromptPayService;
use App\Services\EasySlipSDK;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Carbon\Carbon;

class StaffJobController extends Controller
{
    public function index()
    {
        $myJobs = Booking::with(['customer', 'equipment'])
            ->where('assigned_staff_id', Auth::id())
            ->whereIn('status', ['scheduled', 'in_progress'])
            ->orderBy('scheduled_start', 'asc')
            ->get();

        $qrCodes = [];
        $promptPayNo = env('PROMPTPAY_NUMBER');

        foreach ($myJobs as $job) {
            if ($job->status == 'in_progress') {
                $balance = $job->total_price - $job->deposit_amount;
                if ($balance > 0 && $promptPayNo) {
                    try {
                        $qrCodes[$job->id] = PromptPayService::generatePayload($promptPayNo, $balance);
                    } catch (\Exception $e) {
                        Log::error("QR Generation Error: " . $e->getMessage());
                    }
                }
            }
        }

        $historyJobs = Booking::with(['customer'])
            ->where('assigned_staff_id', Auth::id())
            ->whereIn('status', ['completed', 'completed_pending_approval'])
            ->latest('actual_end')
            ->take(5)
            ->get();

        $equipments = Equipment::where('deleted_at', null)->get();

        return view('staff.jobs.index', compact('myJobs', 'historyJobs', 'equipments', 'qrCodes'));
    }

    public function show($id)
    {
        $job = Booking::with(['customer', 'equipment'])->findOrFail($id);

        if ($job->assigned_staff_id != Auth::id()) {
            abort(403, 'Access Denied: You are not authorized to view this job.');
        }

        $balance = $job->total_price - $job->deposit_amount;
        $qrData = null;
        $promptPayNo = env('PROMPTPAY_NUMBER');

        if ($balance > 0 && $promptPayNo) {
            try {
                $qrData = PromptPayService::generatePayload($promptPayNo, $balance);
            } catch (\Exception $e) {
            }
        }

        return view('staff.jobs.show', compact('job', 'qrData', 'balance'));
    }

    public function startWork(Request $request, $id)
    {
        $job = Booking::with(['equipment', 'customer'])
            ->where('id', $id)
            ->where('assigned_staff_id', Auth::id())
            ->firstOrFail();

        $startTime = Carbon::now();

        $job->update([
            'status' => 'in_progress',
            'actual_start' => $startTime,
        ]);

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

    public function finishWork(Request $request, $id)
    {
        Log::info("Job Finish Process Initiated: Job ID {$id}");

        $job = Booking::with(['equipment', 'customer'])
            ->where('id', $id)
            ->where('assigned_staff_id', Auth::id())
            ->firstOrFail();

        // 1. รับค่าพื้นที่ทำจริง (เก็บเป็นสถิติเฉยๆ ไม่เอาไปคูณเงิน)
        $actualArea = $request->input('actual_area') ?: ($job->estimated_area ?? 0);
        $request->merge(['actual_area' => $actualArea]);

        // 2. ใช้ยอดเงินเดิมที่แอดมินตั้งไว้เลย (ไม่มีการคำนวณใหม่)
        $balance = $job->total_price - $job->deposit_amount;

        $isAlreadyPaid = in_array($job->payment_status, ['paid', 'pending_approval']) ||
            ($job->payment_status == 'deposit_paid' && $balance <= 0);

        // 3. Validate ข้อมูล
        $rules = [
            'actual_area' => 'required|numeric|min:0.1',
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

        // 4. เตรียมอัปเดตข้อมูล (ไม่ต้องแก้ total_price แล้ว)
        $updateData = [
            'status' => 'completed_pending_approval',
            'actual_end' => Carbon::now(),
            'actual_area' => $actualArea, // บันทึกแค่ไร่ที่ทำจริง
            'note' => $request->note,
        ];

        if ($request->hasFile('job_image')) {
            $updateData['image_path'] = $request->file('job_image')->store('job_evidence', 'public');
        }

        if (!$isAlreadyPaid && $balance > 0) {
            $updateData['payment_status'] = 'paid';
            $updateData['payment_method'] = $request->payment_method;
            if ($request->hasFile('payment_proof')) {
                $updateData['payment_proof'] = $request->file('payment_proof')->store('payments', 'public');
            }
        }

        $job->update($updateData);

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

    public function history()
    {
        $historyJobs = Booking::with(['customer', 'equipment'])
            ->where('assigned_staff_id', Auth::id())
            ->whereIn('status', ['completed', 'completed_pending_approval'])
            ->latest('actual_end')
            ->paginate(15);

        return view('staff.jobs.history', compact('historyJobs'));
    }

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

        $urgentJobs = Booking::with(['customer', 'equipment'])
            ->where('assigned_staff_id', $userId)
            ->where(function ($q) {
                $q->where('status', 'in_progress')
                    ->orWhere(function ($sub) {
                        $sub->where('status', 'scheduled')
                            ->whereDate('scheduled_start', Carbon::today());
                    });
            })
            ->orderByRaw("FIELD(status, 'in_progress', 'scheduled')")
            ->orderBy('scheduled_start', 'asc')
            ->limit(10)
            ->get();

        return view('staff.dashboard', compact('counts', 'urgentJobs'));
    }

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

        MaintenanceLog::create([
            'equipment_id' => $request->equipment_id,
            'description' => $request->description,
            'image_url' => $imagePath,
            'maintenance_date' => now(),
            'status' => 'pending',
            'total_cost' => 0
        ]);

        Equipment::where('id', $request->equipment_id)->update(['current_status' => 'maintenance']);

        return back()->with('success', 'บันทึกข้อมูลการแจ้งซ่อมเรียบร้อยแล้ว สถานะอุปกรณ์ถูกเปลี่ยนเป็น Maintenance');
    }

    // --- ส่วนของระบบแจ้งซ่อม (Staff Maintenance) ---

    public function maintenanceIndex()
    {
        // ดึงประวัติที่พนักงานคนนี้เคยกดแจ้งไว้
        $myMaintenanceLogs = MaintenanceLog::with('equipment')
            ->orderBy('created_at', 'desc')
            ->get();

        return view('staff.maintenance.index', compact('myMaintenanceLogs'));
    }

    public function createReport()
    {
        // ดึงรถทั้งหมดมาให้พนักงานเลือก
        $equipments = Equipment::all();
        return view('staff.maintenance.create', compact('equipments'));
    }

    public function storeReport(Request $request)
    {
        // 1. ตรวจสอบข้อมูล
        $request->validate([
            'equipment_id' => 'required|exists:equipment,id',
            'description' => 'required|string',
            'image' => 'nullable|image|max:10240', // รูปถ่าย ไม่เกิน 10MB
        ]);

        // 2. จัดการอัปโหลดรูปภาพ (ถ้ามี)
        $imagePath = null;
        if ($request->hasFile('image')) {
            $imagePath = $request->file('image')->store('maintenance_issues', 'public');
        }

        // 3. บันทึกลงตาราง MaintenanceLog
        MaintenanceLog::create([
            'equipment_id' => $request->equipment_id,
            'description' => $request->description,
            'image_url' => $imagePath, // บันทึกรูปหน้างาน
            'status' => 'pending',     // สถานะ: รอแอดมินรับเรื่อง
            'total_cost' => 0,
        ]);

        // 4. เปลี่ยนสถานะรถคันนั้นเป็น "กำลังซ่อม" เพื่อไม่ให้แอดมินเผลอจัดคิวงานให้
        Equipment::where('id', $request->equipment_id)->update([
            'current_status' => 'maintenance'
        ]);

        // 5. เด้งกลับหน้าประวัติ พร้อมข้อความสำเร็จ
        return redirect()->route('staff.maintenance.index')
            ->with('success', 'ส่งเรื่องแจ้งซ่อมเรียบร้อยแล้ว กรุณารอแอดมินตรวจสอบครับ');
    }

    public function showReport($id)
    {
        // ดึงรายละเอียดเพื่อแสดงในหน้า Show
        $log = MaintenanceLog::with('equipment')->findOrFail($id);
        return view('staff.maintenance.show', compact('log'));
    }

    public function reportIssue(Request $request, $jobId)
    {
        // สร้าง Log แจ้งซ่อมและเปลี่ยนสถานะเครื่องจักร
        $this->reportGeneral($request);

        // โน้ตลงในตัวงานด้วยว่ามีการแจ้งขัดข้อง
        $job = Booking::find($jobId);
        if ($job) {
            $job->update([
                'note' => ltrim($job->note . ' | [แจ้งเหตุขัดข้อง]: ' . $request->description, ' | ')
            ]);
        }

        return back()->with('success', 'แจ้งซ่อมเรียบร้อย โปรดรอแอดมินติดต่อกลับ');
    }
}