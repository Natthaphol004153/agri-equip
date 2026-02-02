@extends('layouts.admin')

@section('title', 'แก้ไขเครื่องจักร')
@section('header', 'แก้ไขข้อมูล: ' . $equipment->name)

@section('content')
<div class="max-w-5xl mx-auto">
    {{-- ปุ่มย้อนกลับ --}}
    <div class="mb-6">
        <a href="{{ route('admin.equipments.index') }}" class="text-gray-500 hover:text-gray-700 text-sm flex items-center gap-1 transition w-fit">
            <i class="fa-solid fa-arrow-left"></i> กลับหน้ารายการ
        </a>
    </div>

    <form action="{{ route('admin.equipments.update', $equipment->id) }}" method="POST" enctype="multipart/form-data">
        @csrf
        @method('PUT')

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            {{-- 🟢 LEFT COLUMN: ข้อมูล --}}
            <div class="lg:col-span-2 space-y-6">
                
                {{-- Card 1: ข้อมูลทั่วไป --}}
                <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
                    <h3 class="font-bold text-gray-800 text-lg mb-6 flex items-center gap-2 border-b border-gray-100 pb-3">
                        <i class="fa-solid fa-pen-to-square text-orange-500"></i> ข้อมูลทั่วไป
                    </h3>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-5 mb-5">
                        <div class="md:col-span-2">
                            <label class="block text-sm font-bold text-gray-700 mb-1.5">ชื่อเครื่องจักร <span class="text-red-500">*</span></label>
                            <input type="text" name="name" class="w-full px-4 py-2.5 rounded-xl border border-gray-200 focus:ring-2 focus:ring-agri-primary/20 focus:border-agri-primary outline-none transition" value="{{ old('name', $equipment->name) }}" required>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1.5">ประเภท</label>
                            <select name="type" class="w-full px-4 py-2.5 rounded-xl border border-gray-200 bg-white" required>
                                <option value="tractor" {{ $equipment->type == 'tractor' ? 'selected' : '' }}>รถไถ</option>
                                <option value="drone" {{ $equipment->type == 'drone' ? 'selected' : '' }}>โดรน</option>
                                <option value="harvester" {{ $equipment->type == 'harvester' ? 'selected' : '' }}>รถเกี่ยว</option>
                                <option value="sprayer" {{ $equipment->type == 'sprayer' ? 'selected' : '' }}>รถพ่นยา</option>
                                <option value="other" {{ $equipment->type == 'other' ? 'selected' : '' }}>อื่นๆ</option>
                            </select>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1.5">รหัสเครื่องจักร</label>
                            <input type="text" name="equipment_code" class="w-full px-4 py-2.5 rounded-xl border border-gray-200 bg-gray-50 text-gray-500" value="{{ $equipment->equipment_code }}" readonly>
                        </div>
                        <div class="md:col-span-2">
                            <label class="block text-sm font-medium text-gray-700 mb-1.5">เลขทะเบียน</label>
                            <input type="text" name="registration_number" class="w-full px-4 py-2.5 rounded-xl border border-gray-200" value="{{ old('registration_number', $equipment->registration_number) }}">
                        </div>
                    </div>
                </div>

                {{-- Card 2: การตั้งค่า --}}
                <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
                    <h3 class="font-bold text-gray-800 text-lg mb-6 flex items-center gap-2 border-b border-gray-100 pb-3">
                        <i class="fa-solid fa-screwdriver-wrench text-orange-500"></i> การตั้งค่า
                    </h3>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                        <div>
                            <label class="block text-sm font-bold text-gray-700 mb-1.5">รอบซ่อมบำรุง (ชม.)</label>
                            <input type="number" name="maintenance_hour_threshold" class="w-full px-4 py-2.5 rounded-xl border border-gray-200" value="{{ old('maintenance_hour_threshold', $equipment->maintenance_hour_threshold) }}" required>
                        </div>
                        <div>
                            <label class="block text-sm font-bold text-gray-700 mb-1.5">ค่าเช่า (บาท/ชม.)</label>
                            <input type="number" name="hourly_rate" class="w-full px-4 py-2.5 rounded-xl border border-gray-200" value="{{ old('hourly_rate', $equipment->hourly_rate) }}" required>
                        </div>
                        <div class="md:col-span-2">
                            <label class="block text-sm font-medium text-gray-500 mb-1.5">ชั่วโมงใช้งานสะสม (แก้ไขไม่ได้)</label>
                            <input type="text" class="w-full px-4 py-2.5 rounded-xl border border-gray-200 bg-gray-100 text-gray-500" value="{{ $equipment->current_hours }} ชม." readonly>
                        </div>
                    </div>
                </div>
            </div>

            {{-- 🔵 RIGHT COLUMN: รูปภาพ & ปุ่มบันทึก --}}
            <div class="lg:col-span-1 space-y-6">
                <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
                    <label class="block text-sm font-bold text-gray-700 mb-3">รูปภาพ</label>
                    <div class="relative w-full aspect-square bg-gray-50 rounded-xl border-2 border-dashed border-gray-200 flex flex-col items-center justify-center overflow-hidden hover:bg-gray-100 transition group cursor-pointer" onclick="document.getElementById('imageInput').click()">
                        {{-- รูปภาพ Preview --}}
                        <img id="imagePreview" src="{{ $equipment->image_path ? asset($equipment->image_path) : '' }}" class="absolute inset-0 w-full h-full object-cover {{ $equipment->image_path ? '' : 'hidden' }}">
                        
                        {{-- Placeholder เมื่อไม่มีรูป --}}
                        <div id="uploadPlaceholder" class="text-center p-4 {{ $equipment->image_path ? 'hidden' : '' }}">
                            <i class="fa-solid fa-cloud-arrow-up text-3xl text-gray-300 mb-2"></i>
                            <p class="text-xs text-gray-500">คลิกเปลี่ยนรูป</p>
                        </div>
                    </div>
                    <input type="file" name="image" id="imageInput" class="hidden" accept="image/*" onchange="previewImage(event)">
                </div>

                <button type="submit" class="w-full bg-agri-primary text-white py-3 rounded-xl shadow-lg shadow-agri-primary/30 hover:bg-agri-hover transition font-bold flex items-center justify-center gap-2">
                    <i class="fa-solid fa-save"></i> บันทึกการแก้ไข
                </button>
            </div>
        </div>
    </form>
</div>

<script>
    function previewImage(event) {
        const reader = new FileReader();
        reader.onload = function(){
            const output = document.getElementById('imagePreview');
            const placeholder = document.getElementById('uploadPlaceholder');
            output.src = reader.result;
            output.classList.remove('hidden');
            placeholder.classList.add('hidden');
        };
        if(event.target.files[0]) reader.readAsDataURL(event.target.files[0]);
    }
</script>
@endsection