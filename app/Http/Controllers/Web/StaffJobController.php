<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Booking;
use App\Models\Equipment;
use App\Models\MaintenanceLog;
use App\Services\LineMessagingApi;
use App\Services\PromptPayService;
use App\Services\EasySlipSDK; // ✅ เรียกใช้ EasySlip
use Illuminate\Support\Facades\Log; // ✅ เรียกใช้ Log
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class StaffJobController extends Controller
{
    // ... (ฟังก์ชัน index, show, startWork เหมือนเดิม) ...
    
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
                    } catch (\Exception $e) { }
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
            abort(403, 'Unauthorized action.');
        }

        $balance = $job->total_price - $job->deposit_amount;
        $qrData = null;
        $promptPayNo = env('PROMPTPAY_NUMBER');

        if ($balance > 0 && $promptPayNo) {
            $qrData = PromptPayService::generatePayload($promptPayNo, $balance);
        }

        return view('staff.jobs.show', compact('job', 'qrData', 'balance'));
    }

    public function startWork(Request $request, $id)
    {
        $job = Booking::with('equipment')
            ->where('id', $id)
            ->where('assigned_staff_id', Auth::id())
            ->firstOrFail();

        $job->update([
            'status' => 'in_progress',
            'actual_start' => Carbon::now(),
        ]);

        try {
            $msg = "▶️ เริ่มปฏิบัติงาน!\n📄 Job: {$job->job_number}\n👤 Staff: " . Auth::user()->name;
            LineMessagingApi::send($msg);
        } catch (\Exception $e) { }

        if ($request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => 'เริ่มงานแล้ว! ลุยเลย ✌️',
                'job_id' => $job->id,
                'new_status' => 'in_progress'
            ]);
        }

        return back()->with('success', 'เริ่มงานแล้ว! สู้ๆ ครับ ✌️');
    }

    // --------------------------------------------------------
    // 🔥 แก้ไขฟังก์ชัน finishWork ให้ Log ปลอดภัย
    // --------------------------------------------------------
    public function finishWork(Request $request, $id)
    {
        Log::info("Job Finish Started: Job ID {$id}");

        $job = Booking::with('equipment')
            ->where('id', $id)
            ->where('assigned_staff_id', Auth::id())
            ->firstOrFail();

        $balance = $job->total_price - $job->deposit_amount;

        $request->validate([
            'job_image' => 'required|image|max:10240',
            'payment_proof' => ($balance > 0) ? 'required|image|max:10240' : 'nullable|image|max:10240',
            'note' => 'nullable|string',
        ]);

        if ($balance > 0 && $request->hasFile('payment_proof')) {
            
            Log::info("Job Finish: Checking Slip with EasySlip...");

            $sdk = new EasySlipSDK();
            $imageFile = $request->file('payment_proof');
            $result = $sdk->verify($imageFile);

            // ✅ Log แบบนี้ปลอดภัย (ใส่ Array เป็น argument ที่ 2)
            Log::info("Job Finish: EasySlip Result", $result); 

            if (!$result['success']) {
                $msg = '❌ ' . ($result['message'] ?? 'Unknown Error');
                if ($request->ajax()) return response()->json(['success' => false, 'message' => $msg]);
                return back()->with('error', $msg);
            }

            $slipAmount = $result['data']['amount'];
            
            // เช็คยอดเงิน
            if ($slipAmount < $balance) {
                $msg = "❌ ยอดเงินไม่ครบ! (โอนมา {$slipAmount} บ. / ต้องจ่าย {$balance} บ.)";
                Log::warning("Job Finish Failed: Insufficient amount.", ['slip' => $slipAmount, 'required' => $balance]);
                
                if ($request->ajax()) return response()->json(['success' => false, 'message' => $msg]);
                return back()->with('error', $msg);
            }
            
            Log::info("Job Finish: Slip Passed. Amount: {$slipAmount}");
        }

        // บันทึกรูป
        $paymentProofPath = null;
        if ($request->hasFile('payment_proof')) {
            $paymentProofPath = $request->file('payment_proof')->store('payments', 'public');
        }

        $imagePath = null;
        if ($request->hasFile('job_image')) {
            $imagePath = $request->file('job_image')->store('job_evidence', 'public');
        }

        $job->update([
            'status' => 'completed_pending_approval',
            'actual_end' => Carbon::now(),
            'image_path' => $imagePath,
            'payment_proof' => $paymentProofPath,
            'payment_status' => $paymentProofPath ? 'paid' : $job->payment_status,
            'note' => $request->note,
        ]);

        if ($request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => '✅ ตรวจสอบสลิปผ่านแล้ว! บันทึกงานเรียบร้อย',
                'job_id' => $job->id,
                'new_status' => 'completed'
            ]);
        }

        return back()->with('success', "บันทึกงานเรียบร้อย!");
    }

    // ... (ส่วน reportGeneral, dashboard, อื่นๆ เหมือนเดิม) ...
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
            'reported_by' => Auth::id(),
            'description' => $request->description,
            'image_path' => $imagePath,
            'maintenance_date' => now(),
            'status' => 'pending',
            'cost' => 0
        ]);
        Equipment::where('id', $request->equipment_id)->update(['current_status' => 'maintenance']);
        return back()->with('success', 'แจ้งซ่อมเรียบร้อย! รถถูกเปลี่ยนสถานะเป็น "กำลังซ่อม"');
    }

    public function maintenanceIndex()
    {
        $myMaintenanceLogs = MaintenanceLog::with('equipment')
            ->where('reported_by', Auth::id())
            ->latest()
            ->limit(20)
            ->get();
        return view('staff.maintenance.index', compact('myMaintenanceLogs'));
    }

    public function createReport()
    {
        $equipments = Equipment::all();
        return view('staff.maintenance.create', compact('equipments'));
    }

    public function storeReport(Request $request)
    {
        return $this->reportGeneral($request);
    }

    public function reportIssue(Request $request, $jobId)
    {
        return $this->reportGeneral($request);
    }
}