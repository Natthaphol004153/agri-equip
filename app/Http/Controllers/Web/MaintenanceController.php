<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Equipment;
use App\Models\MaintenanceLog;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use App\Services\LineBotService;
class MaintenanceController extends Controller
{
    // 1. หน้า Dashboard รวม
    public function index()
    {
        // รายการรอรับเรื่อง
        $reportedIssues = MaintenanceLog::where('status', 'pending')
            ->with(['equipment', 'reportedBy'])
            ->orderBy('created_at', 'asc')
            ->get();

        $availableForMaintenance = Equipment::whereDoesntHave('activeMaintenance')
            ->orderBy('name')
            ->get();

        $majorServiceMeters = $this->getLatestMajorServiceMeters();

        // รถถึงระยะซ่อม: คิดจากมิเตอร์ที่ใช้ไปหลัง "ซ่อมใหญ่ล่าสุด"
        $needMaintenance = $availableForMaintenance
            ->filter(function (Equipment $equipment) use ($majorServiceMeters) {
                $threshold = $equipment->getThresholdMeterValue();
                if ($threshold <= 0) {
                    return false;
                }

                $baseMeter = (float) ($majorServiceMeters[$equipment->id] ?? 0);
                $usedSinceMajorService = max(0, $equipment->getCurrentMeterValue() - $baseMeter);

                return $usedSinceMajorService >= $threshold;
            })
            ->values();

        // กำลังซ่อม
        $inMaintenance = MaintenanceLog::where('status', 'in_progress')
            ->with(['equipment', 'reportedBy'])
            ->get();

        // ประวัติย้อนหลัง (10 รายการ)
        $history = MaintenanceLog::where('status', 'completed')
            ->with(['equipment', 'reportedBy'])
            ->latest()
            ->take(10)
            ->get();

        // ส่งข้อมูลรถไปหน้า Index ด้วย (ตัดเฉพาะคันที่กำลังซ่อมค้างอยู่)
        $equipments = $availableForMaintenance;

        return view('admin.maintenance.index', compact('reportedIssues', 'needMaintenance', 'inMaintenance', 'history', 'equipments', 'majorServiceMeters'));
    }

    // 2. แสดงฟอร์มรับเรื่อง (GET)
    public function showAcceptForm($logId)
    {
        $log = MaintenanceLog::with('equipment')->findOrFail($logId);
        return view('admin.maintenance.accept', compact('log'));
    }

    // 3. บันทึกการรับเรื่อง (POST)
    public function accept(Request $request, $logId)
    {
        $request->validate([
            'admin_note' => 'required|string'
        ]);

        $log = MaintenanceLog::findOrFail($logId);

        $log->update([
            'status' => 'in_progress',
            'description' => $log->description . " | Admin Note: " . $request->admin_note,
            'maintenance_date' => now(),
        ]);

        // ล็อกรถ
        $log->equipment->update(['current_status' => 'maintenance']);

        return redirect()->route('admin.maintenance.index')->with('success', 'รับเรื่องเรียบร้อย! รถเข้าสู่สถานะกำลังซ่อม 🛠️');
    }

    // 4. หน้าฟอร์มเปิดใบงานเอง (Create)
    public function create()
    {
        // ดึงรถที่ยังไม่ติดใบซ่อมค้าง (เพื่อให้เห็นเครื่องจักรสถานะอื่นด้วย)
        $equipments = Equipment::whereDoesntHave('activeMaintenance')
            ->orderBy('name')
            ->get();

        // ถ้าไม่มีเครื่องที่พร้อมส่งซ่อม ให้ส่ง array ว่างไปป้องกัน error
        if ($equipments->isEmpty()) {
            // กรณีอยากดึงรถทั้งหมดมาแสดงแม้มีใบซ่อมค้าง (Optional)
            // $equipments = Equipment::all();
        }

        return view('admin.maintenance.create', compact('equipments'));
    }

