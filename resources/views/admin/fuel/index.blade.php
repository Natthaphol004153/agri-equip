@extends('layouts.admin')

@section('content')
{{-- ปรับ padding: p-4 สำหรับมือถือ, md:p-6 สำหรับจอใหญ่ --}}
<div class="p-4 md:p-6">
    
    {{-- Header: ปรับเป็น flex-col บนมือถือ (เรียงลงมา) และ flex-row (เรียงนอน) บนจอ md ขึ้นไป --}}
    <div class="flex flex-col md:flex-row justify-between items-center mb-6 gap-4">
        <h2 class="text-xl md:text-2xl font-bold text-gray-800 text-center md:text-left">
            จัดการสต็อกน้ำมัน (Fuel Inventory)
        </h2>
        
        {{-- ปุ่มกด: ปรับให้เต็มความกว้างบนมือถือ หรือจัดกึ่งกลาง --}}
        <div class="flex flex-wrap justify-center gap-2 w-full md:w-auto">
            {{-- ปุ่มกดเปิด Modal เพิ่มถัง --}}
            <button onclick="document.getElementById('addTankModal').classList.remove('hidden')" 
                    class="bg-indigo-600 text-white px-4 py-2 rounded-lg hover:bg-indigo-700 shadow transition text-sm md:text-base flex-1 md:flex-none text-center whitespace-nowrap">
                + เพิ่มถังน้ำมัน
            </button>
            
            {{-- ปุ่มไปหน้าซื้อน้ำมัน --}}
            <a href="{{ route('admin.fuel.purchase') }}" 
               class="bg-green-600 text-white px-4 py-2 rounded-lg hover:bg-green-700 shadow transition text-sm md:text-base flex-1 md:flex-none text-center whitespace-nowrap">
                + ซื้อน้ำมันเข้า
            </a>
        </div>
    </div>

    {{-- Flash Messages --}}
    @if(session('success'))
        <div class="bg-green-100 border-l-4 border-green-500 text-green-700 p-4 mb-6 rounded shadow-sm flex items-center gap-3">
            <i class="fa-solid fa-circle-check text-xl"></i>
            <p class="font-bold text-sm md:text-base">{{ session('success') }}</p>
        </div>
    @endif

    @if(session('error'))
        <div class="bg-red-100 border-l-4 border-red-500 text-red-700 p-4 mb-6 rounded shadow-sm flex items-center gap-3">
            <i class="fa-solid fa-circle-exclamation text-xl"></i>
            <p class="font-bold text-sm md:text-base">{{ session('error') }}</p>
        </div>
    @endif

    {{-- Grid Cards: grid-cols-1 (มือถือ) -> md:grid-cols-3 (ไอแพด/คอม) --}}
    <div class="grid grid-cols-1 md:grid-cols-3 gap-4 md:gap-6">
        @foreach($tanks as $tank)
        <div class="bg-white rounded-xl shadow p-5 md:p-6 border-l-4 border-blue-500 relative group hover:shadow-lg transition">
            
            {{-- ปุ่มลบถัง --}}
            <form action="{{ route('admin.fuel.tank.destroy', $tank->id) }}" method="POST" 
                  onsubmit="return confirm('ยืนยันที่จะลบถังนี้? ข้อมูลประวัติการเติมอาจหายไป')" 
                  class="absolute top-2 right-2 opacity-100 md:opacity-0 group-hover:opacity-100 transition-opacity duration-200">
                @csrf
                @method('DELETE')
                <button type="submit" class="text-gray-400 hover:text-red-500 p-1" title="ลบถังน้ำมัน">
                    <i class="fa-solid fa-trash"></i>
                </button>
            </form>

            <div class="flex justify-between items-start mb-2">
                <h3 class="text-lg md:text-xl font-bold text-gray-800 break-words max-w-[70%]">{{ $tank->name }}</h3>
                <span class="text-xs bg-blue-100 text-blue-800 px-2 py-1 rounded-full font-bold whitespace-nowrap">
                    {{ $tank->fuel_type ?? 'N/A' }}
                </span>
            </div>
            
            <div class="text-gray-500 text-xs md:text-sm mb-4 flex items-center gap-1">
                <i class="fa-solid fa-database"></i> ความจุ: {{ number_format($tank->capacity) }} ลิตร
            </div>
            
            <div class="flex justify-between items-end mb-2">
                <span class="text-2xl md:text-3xl font-bold text-blue-600">{{ number_format($tank->current_balance, 0) }}</span>
                <span class="text-sm md:text-base text-gray-600">ลิตร (คงเหลือ)</span>
            </div>
            
            {{-- Progress Bar --}}
            @php 
                $percent = ($tank->capacity > 0) ? ($tank->current_balance / $tank->capacity) * 100 : 0;
                $barColor = $percent < 20 ? 'bg-red-500' : 'bg-blue-600';
            @endphp
            <div class="w-full bg-gray-200 rounded-full h-3 mb-1 overflow-hidden">
                <div class="{{ $barColor }} h-3 rounded-full transition-all duration-1000" style="width: {{ $percent }}%"></div>
            </div>
            <div class="text-right text-xs text-gray-400 font-bold">{{ number_format($percent, 1) }}%</div>

            <div class="pt-4 mt-4 border-t border-gray-100 text-sm text-gray-600 flex justify-between items-center bg-gray-50 -mx-5 -mb-5 px-5 py-3 md:-mx-6 md:-mb-6 md:px-6 rounded-b-xl">
                <span>ต้นทุนเฉลี่ย:</span>
                <span class="font-bold text-gray-800 bg-white px-2 py-1 rounded border border-gray-200 shadow-sm text-xs md:text-sm">
                    {{ number_format($tank->average_price, 2) }} ฿/ลิตร
                </span>
            </div>
        </div>
        @endforeach
    </div>

    {{-- ตารางประวัติการซื้อ --}}
    @if(isset($purchases) && $purchases->count() > 0)
    <div class="mt-8 md:mt-10 bg-white rounded-xl shadow overflow-hidden">
        <div class="px-4 py-4 md:px-6 border-b border-gray-200 bg-gray-50">
            <h3 class="font-bold text-gray-700">📜 ประวัติการซื้อน้ำมันล่าสุด</h3>
        </div>
        
        {{-- ✅ เพิ่ม overflow-x-auto เพื่อให้ตารางเลื่อนซ้ายขวาได้ในมือถือ --}}
        <div class="overflow-x-auto">
            <table class="min-w-full leading-normal text-sm text-left whitespace-nowrap">
                <thead class="bg-gray-100 text-gray-600 uppercase font-bold text-xs">
                    <tr>
                        <th class="px-4 py-3 md:px-6">วันที่</th>
                        <th class="px-4 py-3 md:px-6">ถังน้ำมัน</th>
                        <th class="px-4 py-3 md:px-6">ร้านค้า</th>
                        <th class="px-4 py-3 md:px-6 text-right">จำนวน (ลิตร)</th>
                        <th class="px-4 py-3 md:px-6 text-right">ราคา/ลิตร</th>
                        <th class="px-4 py-3 md:px-6 text-right">รวมเงิน</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200">
                    @foreach($purchases as $purchase)
                    <tr class="hover:bg-gray-50">
                        <td class="px-4 py-3 md:px-6 border-b border-gray-200 bg-white">
                            {{ \Carbon\Carbon::parse($purchase->purchase_date)->format('d/m/Y') }}
                        </td>
                        <td class="px-4 py-3 md:px-6 border-b border-gray-200 bg-white font-medium">
                            {{ $purchase->tank->name ?? '-' }}
                        </td>
                        <td class="px-4 py-3 md:px-6 border-b border-gray-200 bg-white text-gray-500">
                            {{ $purchase->supplier ?? '-' }}
                        </td>
                        <td class="px-4 py-3 md:px-6 border-b border-gray-200 bg-white text-right text-blue-600 font-bold">
                            +{{ number_format($purchase->liters, 0) }}
                        </td>
                        <td class="px-4 py-3 md:px-6 border-b border-gray-200 bg-white text-right">
                            {{ number_format($purchase->price_per_liter, 2) }}
                        </td>
                        <td class="px-4 py-3 md:px-6 border-b border-gray-200 bg-white text-right font-bold text-gray-800">
                            {{ number_format($purchase->total_cost, 2) }}
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
    @endif

</div>

{{-- Modal เพิ่มถังน้ำมัน --}}
<div id="addTankModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full hidden z-50 px-4">
    {{-- ปรับ w-96 เป็น w-full max-w-md เพื่อให้ยืดหยุ่นในมือถือ --}}
    <div class="relative top-20 mx-auto p-5 border w-full max-w-md shadow-lg rounded-md bg-white">
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