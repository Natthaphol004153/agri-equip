<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\FuelTank;
use App\Models\FuelPurchase;

class FuelStockController extends Controller
{
    // แสดงรายการถังน้ำมันและยอดคงเหลือ
    public function index()
    {
        $tanks = FuelTank::all();
        return view('admin.fuel.index', compact('tanks'));
    }

    // หน้าฟอร์มซื้อน้ำมันเข้า
    public function createPurchase()
    {
        $tanks = FuelTank::all();
        return view('admin.fuel.purchase', compact('tanks'));
    }

    // บันทึกการซื้อน้ำมัน (Stock In) + คำนวณ Average Cost
    public function storePurchase(Request $request)
    {
        $request->validate([
            'fuel_tank_id' => 'required|exists:fuel_tanks,id',
            'liters' => 'required|numeric|min:1',
            'price_per_liter' => 'required|numeric|min:0',
            'purchase_date' => 'required|date',
        ]);

        $tank = FuelTank::findOrFail($request->fuel_tank_id);
        
        $newLiters = $request->liters;
        $newPrice = $request->price_per_liter;
        $totalCost = $newLiters * $newPrice;

        // --- สูตรคำนวณ Weighted Average Cost ---
        // (มูลค่าของเดิม + มูลค่าของใหม่) / (ปริมาณเดิม + ปริมาณใหม่)
        
        $oldValue = $tank->current_balance * $tank->average_price;
        $newValue = $totalCost;
        $totalLiters = $tank->current_balance + $newLiters;

        $newAveragePrice = ($oldValue + $newValue) / $totalLiters;

        // 1. บันทึกประวัติการซื้อ
        FuelPurchase::create([
            'fuel_tank_id' => $tank->id,
            'liters' => $newLiters,
            'price_per_liter' => $newPrice,
            'total_cost' => $totalCost,
            'purchase_date' => $request->purchase_date,
            'supplier' => $request->supplier,
            'note' => $request->note,
        ]);

        // 2. อัปเดตถังน้ำมัน (ราคาเฉลี่ยใหม่ + ยอดคงเหลือเพิ่มขึ้น)
        $tank->update([
            'current_balance' => $totalLiters,
            'average_price' => $newAveragePrice,
        ]);

        return redirect()->route('admin.fuel.index')->with('success', 'เพิ่มสต็อกน้ำมันเรียบร้อย! ราคาเฉลี่ยอัปเดตแล้ว');
    }
}