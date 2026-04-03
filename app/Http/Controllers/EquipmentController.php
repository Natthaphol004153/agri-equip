<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Equipment;

class EquipmentController extends Controller
{
    // ดึงรายชื่อเครื่องจักรทั้งหมด + สถานะการซ่อมล่าสุด
    public function index()
    {
        // with('activeMaintenance') คือการดึงข้อมูลใบแจ้งซ่อมติดมาด้วย (ถ้ามี)
        $equipments = Equipment::with('activeMaintenance')->get();
        return response()->json($equipments);
    }
    
    // (เผื่อไว้) สร้างเครื่องจักรใหม่
    public function store(Request $request)
    {
        $data = $request->validate([
            'equipment_code' => 'required|unique:equipment',
            'name' => 'required',
            'type' => 'required',
            'tracking_type' => 'nullable|in:hours,kilometers',
            'maintenance_hour_threshold' => 'nullable|numeric|min:1',
            'maintenance_km_threshold' => 'nullable|numeric|min:1',
            'initial_meter' => 'nullable|numeric|min:0',
        ]);

        $data['tracking_type'] = $data['tracking_type'] ?? 'hours';

        if ($data['tracking_type'] === 'hours' && empty($data['maintenance_hour_threshold'])) {
            return response()->json(['message' => 'maintenance_hour_threshold is required when tracking_type=hours'], 422);
        }

        if ($data['tracking_type'] === 'kilometers' && empty($data['maintenance_km_threshold'])) {
            return response()->json(['message' => 'maintenance_km_threshold is required when tracking_type=kilometers'], 422);
        }

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
        
        return Equipment::create($data);
    }
}