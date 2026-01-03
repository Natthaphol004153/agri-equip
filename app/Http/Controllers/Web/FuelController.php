<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Equipment;
use App\Models\FuelLog;
use App\Models\FuelTank;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB; // ✅ เพิ่ม DB Facade

class FuelController extends Controller
{
    public function create()
    {
        $equipments = Equipment::whereIn('current_status', ['available', 'in_use'])->get();
        // ส่งข้อมูลถังน้ำมันไปด้วย เพื่อให้เลือกในหน้าเติม (เฉพาะถังที่มีน้ำมัน)
        $tanks = FuelTank::where('current_balance', '>', 0)->get(); 
        
        return view('staff.fuel.create', compact('equipments', 'tanks'));
    }

    public function store(Request $request)
    {
        // Validation ส่วนกลาง
        $request->validate([
            'equipment_id' => 'required|exists:equipment,id',
            'fuel_source' => 'required|in:external,internal',
            'image' => 'nullable|image|max:10240',
            'mileage' => 'nullable|numeric',
        ]);

        try {
            // ✅ ใช้ Transaction คลุมการทำงานทั้งหมด เพื่อความปลอดภัยของข้อมูล
            DB::transaction(function () use ($request) {
                
                $fuelData = [];

                if ($request->fuel_source == 'internal') {
                    // ---------------------------------------------------------
                    // 🏢 กรณีเติมจากถังบริษัท (ตัดสต็อก)
                    // ---------------------------------------------------------
                    $request->validate([
                        'fuel_tank_id' => 'required|exists:fuel_tanks,id',
                        'liters' => 'required|numeric|min:0.1',
                    ]);

                    // 🔒 Lock แถวข้อมูลถังน้ำมันไว้ เพื่อป้องกันคนแย่งกันตัดยอดพร้อมกัน
                    $tank = FuelTank::lockForUpdate()->find($request->fuel_tank_id);

                    // เช็คว่าน้ำมันพอจ่ายไหม
                    if ($tank->current_balance < $request->liters) {
                        throw new \Exception('น้ำมันในถังไม่พอจ่าย (เหลือ ' . number_format($tank->current_balance, 2) . ' ลิตร)');
                    }

                    // คำนวณต้นทุนตัดจ่าย (Cost of Goods Sold) ตามราคาเฉลี่ย
                    $cost = $request->liters * $tank->average_price;

                    // 📉 ตัดยอดออกจากถังจริง
                    $tank->decrement('current_balance', $request->liters);

                    // เตรียมข้อมูลบันทึก
                    $fuelData = [
                        'equipment_id' => $request->equipment_id,
                        'user_id' => Auth::id(),
                        'fuel_source' => 'internal',
                        'fuel_tank_id' => $tank->id,
                        'amount' => $cost, // บันทึกเป็นต้นทุนภายใน
                        'liters' => $request->liters,
                        'mileage' => $request->mileage,
                        'note' => $request->note,
                        'refill_date' => now(),
                    ];

                } else {
                    // ---------------------------------------------------------
                    // ⛽ กรณีเติมปั๊มข้างนอก (ไม่ต้องตัดสต็อก)
                    // ---------------------------------------------------------
                    $request->validate([
                        'amount' => 'required|numeric|min:1',
                        'image' => 'required|image', // บังคับรูปสลิป
                    ]);

                    $imagePath = null;
                    if ($request->hasFile('image')) {
                        $imagePath = $request->file('image')->store('fuel_receipts', 'public');
                    }

                    $fuelData = [
                        'equipment_id' => $request->equipment_id,
                        'user_id' => Auth::id(),
                        'fuel_source' => 'external',
                        'amount' => $request->amount,
                        'liters' => $request->liters, // อาจจะ null ได้ถ้าไม่ได้จด
                        'mileage' => $request->mileage,
                        'image_path' => $imagePath,
                        'note' => $request->note,
                        'refill_date' => now(),
                    ];
                }

                // 💾 บันทึกลงตาราง Logs
                FuelLog::create($fuelData);
            });

            return back()->with('success', 'บันทึกการเติมน้ำมันเรียบร้อย!');

        } catch (\Exception $e) {
            // ถ้ามี Error (เช่น น้ำมันไม่พอ) จะเข้า block นี้และ Rollback อัตโนมัติ
            return back()->with('error', 'เกิดข้อผิดพลาด: ' . $e->getMessage())->withInput();
        }
    }
}