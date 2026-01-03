<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Equipment;
use App\Models\FuelLog;
use App\Models\FuelTank; // อย่าลืม import
use Illuminate\Support\Facades\Auth;

class FuelController extends Controller
{
    public function create()
    {
        $equipments = Equipment::whereIn('current_status', ['available', 'in_use'])->get();
        // ส่งข้อมูลถังน้ำมันไปด้วย เพื่อให้เลือกในหน้าเติม
        $tanks = FuelTank::where('current_balance', '>', 0)->get(); 
        
        return view('staff.fuel.create', compact('equipments', 'tanks'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'equipment_id' => 'required|exists:equipment,id',
            'fuel_source' => 'required|in:external,internal', // เช็คแหล่งที่มา
            'image' => 'nullable|image|max:10240', // internal อาจจะไม่ต้องใช้รูปก็ได้ แล้วแต่ Business
            'mileage' => 'nullable|numeric',
        ]);

        // Logic แยกตามแหล่งที่มา
        if ($request->fuel_source == 'internal') {
            // --- กรณีเติมจากถังบริษัท ---
            $request->validate([
                'fuel_tank_id' => 'required|exists:fuel_tanks,id',
                'liters' => 'required|numeric|min:0.1',
            ]);

            $tank = FuelTank::find($request->fuel_tank_id);

            // เช็คว่าน้ำมันพอจ่ายไหม
            if ($tank->current_balance < $request->liters) {
                return back()->with('error', 'น้ำมันในถังไม่พอจ่าย (เหลือ ' . $tank->current_balance . ' ลิตร)');
            }

            // คำนวณต้นทุนตัดจ่าย (Cost of Goods Sold)
            $cost = $request->liters * $tank->average_price;

            // ตัดสต็อก
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
            // --- กรณีเติมปั๊มข้างนอก (Logic เดิม) ---
            $request->validate([
                'amount' => 'required|numeric|min:1',
                'image' => 'required|image', // เติมปั๊มต้องมีรูป
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

        FuelLog::create($fuelData);

        return back()->with('success', 'บันทึกการเติมน้ำมันเรียบร้อย!');
    }
}