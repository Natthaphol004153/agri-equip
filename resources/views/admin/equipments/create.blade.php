@extends('layouts.admin')

@section('title', 'เพิ่มเครื่องจักร')
@section('header', 'เพิ่มเครื่องจักรใหม่')

@section('content')
<div class="max-w-5xl mx-auto">
    
    <div class="mb-6">
        <a href="{{ route('admin.equipments.index') }}" class="text-gray-500 hover:text-agri-primary text-sm flex items-center gap-1 transition w-fit font-medium">
            <i class="fa-solid fa-arrow-left"></i> กลับหน้ารายการ
        </a>
    </div>

    <form action="{{ route('admin.equipments.store') }}" method="POST" enctype="multipart/form-data">
        @csrf
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            
            {{-- 🟢 LEFT: ข้อมูลหลัก --}}
            <div class="lg:col-span-2">
                <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6 relative overflow-hidden">
                    <div class="absolute top-0 left-0 w-full h-1 bg-agri-primary"></div>
                    <h3 class="font-bold text-gray-800 text-lg mb-6 flex items-center gap-2 border-b border-gray-100 pb-3">
                        <i class="fa-solid fa-tractor text-agri-primary"></i> ข้อมูลทั่วไป
                    </h3>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-5 mb-5">
                        {{-- ชื่อ --}}
                        <div class="md:col-span-2">
                            <label class="block text-sm font-bold text-gray-700 mb-1.5">ชื่อเครื่องจักร <span class="text-red-500">*</span></label>
                            <input type="text" name="name" class="w-full px-4 py-2.5 rounded-xl border border-gray-200 focus:ring-2 focus:ring-agri-primary/20 focus:border-agri-primary outline-none transition" placeholder="เช่น รถไถ Kubota L5018" value="{{ old('name') }}" required>
                        </div>

                        {{-- ประเภท --}}
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1.5">ประเภท <span class="text-red-500">*</span></label>
                            <select name="type" id="typeSelect" class="w-full px-4 py-2.5 rounded-xl border border-gray-200 focus:ring-2 focus:ring-agri-primary/20 focus:border-agri-primary outline-none bg-white" required>
                                <option value="" selected disabled>-- เลือกประเภท --</option>
                                <option value="tractor" {{ old('type') == 'tractor' ? 'selected' : '' }}>🚜 รถไถ (Tractor)</option>
                                <option value="excavator" {{ old('type') == 'excavator' ? 'selected' : '' }}>🏗️ รถแม็คโคร (Excavator)</option>
                                <option value="drone" {{ old('type') == 'drone' ? 'selected' : '' }}>🚁 โดรน (Drone)</option>
                                <option value="harvester" {{ old('type') == 'harvester' ? 'selected' : '' }}>🌾 รถเกี่ยว (Harvester)</option>
                                <option value="sprayer" {{ old('type') == 'sprayer' ? 'selected' : '' }}>💦 รถพ่นยา (Sprayer)</option>
                                <option value="other" {{ old('type') == 'other' ? 'selected' : '' }}>📦 อื่นๆ</option>
                            </select>
                        </div>

                        {{-- รหัส (Auto) --}}
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1.5">รหัสเครื่องจักร (Code)</label>
                            <div class="relative">
                                <input type="text" name="equipment_code" id="equipmentCode" class="w-full px-4 py-2.5 rounded-xl border border-gray-200 bg-gray-50 text-gray-500 focus:outline-none cursor-not-allowed" placeholder="Auto Generate" readonly>
                                <i class="fa-solid fa-lock absolute right-4 top-1/2 -translate-y-1/2 text-gray-400 text-xs"></i>
                            </div>
                        </div>

                        {{-- ทะเบียน --}}
                        <div class="md:col-span-2">
                            <label class="block text-sm font-medium text-gray-700 mb-1.5">เลขทะเบียน / Serial No.</label>
                            <input type="text" name="registration_number" class="w-full px-4 py-2.5 rounded-xl border border-gray-200 focus:ring-2 focus:ring-agri-primary/20 focus:border-agri-primary outline-none transition" placeholder="ระบุเลขทะเบียน (ถ้ามี)" value="{{ old('registration_number') }}">
                        </div>
                    </div>
                </div>

                {{-- การตั้งค่าซ่อมบำรุงและราคา --}}
                <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6 mt-6">
                    <h3 class="font-bold text-gray-800 text-lg mb-6 flex items-center gap-2 border-b border-gray-100 pb-3">
                        <i class="fa-solid fa-screwdriver-wrench text-orange-500"></i> การตั้งค่าระบบ และ ราคา
                    </h3>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                        
                        {{-- รอบซ่อมบำรุง --}}
                        <div class="md:col-span-2 p-4 bg-orange-50 rounded-xl border border-orange-100">
                            <label class="block text-sm font-bold text-orange-800 mb-1.5">รอบซ่อมบำรุง (ชั่วโมง) <span class="text-red-500">*</span></label>
                            <div class="relative">
                                <input type="number" name="maintenance_hour_threshold" class="w-full px-4 py-2.5 rounded-xl border border-orange-200 focus:ring-2 focus:ring-orange-500/20 focus:border-orange-500 outline-none transition" value="{{ old('maintenance_hour_threshold', 100) }}" required>
                                <span class="absolute right-4 top-1/2 -translate-y-1/2 text-orange-400 text-sm font-bold">ชม.</span>
                            </div>
                            <p class="text-xs text-orange-600/70 mt-1.5"><i class="fa-solid fa-bell"></i> ระบบจะแจ้งเตือนเมื่อใช้งานครบกำหนด</p>
                        </div>

                        {{-- ราคาต่อชั่วโมง --}}
                        <div>
                            <label class="block text-sm font-bold text-gray-700 mb-1.5">ค่าเช่า (บาท/ชั่วโมง)</label>
                            <div class="relative">
                                <input type="number" name="hourly_rate" class="w-full px-4 py-2.5 rounded-xl border border-gray-200 focus:ring-2 focus:ring-agri-primary/20 focus:border-agri-primary outline-none transition text-lg" value="{{ old('hourly_rate', 0) }}" min="0" step="0.01">
                                <span class="absolute right-4 top-1/2 -translate-y-1/2 text-gray-400 text-sm font-bold">฿/ชม.</span>
                            </div>
                            <p class="text-xs text-gray-400 mt-1.5">กรณีคิดราคาตามเวลาใช้งาน</p>
                        </div>

                        {{-- ✅ เพิ่ม: ราคาต่อไร่ --}}
                        <div>
                            <label class="block text-sm font-bold text-gray-700 mb-1.5">ค่าเช่า (บาท/ไร่)</label>
                            <div class="relative">
                                <input type="number" name="price_per_rai" class="w-full px-4 py-2.5 rounded-xl border border-gray-200 focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 outline-none transition text-lg" value="{{ old('price_per_rai', 0) }}" min="0" step="0.01">
                                <span class="absolute right-4 top-1/2 -translate-y-1/2 text-blue-400 text-sm font-bold">฿/ไร่</span>
                            </div>
                            <p class="text-xs text-gray-400 mt-1.5">กรณีคิดราคาเหมาตามพื้นที่</p>
                        </div>

                    </div>
                </div>
            </div>

            {{-- 🔵 RIGHT: รูปภาพ & สถานะ --}}
            <div class="lg:col-span-1 space-y-6">
                <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
                    <label class="block text-sm font-bold text-gray-700 mb-3">รูปภาพเครื่องจักร</label>
                    
                    {{-- Image Preview --}}
                    <div class="relative w-full aspect-square bg-gray-50 rounded-xl border-2 border-dashed border-gray-300 flex flex-col items-center justify-center overflow-hidden hover:bg-gray-100 hover:border-agri-primary transition group cursor-pointer" onclick="document.getElementById('imageInput').click()">
                        <img id="imagePreview" class="absolute inset-0 w-full h-full object-cover hidden">
                        <div id="uploadPlaceholder" class="text-center p-4">
                            <i class="fa-solid fa-cloud-arrow-up text-4xl text-gray-300 mb-3 group-hover:text-agri-primary transition-colors"></i>
                            <p class="text-sm font-medium text-gray-500">คลิกเพื่ออัปโหลดรูปภาพ</p>
                            <p class="text-xs text-gray-400 mt-1">รองรับ JPG, PNG (สูงสุด 2MB)</p>
                        </div>
                    </div>
                    <input type="file" name="image" id="imageInput" class="hidden" accept="image/*" onchange="previewImage(event)">
                </div>

                <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
                    <label class="block text-sm font-bold text-gray-700 mb-3">สถานะเริ่มต้น</label>
                    <div class="relative">
                        <i class="fa-solid fa-toggle-on absolute left-4 top-1/2 -translate-y-1/2 text-gray-400"></i>
                        <select name="current_status" class="w-full pl-10 pr-4 py-2.5 rounded-xl border border-gray-200 focus:ring-2 focus:ring-agri-primary/20 focus:border-agri-primary outline-none bg-white font-medium text-gray-700 cursor-pointer">
                            <option value="available" class="text-green-600">✅ ว่าง (Available)</option>
                            <option value="maintenance" class="text-orange-600">🛠️ ซ่อมบำรุง (Maintenance)</option>
                        </select>
                    </div>
                </div>

                <button type="submit" class="w-full bg-agri-primary text-white py-3.5 rounded-xl shadow-lg shadow-agri-primary/30 hover:bg-agri-hover hover:-translate-y-0.5 transition font-bold flex items-center justify-center gap-2 text-lg">
                    <i class="fa-solid fa-save"></i> บันทึกข้อมูล
                </button>
            </div>
        </div>
    </form>
</div>

<script>
    // Preview Image
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

    // Auto Generate Code
    document.addEventListener('DOMContentLoaded', function() {
        const typeSelect = document.getElementById('typeSelect');
        const codeInput = document.getElementById('equipmentCode');
        const prefixes = { 'tractor': 'TR-', 'excavator': 'EX-', 'drone': 'DR-', 'harvester': 'HV-', 'sprayer': 'SP-', 'other': 'OT-' };

        typeSelect.addEventListener('change', function() {
            const prefix = prefixes[this.value] || 'EQ-';
            const random = Math.floor(Math.random() * 900) + 100; 
            codeInput.value = prefix + random;
        });
    });
</script>
@endsection