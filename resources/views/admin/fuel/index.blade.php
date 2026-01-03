@extends('layouts.admin')

@section('content')
<div class="container mx-auto px-4 py-6">
    
    <div class="flex justify-between items-center mb-6">
        <h2 class="text-2xl font-bold text-gray-800">⛽ จัดการสต็อกน้ำมัน (Fuel Inventory)</h2>
        <div class="space-x-2">
            <button onclick="document.getElementById('addTankModal').classList.remove('hidden')" 
                    class="bg-indigo-600 hover:bg-indigo-700 text-white font-bold py-2 px-4 rounded shadow">
                + เพิ่มถังน้ำมัน
            </button>
            
            <a href="{{ route('admin.fuel.purchase') }}" 
               class="bg-green-600 hover:bg-green-700 text-white font-bold py-2 px-4 rounded shadow">
                + ซื้อน้ำมันเข้า (Stock In)
            </a>
        </div>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 mb-8">
        @foreach($tanks as $tank)
        @php
            $percent = ($tank->capacity > 0) ? ($tank->current_balance / $tank->capacity) * 100 : 0;
            $color = $percent < 20 ? 'red' : ($percent < 50 ? 'yellow' : 'green');
        @endphp
        <div class="bg-white rounded-lg shadow p-6 border-l-4 border-{{ $color }}-500 relative">
            <form action="{{ route('admin.fuel.tank.destroy', $tank->id) }}" method="POST" 
                  onsubmit="return confirm('ยืนยันที่จะลบถังนี้? ข้อมูลประวัติการเติมอาจหายไป')" 
                  class="absolute top-2 right-2">
                @csrf
                @method('DELETE')
                <button type="submit" class="text-gray-400 hover:text-red-500">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                </button>
            </form>

            <div class="flex justify-between items-start">
                <div>
                    <h3 class="text-lg font-bold text-gray-700">{{ $tank->name }}</h3>
                    <p class="text-sm text-gray-500">ชนิด: {{ $tank->fuel_type }}</p>
                </div>
                <span class="text-2xl font-bold text-{{ $color }}-600">
                    {{ number_format($tank->current_balance, 0) }} <span class="text-sm text-gray-500">/ {{ number_format($tank->capacity, 0) }} ลิตร</span>
                </span>
            </div>
            
            <div class="w-full bg-gray-200 rounded-full h-4 mt-4 dark:bg-gray-700">
                <div class="bg-{{ $color }}-500 h-4 rounded-full transition-all duration-500" style="width: {{ $percent }}%"></div>
            </div>
            <p class="text-xs text-right mt-1 text-gray-500">{{ number_format($percent, 1) }}%</p>
        </div>
        @endforeach
    </div>

    <div class="bg-white rounded-lg shadow overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-200 bg-gray-50">
            <h3 class="text-lg font-semibold text-gray-700">📜 ประวัติการซื้อน้ำมันล่าสุด</h3>
        </div>
        <table class="min-w-full leading-normal">
            <thead>
                <tr>
                    <th class="px-5 py-3 border-b-2 border-gray-200 bg-gray-100 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">วันที่</th>
                    <th class="px-5 py-3 border-b-2 border-gray-200 bg-gray-100 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">ถังน้ำมัน</th>
                    <th class="px-5 py-3 border-b-2 border-gray-200 bg-gray-100 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">ร้านค้า/ซัพพลายเออร์</th>
                    <th class="px-5 py-3 border-b-2 border-gray-200 bg-gray-100 text-right text-xs font-semibold text-gray-600 uppercase tracking-wider">จำนวน (ลิตร)</th>
                    <th class="px-5 py-3 border-b-2 border-gray-200 bg-gray-100 text-right text-xs font-semibold text-gray-600 uppercase tracking-wider">ราคา/ลิตร</th>
                    <th class="px-5 py-3 border-b-2 border-gray-200 bg-gray-100 text-right text-xs font-semibold text-gray-600 uppercase tracking-wider">รวมเงิน</th>
                </tr>
            </thead>
            <tbody>
                @forelse($purchases as $purchase)
                <tr>
                    <td class="px-5 py-4 border-b border-gray-200 bg-white text-sm">
                        {{ \Carbon\Carbon::parse($purchase->purchase_date)->format('d/m/Y') }}
                    </td>
                    <td class="px-5 py-4 border-b border-gray-200 bg-white text-sm font-medium">
                        {{ $purchase->tank->name ?? '-' }}
                    </td>
                    <td class="px-5 py-4 border-b border-gray-200 bg-white text-sm text-gray-500">
                        {{ $purchase->supplier_name ?? '-' }}
                    </td>
                    <td class="px-5 py-4 border-b border-gray-200 bg-white text-sm text-right text-blue-600 font-bold">
                        +{{ number_format($purchase->amount_liters, 0) }}
                    </td>
                    <td class="px-5 py-4 border-b border-gray-200 bg-white text-sm text-right">
                        {{ number_format($purchase->price_per_liter, 2) }}
                    </td>
                    <td class="px-5 py-4 border-b border-gray-200 bg-white text-sm text-right font-bold text-gray-800">
                        {{ number_format($purchase->total_cost, 2) }}
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="6" class="px-5 py-4 text-center text-gray-500">ไม่มีข้อมูลการซื้อเร็วๆ นี้</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

</div>

<div id="addTankModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full hidden" style="z-index: 50;">
    <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-md bg-white">
        <div class="mt-3 text-center">
            <h3 class="text-lg leading-6 font-medium text-gray-900">เพิ่มถังน้ำมันใหม่</h3>
            <form action="{{ route('admin.fuel.tank.store') }}" method="POST" class="mt-4 text-left">
                @csrf
                <div class="mb-4">
                    <label class="block text-gray-700 text-sm font-bold mb-2">ชื่อถัง (เช่น ถังใหญ่ 1)</label>
                    <input type="text" name="name" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline" required>
                </div>
                <div class="mb-4">
                    <label class="block text-gray-700 text-sm font-bold mb-2">ประเภทน้ำมัน</label>
                    <select name="fuel_type" class="shadow border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline">
                        <option value="Diesel">ดีเซล (Diesel)</option>
                        <option value="Gasohol 95">แก๊สโซฮอล์ 95</option>
                        <option value="Gasohol 91">แก๊สโซฮอล์ 91</option>
                        <option value="Benzene">เบนซิน</option>
                    </select>
                </div>
                <div class="mb-4">
                    <label class="block text-gray-700 text-sm font-bold mb-2">ความจุถัง (ลิตร)</label>
                    <input type="number" name="capacity" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline" required min="1">
                </div>
                <div class="flex justify-end space-x-2">
                    <button type="button" onclick="document.getElementById('addTankModal').classList.add('hidden')" class="bg-gray-500 text-white px-4 py-2 rounded hover:bg-gray-600">ยกเลิก</button>
                    <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700">บันทึก</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection