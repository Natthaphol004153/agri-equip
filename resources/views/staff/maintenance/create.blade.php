@extends('layouts.staff')

@section('title', 'แจ้งซ่อม')
@section('header', 'แจ้งปัญหารถเสีย')

@section('content')
<div class="max-w-lg mx-auto pb-20" x-data="{ isSubmitting: false }">
    
    <div class="mb-4">
        <a href="{{ route('staff.maintenance.index') }}" class="inline-flex items-center gap-2 text-gray-500 hover:text-gray-700 text-sm font-bold">
            <i class="fa-solid fa-arrow-left"></i> กลับ
        </a>
    </div>

    {{-- 🔴 แสดง Error ถ้ากรอกข้อมูลไม่ครบ --}}
    @if ($errors->any())
        <div class="mb-4 p-4 bg-red-50 border-l-4 border-red-500 rounded-r-xl shadow-sm">
            <div class="flex">
                <div class="flex-shrink-0"><i class="fa-solid fa-circle-exclamation text-red-500"></i></div>
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

    <form action="{{ route('staff.maintenance.store') }}" method="POST" enctype="multipart/form-data" @submit="isSubmitting = true">
        @csrf
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
            
            <div class="bg-red-50 px-6 py-4 border-b border-red-100 flex items-center gap-3">
                <div class="w-10 h-10 rounded-full bg-red-100 text-red-600 flex items-center justify-center text-lg">
                    <i class="fa-solid fa-screwdriver-wrench"></i>
                </div>
                <div>
                    <h3 class="font-bold text-gray-800">ฟอร์มแจ้งซ่อม</h3>
                    <p class="text-xs text-red-600">กรุณาระบุอาการเสียให้ชัดเจน</p>
                </div>
            </div>

            <div class="p-6 space-y-5">
                
                <div>
                    <label class="block text-sm font-bold text-gray-700 mb-1">เครื่องจักรที่มีปัญหา *</label>
                    <div class="relative">
                        <i class="fa-solid fa-tractor absolute left-4 top-1/2 -translate-y-1/2 text-gray-400"></i>
                        <select name="equipment_id" required class="w-full pl-10 pr-4 py-3 rounded-xl border border-gray-200 bg-white focus:ring-2 focus:ring-red-500/20 focus:border-red-500 appearance-none">
                            <option value="" disabled selected>-- เลือกรถ --</option>
                            @foreach($equipments as $eq)
                                <option value="{{ $eq->id }}" {{ old('equipment_id') == $eq->id ? 'selected' : '' }}>
                                    {{ $eq->name }} ({{ $eq->equipment_code }})
                                </option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <div>
                    <label class="block text-sm font-bold text-gray-700 mb-1">อาการเสีย/สาเหตุ *</label>
                    <textarea name="description" rows="4" required class="w-full px-4 py-3 rounded-xl border border-gray-200 focus:ring-2 focus:ring-red-500/20 focus:border-red-500" placeholder="เช่น สตาร์ทไม่ติด, ยางรั่ว, มีเสียงดัง...">{{ old('description') }}</textarea>
                </div>

                <div>
                    <label class="block text-sm font-bold text-gray-700 mb-1">รูปถ่ายความเสียหาย <span class="text-xs text-gray-400 font-normal">(ถ้ามี)</span></label>
                    <input type="file" name="image" accept="image/*" capture="environment" class="block w-full text-sm text-gray-500 file:mr-4 file:py-2.5 file:px-4 file:rounded-full file:border-0 file:text-sm file:font-semibold file:bg-red-50 file:text-red-700 hover:file:bg-red-100 transition">
                </div>

            </div>

            <div class="p-6 bg-gray-50 border-t border-gray-100">
                <button type="submit" 
                    class="w-full bg-red-600 text-white font-bold py-3.5 rounded-xl shadow-lg shadow-red-200 hover:bg-red-700 hover:-translate-y-0.5 transition flex items-center justify-center gap-2 disabled:opacity-50 disabled:cursor-not-allowed"
                    :disabled="isSubmitting">
                    
                    <span x-show="!isSubmitting"><i class="fa-solid fa-paper-plane"></i> ส่งเรื่องแจ้งซ่อม</span>
                    <span x-show="isSubmitting"><i class="fa-solid fa-spinner fa-spin"></i> กำลังอัปโหลดข้อมูล...</span>
                    
                </button>
            </div>
        </div>
    </form>
</div>
@endsection