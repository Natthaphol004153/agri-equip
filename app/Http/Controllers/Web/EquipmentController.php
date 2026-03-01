<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Equipment;
use Illuminate\Support\Facades\Storage;
use App\Models\Booking;
use App\Models\MaintenanceLog;

class EquipmentController extends Controller
{
    // 1. หน้ารายการเครื่องจักร
    public function index()
    {
        $equipments = Equipment::latest()->paginate(10);
        return view('admin.equipments.index', compact('equipments'));
    }

    // 2. ฟอร์มเพิ่มข้อมูล
    public function create()
    {
        return view('admin.equipments.create');
    }

    // 3. บันทึกข้อมูลใหม่ (✅ แก้ไข: เพิ่มระบบสร้างรหัสอัตโนมัติ)
    public function store(Request $request)
    {
        // 1. ตรวจสอบข้อมูล (ตัด equipment_code ออก เพราะเราจะสร้างเอง)
        $request->validate([
            'name' => 'required',
            'type' => 'required',
            'custom_type_name' => 'nullable|string|max:100',
            'equipment_group' => 'required|in:machine,drone',
            'tracking_type' => 'required|in:hours,kilometers',
            'hourly_rate' => 'required|numeric|min:0',
            'maintenance_hour_threshold' => 'nullable|numeric|min:1',
            'maintenance_km_threshold' => 'nullable|numeric|min:1',
            'initial_meter' => 'nullable|numeric|min:0',
            'image' => 'nullable|image|max:5120', // รูปไม่เกิน 5MB
        ]);

        if ($request->tracking_type === 'hours' && !$request->filled('maintenance_hour_threshold')) {
            return back()->withErrors(['maintenance_hour_threshold' => 'กรุณากำหนดรอบซ่อมบำรุง (ชั่วโมง)'])->withInput();
        }

        if ($request->tracking_type === 'kilometers' && !$request->filled('maintenance_km_threshold')) {
            return back()->withErrors(['maintenance_km_threshold' => 'กรุณากำหนดรอบซ่อมบำรุง (กิโลเมตร)'])->withInput();
        }

        if ($request->equipment_group === 'drone' && $request->tracking_type !== 'hours') {
            return back()->withErrors(['tracking_type' => 'โดรน/อุปกรณ์ไฟฟ้า รองรับมิเตอร์แบบชั่วโมงในเวอร์ชันนี้'])->withInput();
        }

        if ($request->type === 'other' && !$request->filled('custom_type_name')) {
            return back()->withErrors(['custom_type_name' => 'กรุณาระบุประเภทเพิ่มเติมสำหรับ "อื่นๆ"'])->withInput();
        }

        // 2. 🟢 เริ่มระบบสร้างรหัสอัตโนมัติ (Auto-Generate Code)
        // กำหนดตัวย่อตามประเภท
        $prefix = match($request->type) {
            'drone' => 'DR',
            'tractor' => 'TR',
            'harvester' => 'HV',
            'sprayer' => 'SP',
            'other' => 'OT',
            default => 'EQ'
        };

        // หาเลขล่าสุดใน Database ของประเภทนี้
        // Logic: หาที่มีรหัสขึ้นต้นด้วย Prefix นี้ แล้วเอาตัวเลขมาบวก 1
        $lastEquipment = Equipment::where('equipment_code', 'LIKE', "$prefix-%")
            ->orderByRaw('LENGTH(equipment_code) DESC') // เรียงตามความยาวก่อน (เพื่อให้เลข 10 มาหลังเลข 9)
            ->orderBy('equipment_code', 'desc')
            ->first();

        $nextNum = 1; // เริ่มต้นที่ 001
        if ($lastEquipment) {
            // ตัดสตริงเอาเฉพาะตัวเลขหลังขีด (-)
            $parts = explode('-', $lastEquipment->equipment_code);
            if (isset($parts[1]) && is_numeric($parts[1])) {
                $nextNum = intval($parts[1]) + 1; // บวก 1
            }
        }

        // สร้างรหัสใหม่ (เช่น DR-006)
        $newCode = $prefix . '-' . sprintf('%03d', $nextNum);

        // 3. เตรียมข้อมูลบันทึก
        $data = $request->except(['equipment_code', 'initial_meter']); // ตัด input code ทิ้ง (เผื่อหลุดมา)
        $data['equipment_code'] = $newCode; // ✅ ยัดรหัสที่สร้างเองใส่เข้าไป
        if ($request->type !== 'other') {
            $data['custom_type_name'] = null;
        }

        $initialMeter = (float) ($request->input('initial_meter') ?? 0);
        if ($request->tracking_type === 'hours') {
            $data['current_hours'] = $initialMeter;
            $data['current_kilometers'] = 0;
            $data['maintenance_km_threshold'] = null;
        } else {
            $data['current_kilometers'] = $initialMeter;
            $data['current_hours'] = 0;
            $data['maintenance_hour_threshold'] = null;
        }

        // จัดการรูปภาพ
        if ($request->hasFile('image')) {
            $path = $request->file('image')->store('equipments', 'public');
            $data['image_path'] = 'storage/' . $path;
        }

        Equipment::create($data);

        return redirect()->route('admin.equipments.index')
            ->with('success', "เพิ่มเครื่องจักรสำเร็จ! รหัสที่ได้คือ: $newCode ✅");
    }

