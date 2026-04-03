@extends('layouts.admin')

@section('title', 'เปิดใบสั่งซ่อม')
@section('header', 'เปิดใบสั่งซ่อมบำรุง')

@section('content')
@php
    $viewer = auth()->user();
    $viewerName = $viewer->name ?? 'ไม่ทราบชื่อผู้ใช้งาน';
    $viewerRole = match ($viewer->role ?? null) {
        'admin' => 'แอดมิน',
        'staff' => 'พนักงาน',
        default => 'ผู้ใช้งานระบบ',
    };
@endphp
<div class="max-w-2xl mx-auto" x-data="{ isSubmitting: false }">
    <div class="flex items-center gap-2 mb-4">
        <a href="{{ route('admin.maintenance.index') }}" class="text-gray-500 hover:text-gray-700 font-bold text-sm transition">
            <i class="fa-solid fa-arrow-left"></i> กลับหน้ารายการ
        </a>
    </div>

    {{-- 🔴 ส่วนแสดง Error จาก Validation --}}
    @if ($errors->any())
        <div class="mb-4 p-4 bg-red-50 border-l-4 border-red-500 rounded-r-xl shadow-sm animate-fade-in-down">
            <div class="flex">
                <div class="flex-shrink-0">
                    <i class="fa-solid fa-circle-exclamation text-red-500"></i>
                </div>
                <div class="ml-3">
                    <h3 class="text-sm font-bold text-red-800">กรุณาตรวจสอบข้อมูล:</h3>
                    <ul class="mt-1 list-disc list-inside text-xs text-red-700">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            </div>
        </div>
    @endif

    <div class="bg-white p-6 rounded-2xl shadow-sm border border-gray-100">
        <div class="flex items-center gap-3 mb-6 border-b pb-4">
            <div class="w-12 h-12 bg-blue-100 text-blue-600 rounded-xl flex items-center justify-center text-2xl">
                <i class="fa-solid fa-clipboard-list"></i>
            </div>
            <div>
                <h2 class="text-xl font-bold text-gray-800">เปิดบิลส่งซ่อมใหม่</h2>
                <p class="text-sm text-gray-500">สำหรับรถที่ว่างอยู่ หรือต้องการเข้าเช็คระยะ</p>
            </div>
        </div>

        <div class="mb-6 rounded-xl border border-indigo-100 bg-indigo-50 px-4 py-3 text-sm">
            <div class="flex items-center gap-2 text-indigo-700 font-bold">
                <i class="fa-solid fa-user-check"></i>
                ผู้ที่กำลังเปิดฟอร์มนี้
            </div>
            <p class="mt-1 text-indigo-800">{{ $viewerName }} ({{ $viewerRole }})</p>
        </div>

        <form action="{{ route('admin.maintenance.store') }}" method="POST" @submit="isSubmitting = true">
            @csrf
            <div class="space-y-5">
                
                {{-- 🚜 เลือกเครื่องจักร --}}
                <div>
                    <label class="block text-sm font-bold text-gray-700 mb-2">เลือกเครื่องจักรที่ต้องการส่งซ่อม <span class="text-red-500">*</span></label>
                    
                    @if($equipments->isEmpty())
                        <div class="bg-red-50 text-red-600 p-4 rounded-xl border border-red-200 text-sm font-bold flex items-center gap-3">
                            <i class="fa-solid fa-triangle-exclamation text-2xl"></i>
                            <div>
                                ตอนนี้ไม่มีเครื่องจักรที่พร้อมส่งซ่อมเพิ่ม
                                <span class="block font-normal text-xs text-red-500 mt-0.5">เครื่องจักรทั้งหมดอาจอยู่ระหว่างซ่อมหรือมีใบซ่อมค้างอยู่แล้ว</span>
                            </div>
                        </div>
                    @else
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                                <i class="fa-solid fa-tractor text-gray-400"></i>
                            </div>
                            <select name="equipment_id" required class="w-full pl-11 pr-10 py-3.5 rounded-xl border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 text-gray-700 font-medium appearance-none bg-white cursor-pointer hover:bg-gray-50 transition">
                                <option value="" disabled selected>-- แตะเพื่อเลือกรถ --</option>
                                @foreach($equipments as $eq)
                                    @php
                                        $isKmTracking = ($eq->tracking_type ?? 'hours') === 'kilometers';
                                        $meterValue = $isKmTracking ? ($eq->current_kilometers ?? 0) : ($eq->current_hours ?? 0);
                                        $meterUnit = $isKmTracking ? 'กม.' : 'ชม.';
                                        $statusLabel = match($eq->current_status) {
                                            'available' => 'ว่าง',
                                            'booked' => 'จองคิวแล้ว',
                                            'in_use' => 'กำลังใช้งาน',
                                            'breakdown' => 'เสีย/งดใช้',
                                            'maintenance' => 'กำลังซ่อม',
                                            default => $eq->current_status,
                                        };
                                    @endphp
                                    <option value="{{ $eq->id }}" {{ old('equipment_id') == $eq->id ? 'selected' : '' }}>
                                        {{ $eq->name }} ({{ $eq->equipment_code }}) - สถานะ: {{ $statusLabel }} - มิเตอร์ {{ number_format($meterValue) }} {{ $meterUnit }}
                                    </option>
                                @endforeach
                            </select>
                            <div class="absolute inset-y-0 right-0 pr-4 flex items-center pointer-events-none">
                                <i class="fa-solid fa-chevron-down text-gray-400 text-xs"></i>
                            </div>
                        </div>
                    @endif
                </div>

                {{-- 📝 รายละเอียด --}}
                <div>
                    <label class="block text-sm font-bold text-gray-700 mb-2">สาเหตุการซ่อม / รายละเอียด <span class="text-red-500">*</span></label>
                    <textarea name="description" rows="3" required class="w-full rounded-xl border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 p-4 text-gray-700" placeholder="เช่น เปลี่ยนถ่ายน้ำมันเครื่องประจำปี, ยางรั่ว, เช็คระบบไฟ...">{{ old('description') }}</textarea>
                </div>
            </div>

            {{-- 🚀 ปุ่ม Submit --}}
            <div class="mt-8 border-t border-gray-100 pt-5">
                <button type="submit" 
                    class="w-full bg-blue-600 hover:bg-blue-700 text-white font-bold py-3.5 rounded-xl shadow-lg shadow-blue-200 transition-all flex items-center justify-center gap-2 disabled:opacity-50 disabled:cursor-not-allowed active:scale-95" 
                    :disabled="isSubmitting || {{ $equipments->isEmpty() ? 'true' : 'false' }}">
                    
                    {{-- โชว์สถานะปกติ --}}
                    <span x-show="!isSubmitting" class="flex items-center gap-2">
                        <i class="fa-solid fa-wrench"></i> บันทึกส่งเข้าซ่อม
                    </span>
                    
                    {{-- โชว์ตอนกำลังโหลด --}}
                    <span x-show="isSubmitting" class="flex items-center gap-2" style="display: none;">
                        <i class="fa-solid fa-spinner fa-spin"></i> กำลังบันทึกข้อมูล...
                    </span>
                </button>
            </div>
        </form>
    </div>
</div>
@endsection