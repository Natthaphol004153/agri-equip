<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\FuelTank;
use App\Models\FuelPurchase;

class FuelStockController extends Controller
{
    // ----------------------------------------------------------------
    // 📊 หน้าแสดงรายการถังน้ำมันและประวัติการซื้อ (Stock Overview)
    // ----------------------------------------------------------------
    public function index()
    {
        $tanks = FuelTank::all();

        // ❌ จุดที่แก้: ลบ 'supplier' ออกจาก with() เพราะเป็นแค่ field ธรรมดา
        // ✅ ดึงประวัติการซื้อล่าสุด 10 รายการ (โหลด tank มาแสดงชื่อถัง)
        $purchases = FuelPurchase::with('tank')
            ->latest()
            ->limit(10)
            ->get();

        return view('admin.fuel.index', compact('tanks', 'purchases'));
    }

    // ----------------------------------------------------------------
    // 🛒 หน้าฟอร์มซื้อน้ำมันเข้า (Stock In Form)
    // ----------------------------------------------------------------
    public function createPurchase()
    {
        $tanks = FuelTank::all();
        return view('admin.fuel.purchase', compact('tanks'));
    }

    // ----------------------------------------------------------------
    // 💾 บันทึกการซื้อน้ำมัน (Stock In Logic + Avg Cost Calculation)
    // ----------------------------------------------------------------
    public function storePurchase(Request $request)
    {
        $request->validate([
            'fuel_tank_id' => 'required|exists:fuel_tanks,id',
            'liters' => 'required|numeric|min:1',
            'price_per_liter' => 'required|numeric|min:0',
            'purchase_date' => 'required|date',
            'supplier' => 'nullable|string',
            'note' => 'nullable|string',
        ]);

        $tank = FuelTank::findOrFail($request->fuel_tank_id);
        
        $newLiters = $request->liters;
        $newPrice = $request->price_per_liter;
        $totalCost = $newLiters * $newPrice;

        // --- 🧮 สูตรคำนวณต้นทุนเฉลี่ย (Weighted Average Cost) ---
        // สูตร: (มูลค่าของเดิม + มูลค่าของใหม่) / (ปริมาณเดิม + ปริมาณใหม่)
        
        $oldValue = $tank->current_balance * $tank->average_price;
        $newValue = $totalCost;
        $totalLiters = $tank->current_balance + $newLiters;

        // ป้องกัน Error หารด้วยศูนย์
        $newAveragePrice = $totalLiters > 0 ? ($oldValue + $newValue) / $totalLiters : $newPrice;

        // 1. บันทึกประวัติการซื้อ
        FuelPurchase::create([
            'fuel_tank_id' => $tank->id,
            'liters' => $newLiters,
            'price_per_liter' => $newPrice,
            'total_cost' => $totalCost,
            'purchase_date' => $request->purchase_date,
            'supplier' => $request->supplier, // เก็บเป็น string ธรรมดา
            'note' => $request->note,
        ]);

        // 2. อัปเดตถังน้ำมัน (ยอดคงเหลือเพิ่ม + ราคาเฉลี่ยใหม่)
        $tank->update([
            'current_balance' => $totalLiters,
            'average_price' => $newAveragePrice,
        ]);

        return redirect()->route('admin.fuel.index')->with('success', 'เพิ่มสต็อกน้ำมันเรียบร้อย! อัปเดตราคาต้นทุนเฉลี่ยแล้ว');
    }

    // ----------------------------------------------------------------
    // ➕ ฟังก์ชันเพิ่มถังน้ำมันใหม่ (Create Tank)
    // ----------------------------------------------------------------
    public function storeTank(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'capacity' => 'required|numeric|min:1',
            'fuel_type' => 'required|string', // เช่น Diesel, Gasohol 95
        ]);

        FuelTank::create([
            'name' => $request->name,
            'capacity' => $request->capacity,
            'current_balance' => 0, // ถังใหม่เริ่มที่ 0
            'average_price' => 0,
            'fuel_type' => $request->fuel_type,
        ]);

        return back()->with('success', 'เพิ่มถังน้ำมันเรียบร้อยแล้ว!');
    }

    // ----------------------------------------------------------------
    // 🗑️ ฟังก์ชันลบถังน้ำมัน (Delete Tank)
    // ----------------------------------------------------------------
    public function destroyTank($id)
    {
        $tank = FuelTank::findOrFail($id);
        
        // Safety Check: ห้ามลบถ้ายังมีน้ำมันเหลือ
        if ($tank->current_balance > 0) {
             return back()->with('error', 'ไม่สามารถลบถังได้เนื่องจากยังมีน้ำมันคงเหลือ');
        }

        $tank->delete();

        return back()->with('success', 'ลบถังน้ำมันเรียบร้อยแล้ว!');
    }
}