    // 5. บันทึกเปิดใบงานเอง (Store)
    public function store(Request $request)
    {
        $request->validate([
            'equipment_id' => 'required|exists:equipment,id',
            'description' => 'required|string|max:500',
        ]);

        MaintenanceLog::create([
            'equipment_id' => $request->equipment_id,
            'reported_by_user_id' => Auth::id(),
            'maintenance_date' => now(),
            'description' => $request->description,
            'status' => 'in_progress',
            'total_cost' => 0, // ✅ แก้เป็น total_cost
        ]);

        // ล็อกรถ
        Equipment::where('id', $request->equipment_id)->update([
            'current_status' => 'maintenance'
        ]);

        return redirect()->route('admin.maintenance.index')->with('success', 'ส่งเครื่องจักรเข้าซ่อมเรียบร้อยแล้ว');
    }

    // 6. ส่งเช็คระยะด่วน
    public function start(Request $request, $id)
    {
        $equipment = Equipment::findOrFail($id);

        $hasActiveMaintenance = MaintenanceLog::where('equipment_id', $equipment->id)
            ->whereIn('status', ['pending', 'in_progress'])
            ->exists();

        if ($hasActiveMaintenance) {
            return back()->with('error', "{$equipment->name} มีใบซ่อมค้างอยู่แล้ว");
        }

        MaintenanceLog::create([
            'equipment_id' => $equipment->id,
            'reported_by_user_id' => Auth::id(),
            'maintenance_date' => now(),
            'description' => $request->description ?? 'ตรวจเช็คตามระยะ (Auto Start)',
            'total_cost' => 0, // ✅ แก้เป็น total_cost
            'status' => 'in_progress'
        ]);

        $equipment->update(['current_status' => 'maintenance']);

        return back()->with('success', "ส่ง {$equipment->name} เข้าซ่อมเรียบร้อย!");
    }

    // 7. จบงานซ่อม
    // 7. จบงานซ่อม
    public function finish(Request $request, $id)
    {
        $request->validate([
            'total_cost' => 'required|numeric',
            'service_provider' => 'nullable|string',
            'note' => 'nullable|string',
            'receipt_image' => 'nullable|image|max:10240', // 🟢 เพิ่มการตรวจสอบไฟล์รูป
        ]);

        $log = MaintenanceLog::findOrFail($id);
        $isReset = $request->boolean('major_service')
            || $request->boolean('reset_hours')
            || $request->boolean('reset_counter');

        $updateData = [
            'completion_date' => now(),
            'total_cost' => $request->total_cost,
            'service_provider' => $request->service_provider,
            'description' => $log->description . ($request->note ? ' | จบงาน: ' . $request->note : ''),
            'reset_counter' => $isReset,
            'service_meter_reading' => $isReset ? $log->equipment->getCurrentMeterValue() : null,
            'status' => 'completed'
        ];

        // 🟢 ถ้ามีการอัปโหลดรูปใบเสร็จ
        if ($request->hasFile('receipt_image')) {
            $updateData['receipt_image'] = $request->file('receipt_image')->store('maintenance_receipts', 'public');
        }

        $log->update($updateData);

        // ปลดล็อกรถ
        $updateEqData = ['current_status' => 'available'];
        $log->equipment->update($updateEqData);

        return redirect()->route('admin.maintenance.index')->with('success', 'ซ่อมเสร็จสิ้น! บันทึกข้อมูลและใบเสร็จเรียบร้อย 🚜💨');
    }

    private function getLatestMajorServiceMeters(): array
    {
        return MaintenanceLog::where('status', 'completed')
            ->where('reset_counter', true)
            ->whereNotNull('service_meter_reading')
            ->orderByDesc('completion_date')
            ->get()
            ->unique('equipment_id')
            ->mapWithKeys(function (MaintenanceLog $log) {
                return [$log->equipment_id => (float) $log->service_meter_reading];
            })
            ->toArray();
    }
}