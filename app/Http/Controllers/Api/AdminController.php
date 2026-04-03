<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Booking;
use App\Models\Equipment;
use Barryvdh\DomPDF\Facade\Pdf;

class AdminController extends Controller
{
    // ==========================================
    // 📊 ส่วนที่ 1: Dashboard & Approval
    // ==========================================

    // 1. ดึงสถิติ Dashboard
    public function getDashboardStats()
    {
        return response()->json([
            'total_revenue' => Booking::where('status', 'completed')->sum('total_price'), 
            'completed_jobs' => Booking::where('status', 'completed')->count(), 
            'pending_jobs' => Booking::where('status', 'completed_pending_approval')->count(), 
            'maintenance_machines' => Equipment::where('current_status', 'maintenance')->count() 
        ]);
    }

    // 2. ดูงานที่รอตรวจสอบ (Pending Approval)
    public function getPendingJobs()
    {
        $jobs = Booking::with(['customer', 'equipment', 'staff', 'activities'])
            ->where('status', 'completed_pending_approval')
            ->orderBy('updated_at', 'desc')
            ->get();

        return response()->json($jobs);
    }

    // 3. อนุมัติงาน (Approve)
    public function approveJob($id)
    {
        $booking = Booking::findOrFail($id);
        
        if ($booking->status !== 'completed_pending_approval') {
            return response()->json(['error' => 'สถานะงานไม่ถูกต้อง'], 400);
        }

        $booking->update(['status' => 'completed']);

        return response()->json(['message' => 'อนุมัติเรียบร้อย!']);
    }

    // 4. ดูประวัติงานเก่า (Completed History)
    public function getCompletedJobs()
    {
        return response()->json(
            Booking::with(['customer', 'equipment'])
                ->where('status', 'completed')
                ->orderBy('updated_at', 'desc')
                ->get()
        );
    }

    // 5. พิมพ์ใบเสร็จ (Receipt PDF)
    public function printReceipt($id)
    {
        $booking = Booking::with(['customer', 'equipment'])->findOrFail($id);
        $pdf = Pdf::loadView('receipt', compact('booking'));
        return $pdf->download('receipt-job-'.$id.'.pdf');
    }

    // ==========================================
    // 🛠️ ส่วนที่ 2: CRUD JOBS (จัดการงาน)
    // ==========================================

    // 6. สร้างงานใหม่ (Create Job)
    public function storeJob(Request $request)
    {
        $booking = new Booking();
        $booking->customer_id = 1; // ⚠️ Demo: ลูกค้าขาประจำ (ID 1)
        $booking->equipment_id = $request->equipment_id;
        $booking->scheduled_start = $request->start_date;
        $booking->status = 'scheduled'; 
        $booking->total_price = 1500; // ⚠️ Demo: ราคาเริ่มต้น
        $booking->save();
        
        return response()->json(['success' => true]);
    }

    // 7. ลบงาน (Delete Job)
    public function deleteJob($id)
    {
        Booking::destroy($id);
        return response()->json(['success' => true]);
    }

    // ==========================================
    // 🚜 ส่วนที่ 3: CRUD EQUIPMENT (จัดการเครื่องจักร)
    // ==========================================

    // 8. เพิ่มเครื่องจักรใหม่ (Create Equipment)
    public function storeEquipment(Request $request)
    {
        $data = $request->validate([
            'name' => 'required',
            'type' => 'required|in:drone,tractor,harvester,sprayer,excavator,other',
            'tracking_type' => 'nullable|in:hours,kilometers',
            'maintenance_hour_threshold' => 'nullable|numeric|min:1',
            'maintenance_km_threshold' => 'nullable|numeric|min:1',
            'initial_meter' => 'nullable|numeric|min:0',
            'registration_number' => 'nullable|string',
            'image' => 'nullable|image'
        ]);

        $data['tracking_type'] = $data['tracking_type'] ?? 'hours';

        if ($data['tracking_type'] === 'hours' && empty($data['maintenance_hour_threshold'])) {
            return response()->json(['message' => 'maintenance_hour_threshold is required when tracking_type=hours'], 422);
        }

        if ($data['tracking_type'] === 'kilometers' && empty($data['maintenance_km_threshold'])) {
            return response()->json(['message' => 'maintenance_km_threshold is required when tracking_type=kilometers'], 422);
        }

        // handle image upload
        if ($request->hasFile('image')) {
            $path = $request->file('image')->store('equipment_images', 'public');
            $data['image_path'] = $path;
        }

        // generate equipment code like EQ-YYYYMMDD-XXX
        $prefix = 'EQ-'.now()->format('Ymd').'-';
        $latest = Equipment::where('equipment_code', 'like', $prefix.'%')->orderBy('id', 'desc')->first();
        if (!$latest) {
            $code = $prefix.'001';
        } else {
            $number = intval(substr($latest->equipment_code, -3)) + 1;
            $code = $prefix.str_pad($number, 3, '0', STR_PAD_LEFT);
        }

        $data['equipment_code'] = $code;
        $data['current_status'] = 'available';

        $initialMeter = (float) ($data['initial_meter'] ?? 0);
        if ($data['tracking_type'] === 'kilometers') {
            $data['current_kilometers'] = $initialMeter;
            $data['current_hours'] = 0;
            $data['maintenance_hour_threshold'] = null;
        } else {
            $data['current_hours'] = $initialMeter;
            $data['current_kilometers'] = 0;
            $data['maintenance_km_threshold'] = null;
        }

        unset($data['initial_meter']);

        $equipment = Equipment::create($data);

        return response()->json(['message' => 'สร้างเครื่องจักรเรียบร้อย', 'data' => $equipment], 201);
    }

    // 9. แก้ไขเครื่องจักร (Update Equipment)
    public function updateEquipment(Request $request, $id)
    {
        $equipment = Equipment::findOrFail($id);
        $equipment->update($request->only(['name', 'details'])); 
        
        return response()->json(['success' => true]);
    }

    // 10. ลบเครื่องจักร (Delete Equipment)
    public function deleteEquipment($id)
    {
        Equipment::destroy($id);
        return response()->json(['success' => true]);
    }
}