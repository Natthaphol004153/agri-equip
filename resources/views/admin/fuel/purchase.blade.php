@extends('layouts.admin')
@section('content')
<div class="max-w-2xl mx-auto p-6">
    <h2 class="text-2xl font-bold mb-6">บันทึกซื้อน้ำมันเข้า (Stock In)</h2>
    
    <form action="{{ route('admin.fuel.store_purchase') }}" method="POST" class="bg-white p-6 rounded-xl shadow">
        @csrf
        <div class="space-y-4">
            <div>
                <label class="block mb-1 font-bold">เติมเข้าถังไหน</label>
                <select name="fuel_tank_id" class="w-full border rounded p-2">
                    @foreach($tanks as $tank)
                        <option value="{{ $tank->id }}">{{ $tank->name }} (ปัจจุบันเหลือ {{ $tank->current_balance }} ลิตร)</option>
                    @endforeach
                </select>
            </div>

            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block mb-1 font-bold">จำนวน (ลิตร)</label>
                    <input type="number" step="0.01" name="liters" required class="w-full border rounded p-2">
                </div>
                <div>
                    <label class="block mb-1 font-bold">ราคาต่อลิตร (บาท)</label>
                    <input type="number" step="0.01" name="price_per_liter" required class="w-full border rounded p-2">
                </div>
            </div>

            <div>
                <label class="block mb-1 font-bold">วันที่ซื้อ</label>
                <input type="date" name="purchase_date" value="{{ date('Y-m-d') }}" required class="w-full border rounded p-2">
            </div>

            <div>
                <label class="block mb-1 font-bold">ผู้จำหน่าย / หมายเหตุ</label>
                <input type="text" name="supplier" class="w-full border rounded p-2">
            </div>

            <button type="submit" class="w-full bg-blue-600 text-white font-bold py-3 rounded mt-4 hover:bg-blue-700">
                ยืนยันการรับเข้า
            </button>
        </div>
    </form>
</div>
@endsection