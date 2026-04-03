<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Equipment;
use App\Models\MaintenanceLog;

class MaintenanceController extends Controller
{
    // 1. แจ้งซ่อม (Report Issue)
    // เปลี่ยนสถานะรถเป็น 'maintenance' ทันที
    public function report(Request $request)
    {
        $request->validate([
            'equipment_id' => 'required|exists:equipment,id',
            'description' => 'required|string',
            'maintenance_type' => 'required|in:preventive,corrective' // preventive=เช็คระยะ, corrective=ซ่อมเมื่อเสีย
        ]);

        // 1. สร้างประวัติการซ่อม
        $log = MaintenanceLog::create([
            'equipment_id' => $request->equipment_id,
            'maintenance_type' => $request->maintenance_type,
            'description' => $request->description,
            'total_cost' => 0, // ยังไม่รู้ราคา เพราะเพิ่งแจ้ง
            'reset_counter' => 0
        ]);

        // 2. เปลี่ยนสถานะเครื่องจักรเป็น "กำลังซ่อม" (ทำให้จองไม่ได้)
        $equipment = Equipment::find($request->equipment_id);
        if ($request->maintenance_type === 'corrective') {
            $equipment->update(['current_status' => 'breakdown']);
        } else {
            $equipment->update(['current_status' => 'maintenance']);
        }

        return response()->json([
            'message' => 'แจ้งซ่อมเรียบร้อย เครื่องจักรถูกเปลี่ยนสถานะแล้ว',
            'data' => $log
        ], 201);
    }

    // 2. ปิดงานซ่อม (Complete Maintenance)
    // เปลี่ยนสถานะรถกลับเป็น 'available'
    public function complete(Request $request, $id)
    {
        // $id คือ ID ของใบแจ้งซ่อม (MaintenanceLog)
        $log = MaintenanceLog::findOrFail($id);
        
        $request->validate([
            'total_cost' => 'required|numeric',
            'service_provider' => 'nullable|string', // ใครเป็นคนซ่อม (อู่ไหน/ช่างคนไหน)
            'reset_counter' => 'boolean',
            'major_service' => 'boolean',
        ]);

        $isMajorService = $request->boolean('major_service') || $request->boolean('reset_counter');

        // 1. ดึงข้อมูลเครื่องจักร
        $equipment = Equipment::findOrFail($log->equipment_id);

        // 2. อัปเดตข้อมูลการซ่อม
        $log->update([
            'total_cost' => $request->total_cost,
            'service_provider' => $request->service_provider ?? null,
            'completion_date' => now(),
            'reset_counter' => $isMajorService ? 1 : 0,
            'service_meter_reading' => $isMajorService ? ($equipment->getCurrentMeterValue()) : null,
        ]);

        // 3. reset_counter ใช้บันทึกประวัติการซ่อมใหญ่เท่านั้น (ไม่ล้างเลขมิเตอร์จริง)

        // 4. เปลี่ยนสถานะกลับเป็น "ว่าง" (พร้อมรับงานใหม่)
        $equipment->current_status = 'available';
        $equipment->save();

        return response()->json(['message' => 'ปิดงานซ่อมสำเร็จ เครื่องจักรพร้อมใช้งาน']);

        
    }
}