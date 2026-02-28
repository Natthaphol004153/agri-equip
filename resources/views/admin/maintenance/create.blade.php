@extends('layouts.admin')

@section('title', 'เพิ่มบันทึกซ่อมบำรุง')
@section('header', 'เปิดใบแจ้งซ่อมเครื่องจักร')

@section('content')
<div class="max-w-2xl mx-auto">
    
    <div class="mb-4">
        <a href="{{ route('admin.maintenance.index') }}" class="text-gray-500 hover:text-agri-primary flex items-center gap-1 font-medium transition">
            <i class="fa-solid fa-arrow-left"></i> กลับหน้ารายการ
        </a>
    </div>

    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden relative">
        <div class="absolute top-0 left-0 w-full h-1 bg-agri-primary"></div>
        <div class="px-6 py-4 border-b border-gray-100 bg-gray-50/50">
            <h2 class="text-lg font-bold text-gray-800 flex items-center gap-2">
                <i class="fa-solid fa-wrench text-agri-primary"></i> รายละเอียดการส่งซ่อม
            </h2>
            <p class="text-xs text-gray-500 mt-0.5">ระบุเครื่องจักรและอาการเสีย เพื่อนำรถเข้าสู่สถานะกำลังซ่อม</p>
        </div>

        <form action="{{ route('admin.maintenance.store') }}" method="POST" class="p-6 space-y-5">
            @csrf
            
            {{-- เลือกเครื่องจักร --}}
            <div>
                <label class="block text-sm font-bold text-gray-700 mb-1.5">เครื่องจักรที่ต้องการซ่อม <span class="text-red-500">*</span></label>
                <div class="relative">
                    <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none">
                        <i class="fa-solid fa-tractor text-gray-400"></i>
                    </div>
                    <select name="equipment_id" required class="w-full pl-10 pr-4 py-3 rounded-xl border-gray-300 focus:ring-2 focus:ring-agri-primary/20 focus:border-agri-primary transition shadow-sm bg-white appearance-none">
                        <option value="" disabled selected>-- เลือกเครื่องจักรที่ว่างอยู่ --</option>
                        @foreach($equipments as $eq)
                            <option value="{{ $eq->id }}" {{ (old('equipment_id') ?? request('equipment_id')) == $eq->id ? 'selected' : '' }}>
                                {{ $eq->name }} (รหัส: {{ $eq->equipment_code }}) - ใช้งานมาแล้ว {{ number_format($eq->current_hours, 1) }} ชม.
                            </option>
                        @endforeach
                    </select>
                    <div class="absolute inset-y-0 right-0 pr-3 flex items-center pointer-events-none">
                        <i class="fa-solid fa-chevron-down text-gray-400 text-sm"></i>
                    </div>
                </div>
                @error('equipment_id') <p class="text-red-500 text-xs mt-1.5 font-medium"><i class="fa-solid fa-circle-exclamation"></i> {{ $message }}</p> @enderror
            </div>

            {{-- สาเหตุ/อาการ --}}
            <div>
                <label class="block text-sm font-bold text-gray-700 mb-1.5">สาเหตุ/อาการเสีย <span class="text-red-500">*</span></label>
                <textarea name="description" rows="4" required
                          class="w-full p-3 rounded-xl border-gray-300 focus:ring-2 focus:ring-agri-primary/20 focus:border-agri-primary transition shadow-sm resize-none" 
                          placeholder="อธิบายอาการที่พบ หรือระบุว่าเป็นการเช็คระยะประจำรอบ...">{{ old('description') }}</textarea>
                @error('description') <p class="text-red-500 text-xs mt-1.5 font-medium"><i class="fa-solid fa-circle-exclamation"></i> {{ $message }}</p> @enderror
            </div>

            <div class="bg-blue-50 border border-blue-100 p-4 rounded-xl flex items-start gap-3">
                <i class="fa-solid fa-circle-info text-blue-500 mt-0.5"></i>
                <div class="text-sm text-blue-800">
                    <strong>หมายเหตุ:</strong> เมื่อบันทึกแล้ว เครื่องจักรนี้จะถูกเปลี่ยนสถานะเป็น <span class="bg-orange-100 text-orange-700 px-2 py-0.5 rounded text-xs">🛠️ ซ่อมบำรุง</span> ทันที และไม่สามารถรับงานจองได้จนกว่าจะกดปุ่ม "ซ่อมเสร็จสิ้น"
                </div>
            </div>

            <div class="pt-4 flex items-center justify-end gap-3 border-t border-gray-100">
                <a href="{{ route('admin.maintenance.index') }}" class="px-6 py-2.5 rounded-xl border border-gray-200 text-gray-600 hover:bg-gray-50 hover:text-gray-800 font-bold text-sm transition">
                    ยกเลิก
                </a>
                <button type="submit" class="px-6 py-2.5 rounded-xl bg-agri-primary text-white hover:bg-agri-hover font-bold text-sm shadow-lg shadow-agri-primary/30 transition transform hover:-translate-y-0.5 flex items-center gap-2">
                    <i class="fa-solid fa-paper-plane"></i> ส่งซ่อมเครื่องจักร
                </button>
            </div>
        </form>
    </div>
</div>
@endsection