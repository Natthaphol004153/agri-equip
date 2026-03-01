@extends('layouts.admin')

@section('title', 'รับเรื่องแจ้งซ่อม')
@section('header', 'ตรวจสอบแจ้งเหตุรถเสีย')

@section('content')
<div class="max-w-2xl mx-auto">
    <div class="flex items-center gap-2 mb-4">
        <a href="{{ route('admin.maintenance.index') }}" class="text-gray-500 hover:text-gray-700 font-bold text-sm"><i class="fa-solid fa-arrow-left"></i> กลับหน้ารายการ</a>
    </div>

    <div class="bg-white p-6 rounded-2xl shadow-sm border border-red-200">
        <div class="flex items-center gap-3 mb-6 pb-4 border-b border-gray-100">
            <div class="w-12 h-12 bg-red-100 text-red-600 rounded-xl flex items-center justify-center text-2xl">
                <i class="fa-solid fa-triangle-exclamation"></i>
            </div>
            <div>
                <h2 class="text-xl font-bold text-gray-800">รับเรื่องแจ้งซ่อมด่วน</h2>
                <p class="text-sm text-gray-500">เครื่องจักร: <span class="font-bold text-agri-primary">{{ $log->equipment->name }} ({{ $log->equipment->equipment_code }})</span></p>
            </div>
        </div>

        <div class="space-y-4 mb-6">
            <div>
                <p class="text-xs text-gray-500 font-bold uppercase mb-1">วันที่แจ้ง</p>
                <p class="text-sm font-medium text-gray-800">{{ \Carbon\Carbon::parse($log->created_at)->format('d/m/Y H:i น.') }}</p>
            </div>
            <div class="bg-gray-50 p-4 rounded-xl border border-gray-200">
                <p class="text-xs text-gray-500 font-bold uppercase mb-1">อาการเสียที่พนักงานแจ้งมา</p>
                <p class="font-medium text-red-600 text-lg">"{{ $log->description }}"</p>
            </div>

            @if($log->image_url)
            <div>
                <p class="text-xs text-gray-500 font-bold uppercase mb-2">รูปถ่ายหน้างาน</p>
                <a href="{{ asset('storage/' . $log->image_url) }}" target="_blank">
                    <img src="{{ asset('storage/' . $log->image_url) }}" class="w-full max-h-64 object-cover rounded-xl border hover:opacity-90 transition">
                </a>
            </div>
            @endif
        </div>

        <form action="{{ route('admin.maintenance.accept', $log->id) }}" method="POST" class="bg-red-50 p-5 rounded-xl border border-red-100">
            @csrf
            <label class="block text-sm font-bold text-red-800 mb-2"><i class="fa-solid fa-pen-to-square"></i> บันทึกโน้ตของแอดมิน (ก่อนรับเรื่อง)</label>
            <textarea name="admin_note" rows="2" required class="w-full rounded-xl border-red-200 shadow-sm focus:border-red-500 focus:ring-red-500 p-3 mb-4" placeholder="ระบุการตัดสินใจ เช่น สั่งให้จอดรอช่าง หรือนำกลับบริษัท..."></textarea>
            
            <button type="submit" class="w-full bg-red-600 hover:bg-red-700 text-white font-bold py-3.5 rounded-xl shadow-lg shadow-red-200 transition-all flex items-center justify-center gap-2">
                <i class="fa-solid fa-check"></i> รับทราบและเปลี่ยนสถานะเป็น "กำลังซ่อม"
            </button>
        </form>
    </div>
</div>
@endsection