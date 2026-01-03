@extends('layouts.staff')

@section('title', 'เติมน้ำมัน')
@section('header', 'บันทึกเติมน้ำมัน')

@section('content')
<div class="max-w-lg mx-auto pb-20" x-data="{ source: 'external' }"> 
    
    <div class="mb-4">
        <a href="{{ route('staff.jobs.index') }}" class="inline-flex items-center gap-2 text-gray-500 hover:text-gray-700 text-sm font-bold">
            <i class="fa-solid fa-arrow-left"></i> กลับหน้างาน
        </a>
    </div>

    {{-- ✅ เพิ่มส่วนแจ้งเตือนความสำเร็จ (Success Alert) --}}
    @if(session('success'))
        <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative mb-4 flex items-center gap-2" role="alert">
            <i class="fa-solid fa-circle-check"></i>
            <span class="block sm:inline font-bold">{{ session('success') }}</span>
        </div>
    @endif

    {{-- ส่วนแจ้งเตือนข้อผิดพลาด (Error Alert) --}}
    @if(session('error'))
        <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative mb-4 flex items-center gap-2" role="alert">
            <i class="fa-solid fa-circle-exclamation"></i>
            <span class="block sm:inline font-bold">{{ session('error') }}</span>
        </div>
    @endif

    {{-- แสดง Error จาก Validation (เช่น ลืมกรอกข้อมูล) --}}
    @if ($errors->any())
        <div class="bg-red-50 border-l-4 border-red-500 p-4 mb-4">
            <div class="flex">
                <div class="flex-shrink-0">
                    <i class="fa-solid fa-circle-xmark text-red-500"></i>
                </div>
                <div class="ml-3">
                    <p class="text-sm text-red-700 font-bold">เกิดข้อผิดพลาดในการกรอกข้อมูล:</p>
                    <ul class="mt-1 list-disc list-inside text-sm text-red-600">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            </div>
        </div>
    @endif

    <form action="{{ route('staff.fuel.store') }}" method="POST" enctype="multipart/form-data">
        @csrf
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
            
            <div class="bg-orange-50 px-6 py-4 border-b border-orange-100 flex items-center gap-3">
                <div class="w-10 h-10 rounded-full bg-orange-100 text-orange-600 flex items-center justify-center text-lg">
                    <i class="fa-solid fa-gas-pump"></i>
                </div>
                <div>
                    <h3 class="font-bold text-gray-800">ข้อมูลการเติมน้ำมัน</h3>
                </div>
            </div>

            <div class="p-6 space-y-5">
                
                {{-- 1. เลือกแหล่งที่มา --}}
                <div>
                    <label class="block text-sm font-bold text-gray-700 mb-2">เติมจากที่ไหน? *</label>
                    <div class="grid grid-cols-2 gap-3">
                        <label class="cursor-pointer">
                            <input type="radio" name="fuel_source" value="external" x-model="source" class="peer sr-only">
                            <div class="rounded-xl border-2 border-gray-200 p-3 text-center peer-checked:border-orange-500 peer-checked:bg-orange-50 transition">
                                <i class="fa-solid fa-store text-xl mb-1 text-gray-500 peer-checked:text-orange-600"></i>
                                <div class="text-sm font-bold text-gray-600 peer-checked:text-orange-800">ปั๊มน้ำมันทั่วไป</div>
                            </div>
                        </label>
                        <label class="cursor-pointer">
                            <input type="radio" name="fuel_source" value="internal" x-model="source" class="peer sr-only">
                            <div class="rounded-xl border-2 border-gray-200 p-3 text-center peer-checked:border-blue-500 peer-checked:bg-blue-50 transition">
                                <i class="fa-solid fa-industry text-xl mb-1 text-gray-500 peer-checked:text-blue-600"></i>
                                <div class="text-sm font-bold text-gray-600 peer-checked:text-blue-800">ถังบริษัท</div>
                            </div>
                        </label>
                    </div>
                </div>

                {{-- 2. เลือกรถ --}}
                <div>
                    <label class="block text-sm font-bold text-gray-700 mb-1">เครื่องจักร *</label>
                    <div class="relative">
                        <i class="fa-solid fa-tractor absolute left-4 top-1/2 -translate-y-1/2 text-gray-400"></i>
                        <select name="equipment_id" required class="w-full pl-10 pr-4 py-3 rounded-xl border border-gray-200 bg-white focus:ring-2 focus:ring-orange-500/20 focus:border-orange-500 appearance-none">
                            <option value="" disabled selected>-- เลือกรถที่เติม --</option>
                            @foreach($equipments as $eq)
                                <option value="{{ $eq->id }}" {{ old('equipment_id') == $eq->id ? 'selected' : '' }}>
                                    {{ $eq->name }} ({{ $eq->equipment_code }})
                                </option>
                            @endforeach
                        </select>
                    </div>
                </div>

                {{-- ส่วนของ External (ปั๊มน้ำมัน) --}}
                <div x-show="source === 'external'" class="space-y-4">
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-bold text-gray-700 mb-1">จำนวนเงิน (บาท) *</label>
                            <input type="number" step="0.01" name="amount" value="{{ old('amount') }}" placeholder="0.00" class="w-full px-4 py-3 rounded-xl border border-gray-200 focus:ring-2 focus:ring-orange-500">
                        </div>
                        <div>
                            <label class="block text-sm font-bold text-gray-700 mb-1">จำนวนลิตร</label>
                            <input type="number" step="0.01" name="liters" value="{{ old('liters') }}" placeholder="ระบุลิตร" class="w-full px-4 py-3 rounded-xl border border-gray-200 focus:ring-2 focus:ring-orange-500">
                        </div>
                    </div>
                    
                    <div>
                        <label class="block text-sm font-bold text-gray-700 mb-1">รูปสลิป/หน้าตู้ (บังคับ) *</label>
                        <input type="file" name="image" accept="image/*" class="w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-sm file:font-semibold file:bg-orange-50 file:text-orange-700 hover:file:bg-orange-100">
                    </div>
                </div>

                {{-- ส่วนของ Internal (ถังบริษัท) --}}
                <div x-show="source === 'internal'" class="space-y-4 p-4 bg-blue-50 rounded-xl border border-blue-100" style="display: none;">
                    <div>
                        <label class="block text-sm font-bold text-blue-800 mb-1">เลือกถังน้ำมัน *</label>
                        <select name="fuel_tank_id" class="w-full px-4 py-3 rounded-xl border border-blue-200 focus:ring-2 focus:ring-blue-500">
                            <option value="" disabled selected>-- เลือกถังจ่าย --</option>
                            @foreach($tanks as $tank)
                                <option value="{{ $tank->id }}" {{ old('fuel_tank_id') == $tank->id ? 'selected' : '' }}>
                                    {{ $tank->name }} (เหลือ {{ $tank->current_balance }} ลิตร)
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-bold text-blue-800 mb-1">จำนวนลิตรที่เติม *</label>
                        <input type="number" step="0.01" name="liters" value="{{ old('liters') }}" placeholder="ระบุจำนวนลิตร" class="w-full px-4 py-3 rounded-xl border border-blue-200 focus:ring-2 focus:ring-blue-500">
                    </div>
                </div>

                {{-- Common Field --}}
                <div>
                    <label class="block text-sm font-bold text-gray-700 mb-1">เลขไมล์/ชั่วโมงทำงาน</label>
                    <input type="number" step="0.1" name="mileage" value="{{ old('mileage') }}" placeholder="ระบุเลขชั่วโมงปัจจุบัน" class="w-full px-4 py-3 rounded-xl border border-gray-200 focus:ring-2 focus:ring-orange-500">
                </div>

                <div>
                    <label class="block text-sm font-bold text-gray-700 mb-1">หมายเหตุ</label>
                    <textarea name="note" rows="2" class="w-full px-4 py-3 rounded-xl border border-gray-200 focus:ring-2 focus:ring-orange-500">{{ old('note') }}</textarea>
                </div>

            </div>

            <div class="p-6 bg-gray-50 border-t border-gray-100">
                <button type="submit" class="w-full bg-gray-900 text-white font-bold py-3.5 rounded-xl shadow-lg hover:bg-black transition">
                    <i class="fa-solid fa-save mr-2"></i> บันทึกข้อมูล
                </button>
            </div>
        </div>
    </form>
</div>
{{-- Script สำหรับ AlpineJS --}}
<script src="//unpkg.com/alpinejs" defer></script>
@endsection