    // 4. ฟอร์มแก้ไข
    public function edit($id)
    {
        $equipment = Equipment::findOrFail($id);
        return view('admin.equipments.edit', compact('equipment'));
    }

    // 5. อัปเดตข้อมูล
    public function update(Request $request, $id)
    {
        $equipment = Equipment::findOrFail($id);

        $request->validate([
            'equipment_code' => 'required|unique:equipment,equipment_code,' . $id,
            'name' => 'required',
            'type' => 'required',
            'custom_type_name' => 'nullable|string|max:100',
            'equipment_group' => 'required|in:machine,drone',
            'tracking_type' => 'required|in:hours,kilometers',
            'hourly_rate' => 'required|numeric|min:0',
            'maintenance_hour_threshold' => 'nullable|numeric|min:1',
            'maintenance_km_threshold' => 'nullable|numeric|min:1',
            'image' => 'nullable|image|max:5120',
        ]);

        if ($request->tracking_type === 'hours' && !$request->filled('maintenance_hour_threshold')) {
            return back()->withErrors(['maintenance_hour_threshold' => 'กรุณากำหนดรอบซ่อมบำรุง (ชั่วโมง)'])->withInput();
        }

        if ($request->tracking_type === 'kilometers' && !$request->filled('maintenance_km_threshold')) {
            return back()->withErrors(['maintenance_km_threshold' => 'กรุณากำหนดรอบซ่อมบำรุง (กิโลเมตร)'])->withInput();
        }

        if ($request->equipment_group === 'drone' && $request->tracking_type !== 'hours') {
            return back()->withErrors(['tracking_type' => 'โดรน/อุปกรณ์ไฟฟ้า รองรับมิเตอร์แบบชั่วโมงในเวอร์ชันนี้'])->withInput();
        }

        if ($request->type === 'other' && !$request->filled('custom_type_name')) {
            return back()->withErrors(['custom_type_name' => 'กรุณาระบุประเภทเพิ่มเติมสำหรับ "อื่นๆ"'])->withInput();
        }

        $data = $request->except(['image']);
        if ($request->type !== 'other') {
            $data['custom_type_name'] = null;
        }

        if ($request->tracking_type === 'hours') {
            $data['maintenance_km_threshold'] = null;
        } else {
            $data['maintenance_hour_threshold'] = null;
        }

        // จัดการรูปภาพ (ลบรูปเก่า ลงรูปใหม่)
        if ($request->hasFile('image')) {
            if ($equipment->image_path) {
                // ลบคำว่า storage/ ออกเพื่อให้ได้ path จริงใน disk
                $oldPath = str_replace('storage/', '', $equipment->image_path);
                if(Storage::disk('public')->exists($oldPath)){
                    Storage::disk('public')->delete($oldPath);
                }
            }
            $path = $request->file('image')->store('equipments', 'public');
            $data['image_path'] = 'storage/' . $path;
        }

        $equipment->update($data);

        return redirect()->route('admin.equipments.index')->with('success', 'อัปเดตข้อมูลเรียบร้อยแล้ว 📝');
    }

    // 6. แสดงรายละเอียด (Show)
    public function show($id)
    {
        // ดึงข้อมูลรถ + ประวัติซ่อมที่ยังไม่เสร็จ (activeMaintenance)
        $equipment = Equipment::with(['activeMaintenance'])
            ->findOrFail($id);

        // ดึงงานที่รถคันนี้เคยทำ (เรียงจากล่าสุด)
        $jobHistory = Booking::where('equipment_id', $id)
            ->with(['customer', 'assignedStaff'])
            ->latest()
            ->take(20) // ดึงมาแค่ 20 รายการล่าสุด
            ->get();

        // ดึงประวัติการซ่อม
        $maintenanceHistory = MaintenanceLog::where('equipment_id', $id)
            ->latest()
            ->get();

        // คำนวณรายได้รวมทั้งหมดของรถคันนี้ 💰
        $totalEarnings = Booking::where('equipment_id', $id)
            ->where('status', 'completed')
            ->sum('total_price');

        // คำนวณค่าซ่อมรวมทั้งหมด 🛠️
        // หมายเหตุ: เช็คดีๆ ว่าในตาราง maintenance_logs ฟิลด์ชื่อ 'status' หรือไม่ (บางทีอาจใช้ completion_date เช็ค)
        // ถ้าใช้ status ตามที่ส่งมาล่าสุด:
        $totalMaintenanceCost = $maintenanceHistory->sum('total_cost'); 
        // หรือถ้าจะเอาเฉพาะที่ซ่อมเสร็จแล้ว: $maintenanceHistory->whereNotNull('completion_date')->sum('total_cost');

        return view('admin.equipments.show', compact('equipment', 'jobHistory', 'maintenanceHistory', 'totalEarnings', 'totalMaintenanceCost'));
    }

    // 7. ลบข้อมูล
    public function destroy($id)
    {
        $equipment = Equipment::findOrFail($id);

        // ห้ามลบถ้ารถกำลังทำงาน
        if ($equipment->current_status == 'in_use') {
            return back()->with('error', '❌ ไม่สามารถลบได้ เนื่องจากเครื่องจักรกำลังทำงานอยู่');
        }

        $equipment->delete();
        return back()->with('success', 'ลบเครื่องจักรเรียบร้อยแล้ว 🗑️');
    }
}