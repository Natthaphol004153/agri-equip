<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use App\Models\Equipment;
use App\Models\FuelLog;
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
        $equipments = Equipment::whereIn('current_status', ['available', 'in_use'])
            ->orderBy('name')
            ->get();

        // ❌ จุดที่แก้: ลบ 'supplier' ออกจาก with() เพราะเป็นแค่ field ธรรมดา
        // ✅ ดึงประวัติการซื้อล่าสุด 10 รายการ (โหลด tank มาแสดงชื่อถัง)
        $purchases = FuelPurchase::with('tank')
            ->latest()
            ->limit(10)
            ->get();

        $withdrawals = FuelLog::with(['user', 'equipment', 'tank'])
            ->where('fuel_source', 'internal')
            ->orderByDesc('refill_date')
            ->limit(15)
            ->get();

        return view('admin.fuel.index', compact('tanks', 'purchases', 'equipments', 'withdrawals'));
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

    public function storeWithdraw(Request $request)
    {
        $request->validate([
            'equipment_id' => 'required|exists:equipment,id',
            'fuel_tank_id' => 'required|exists:fuel_tanks,id',
            'liters' => 'required|numeric|min:0.1',
            'mileage' => 'nullable|numeric|min:0',
            'note' => 'nullable|string|max:500',
        ]);

        try {
            DB::transaction(function () use ($request) {
                $tank = FuelTank::lockForUpdate()->findOrFail($request->fuel_tank_id);

                if ((float) $tank->current_balance < (float) $request->liters) {
                    throw new \Exception('น้ำมันในถังไม่พอจ่าย (เหลือ ' . number_format($tank->current_balance, 2) . ' ลิตร)');
                }

                $cost = (float) $request->liters * (float) $tank->average_price;

                $tank->decrement('current_balance', (float) $request->liters);

                FuelLog::create([
                    'equipment_id' => $request->equipment_id,
                    'user_id' => Auth::id(),
                    'fuel_source' => 'internal',
                    'fuel_tank_id' => $tank->id,
                    'amount' => $cost,
                    'liters' => $request->liters,
                    'mileage' => $request->mileage,
                    'note' => $request->note,
                    'refill_date' => now(),
                ]);
            });

            return redirect()->route('admin.fuel.index')->with('success', 'เบิกน้ำมันให้เครื่องจักรเรียบร้อยแล้ว');
        } catch (\Exception $e) {
            return back()->withInput()->with('error', 'ไม่สามารถเบิกน้ำมันได้: ' . $e->getMessage());
        }
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