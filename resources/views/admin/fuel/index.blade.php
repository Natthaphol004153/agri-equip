@extends('layouts.admin')
@section('content')
<div class="p-6">
    <div class="flex justify-between items-center mb-6">
        <h2 class="text-2xl font-bold text-gray-800">จัดการสต็อกน้ำมัน (Fuel Inventory)</h2>
        <a href="{{ route('admin.fuel.purchase') }}" class="bg-green-600 text-white px-4 py-2 rounded-lg hover:bg-green-700">
            + ซื้อน้ำมันเข้า (Stock In)
        </a>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
        @foreach($tanks as $tank)
        <div class="bg-white rounded-xl shadow p-6 border-l-4 border-blue-500">
            <h3 class="text-xl font-bold mb-2">{{ $tank->name }}</h3>
            <div class="text-gray-500 text-sm mb-4">ความจุ: {{ number_format($tank->capacity) }} ลิตร</div>
            
            <div class="flex justify-between items-end mb-2">
                <span class="text-3xl font-bold text-blue-600">{{ number_format($tank->current_balance, 2) }}</span>
                <span class="text-gray-600">ลิตร (คงเหลือ)</span>
            </div>
            
            <div class="w-full bg-gray-200 rounded-full h-2.5 mb-4">
                <div class="bg-blue-600 h-2.5 rounded-full" style="width: {{ ($tank->current_balance / $tank->capacity) * 100 }}%"></div>
            </div>

            <div class="pt-4 border-t text-sm text-gray-600">
                ต้นทุนเฉลี่ย: <strong>{{ number_format($tank->average_price, 2) }}</strong> บาท/ลิตร
            </div>
        </div>
        @endforeach
    </div>
</div>
@endsection