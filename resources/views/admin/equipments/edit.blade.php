@extends('layouts.admin')

@section('title', 'แก้ไขเครื่องจักร')
@section('header', 'แก้ไขข้อมูล: ' . $equipment->name)

@section('content')
<div class="max-w-5xl mx-auto">
    {{-- ปุ่มย้อนกลับ --}}
    <div class="mb-6">
        <a href="{{ route('admin.equipments.index') }}" class="text-gray-500 hover:text-agri-primary text-sm flex items-center gap-1 transition w-fit font-medium">
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
                <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6 relative overflow-hidden">
                    <div class="absolute top-0 left-0 w-full h-1 bg-orange-500"></div>
                    <h3 class="font-bold text-gray-800 text-lg mb-6 flex items-center gap-2 border-b border-gray-100 pb-3">
                        <i class="fa-solid fa-pen-to-square text-orange-500"></i> ข้อมูลทั่วไป
                    </h3>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-5 mb-5">
                        <div class="md:col-span-2">
                            <label class="block text-sm font-bold text-gray-700 mb-1.5">ชื่อเครื่องจักร <span class="text-red-500">*</span></label>
                            <input type="text" name="name" class="w-full px-4 py-2.5 rounded-xl border border-gray-200 focus:ring-2 focus:ring-agri-primary/20 focus:border-agri-primary outline-none transition" value="{{ old('name', $equipment->name) }}" required>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1.5">กลุ่มอุปกรณ์</label>
                            <select name="equipment_group" id="equipmentGroupSelect" class="w-full px-4 py-2.5 rounded-xl border border-gray-200 focus:ring-2 focus:ring-agri-primary/20 focus:border-agri-primary outline-none bg-white cursor-pointer" required>
                                <option value="machine" {{ old('equipment_group', $equipment->equipment_group ?? 'machine') == 'machine' ? 'selected' : '' }}>🚜 เครื่องจักรภาคพื้น</option>
                                <option value="drone" {{ old('equipment_group', $equipment->equipment_group) == 'drone' ? 'selected' : '' }}>🔋 โดรน/อุปกรณ์ไฟฟ้า</option>
                            </select>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1.5">รูปแบบมิเตอร์</label>
                            <select name="tracking_type" id="trackingTypeSelect" class="w-full px-4 py-2.5 rounded-xl border border-gray-200 focus:ring-2 focus:ring-agri-primary/20 focus:border-agri-primary outline-none bg-white cursor-pointer" required>
                                <option value="hours" {{ old('tracking_type', $equipment->tracking_type) == 'hours' ? 'selected' : '' }}>⏱️ ชั่วโมง</option>
                                <option value="kilometers" {{ old('tracking_type', $equipment->tracking_type) == 'kilometers' ? 'selected' : '' }}>🛣️ กิโลเมตร</option>
                            </select>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1.5">ประเภท</label>
                            <select name="type" id="typeSelect" class="w-full px-4 py-2.5 rounded-xl border border-gray-200 focus:ring-2 focus:ring-agri-primary/20 focus:border-agri-primary outline-none bg-white cursor-pointer" required>
                                <option value="tractor" {{ $equipment->type == 'tractor' ? 'selected' : '' }}>🚜 รถไถ</option>
                                <option value="excavator" {{ $equipment->type == 'excavator' ? 'selected' : '' }}>🏗️ รถแม็คโคร</option>
                                <option value="drone" {{ $equipment->type == 'drone' ? 'selected' : '' }}>🚁 โดรน</option>
                                <option value="harvester" {{ $equipment->type == 'harvester' ? 'selected' : '' }}>🌾 รถเกี่ยว</option>
                                <option value="sprayer" {{ $equipment->type == 'sprayer' ? 'selected' : '' }}>💦 รถพ่นยา</option>
                                <option value="other" {{ $equipment->type == 'other' ? 'selected' : '' }}>📦 อื่นๆ</option>
                            </select>
                        </div>

                        <div id="customTypeBlock" class="md:col-span-2 {{ $equipment->type == 'other' ? '' : 'hidden' }}">
                            <label class="block text-sm font-bold text-gray-700 mb-1.5">ระบุประเภทเพิ่มเติม (นวัตกรรมใหม่) <span class="text-red-500">*</span></label>
                            <input type="text" name="custom_type_name" id="customTypeInput" class="w-full px-4 py-2.5 rounded-xl border border-gray-200 focus:ring-2 focus:ring-agri-primary/20 focus:border-agri-primary outline-none transition" value="{{ old('custom_type_name', $equipment->custom_type_name) }}" placeholder="เช่น โดรนหว่านเมล็ดอัตโนมัติ / UGV เกษตร">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1.5">รหัสเครื่องจักร</label>
                            <div class="relative">
                                <input type="text" name="equipment_code" class="w-full px-4 py-2.5 rounded-xl border border-gray-200 bg-gray-50 text-gray-500 outline-none cursor-not-allowed" value="{{ $equipment->equipment_code }}" readonly>
                                <i class="fa-solid fa-lock absolute right-4 top-1/2 -translate-y-1/2 text-gray-400 text-xs"></i>
                            </div>
                        </div>
                        <div class="md:col-span-2">
                            <label class="block text-sm font-medium text-gray-700 mb-1.5">เลขทะเบียน / Serial No.</label>
                            <input type="text" name="registration_number" class="w-full px-4 py-2.5 rounded-xl border border-gray-200 focus:ring-2 focus:ring-agri-primary/20 focus:border-agri-primary outline-none transition" value="{{ old('registration_number', $equipment->registration_number) }}">
                        </div>
                    </div>
                </div>

                {{-- Card 2: การตั้งค่า --}}
                <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
                    <h3 class="font-bold text-gray-800 text-lg mb-6 flex items-center gap-2 border-b border-gray-100 pb-3">
                        <i class="fa-solid fa-screwdriver-wrench text-orange-500"></i> การตั้งค่าระบบ และ ราคา
                    </h3>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                        
                        {{-- รอบซ่อมบำรุง --}}
                        <div id="maintenanceHourBlock" class="md:col-span-2 p-4 bg-orange-50 rounded-xl border border-orange-100">
                            <label class="block text-sm font-bold text-orange-800 mb-1.5">รอบซ่อมบำรุง (ชั่วโมง)</label>
                            <div class="relative">
                                <input type="number" name="maintenance_hour_threshold" id="maintenanceHourInput" class="w-full px-4 py-2.5 rounded-xl border border-orange-200 focus:ring-2 focus:ring-orange-500/20 focus:border-orange-500 outline-none transition" value="{{ old('maintenance_hour_threshold', $equipment->maintenance_hour_threshold) }}">
                                <span class="absolute right-4 top-1/2 -translate-y-1/2 text-orange-400 text-sm font-bold">ชม.</span>
                            </div>
                        </div>

                        <div id="maintenanceKmBlock" class="md:col-span-2 p-4 bg-orange-50 rounded-xl border border-orange-100 hidden">
                            <label class="block text-sm font-bold text-orange-800 mb-1.5">รอบซ่อมบำรุง (กิโลเมตร)</label>
                            <div class="relative">
                                <input type="number" name="maintenance_km_threshold" id="maintenanceKmInput" class="w-full px-4 py-2.5 rounded-xl border border-orange-200 focus:ring-2 focus:ring-orange-500/20 focus:border-orange-500 outline-none transition" value="{{ old('maintenance_km_threshold', $equipment->maintenance_km_threshold) }}">
                                <span class="absolute right-4 top-1/2 -translate-y-1/2 text-orange-400 text-sm font-bold">กม.</span>
                            </div>
                        </div>

                        {{-- ราคาต่อชั่วโมง --}}
                        <div>
                            <label class="block text-sm font-bold text-gray-700 mb-1.5">ค่าเช่า (บาท/ชั่วโมง)</label>
                            <div class="relative">
                                <input type="number" name="hourly_rate" class="w-full px-4 py-2.5 rounded-xl border border-gray-200 focus:ring-2 focus:ring-agri-primary/20 focus:border-agri-primary outline-none transition text-lg" value="{{ old('hourly_rate', $equipment->hourly_rate) }}" min="0" step="0.01">
                                <span class="absolute right-4 top-1/2 -translate-y-1/2 text-gray-400 text-sm font-bold">฿/ชม.</span>
                            </div>
                        </div>

                        {{-- ✅ เพิ่ม: ราคาต่อไร่ --}}
                        <div>
                            <label class="block text-sm font-bold text-gray-700 mb-1.5">ค่าเช่า (บาท/ไร่)</label>
                            <div class="relative">
                                <input type="number" name="price_per_rai" class="w-full px-4 py-2.5 rounded-xl border border-gray-200 focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 outline-none transition text-lg" value="{{ old('price_per_rai', $equipment->price_per_rai) }}" min="0" step="0.01">
                                <span class="absolute right-4 top-1/2 -translate-y-1/2 text-blue-400 text-sm font-bold">฿/ไร่</span>
                            </div>
                        </div>

                        {{-- ชั่วโมงใช้งานสะสม --}}
                        <div class="md:col-span-2 mt-2">
                            <label class="block text-sm font-medium text-gray-500 mb-1.5">มิเตอร์ปัจจุบัน (ดึงจากระบบอัตโนมัติ)</label>
                            <div class="relative">
                                <input type="text" id="currentMeterDisplay" class="w-full px-4 py-2.5 rounded-xl border border-gray-200 bg-gray-100 text-gray-500 font-bold outline-none cursor-not-allowed" readonly>
                                <i class="fa-solid fa-clock-rotate-left absolute right-4 top-1/2 -translate-y-1/2 text-gray-400"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- 🔵 RIGHT COLUMN: รูปภาพ & ปุ่มบันทึก --}}
            <div class="lg:col-span-1 space-y-6">
                <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
                    <label class="block text-sm font-bold text-gray-700 mb-3">รูปภาพ</label>
                    <div class="relative w-full aspect-square bg-gray-50 rounded-xl border-2 border-dashed border-gray-300 flex flex-col items-center justify-center overflow-hidden hover:bg-gray-100 hover:border-agri-primary transition group cursor-pointer" onclick="document.getElementById('imageInput').click()">
                        
                        {{-- รูปภาพ Preview --}}
                        <img id="imagePreview" src="{{ $equipment->image_path ? asset($equipment->image_path) : '' }}" class="absolute inset-0 w-full h-full object-cover {{ $equipment->image_path ? '' : 'hidden' }}">
                        
                        {{-- Placeholder เมื่อไม่มีรูป --}}
                        <div id="uploadPlaceholder" class="text-center p-4 {{ $equipment->image_path ? 'hidden' : '' }}">
                            <i class="fa-solid fa-cloud-arrow-up text-4xl text-gray-300 mb-3 group-hover:text-agri-primary transition-colors"></i>
                            <p class="text-sm font-medium text-gray-500">คลิกเปลี่ยนรูป</p>
                            <p class="text-xs text-gray-400 mt-1">รองรับ JPG, PNG</p>
                        </div>
                    </div>
                    <input type="file" name="image" id="imageInput" class="hidden" accept="image/*" onchange="previewImage(event)">
                </div>

                <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
                    <label class="block text-sm font-bold text-gray-700 mb-3">สถานะเครื่องจักร</label>
                    <div class="relative">
                        <i class="fa-solid fa-toggle-on absolute left-4 top-1/2 -translate-y-1/2 text-gray-400"></i>
                        <select name="current_status" class="w-full pl-10 pr-4 py-2.5 rounded-xl border border-gray-200 focus:ring-2 focus:ring-agri-primary/20 focus:border-agri-primary outline-none bg-white font-medium text-gray-700 cursor-pointer">
                            <option value="available" {{ $equipment->current_status == 'available' ? 'selected' : '' }} class="text-green-600">✅ ว่าง (Available)</option>
                            <option value="booked" {{ $equipment->current_status == 'booked' ? 'selected' : '' }} class="text-blue-600">📅 จองคิวแล้ว (Booked)</option>
                            <option value="in_use" {{ $equipment->current_status == 'in_use' ? 'selected' : '' }} class="text-purple-600">🚜 กำลังทำงาน (In Use)</option>
                            <option value="maintenance" {{ $equipment->current_status == 'maintenance' ? 'selected' : '' }} class="text-orange-600">🛠️ ซ่อมบำรุง (Maintenance)</option>
                            <option value="breakdown" {{ $equipment->current_status == 'breakdown' ? 'selected' : '' }} class="text-red-600">🚨 เสียหาย (Breakdown)</option>
                        </select>
                    </div>
                </div>

                <button type="submit" class="w-full bg-agri-primary text-white py-3.5 rounded-xl shadow-lg shadow-agri-primary/30 hover:bg-agri-hover hover:-translate-y-0.5 transition font-bold flex items-center justify-center gap-2 text-lg">
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

    document.addEventListener('DOMContentLoaded', function() {
        const groupSelect = document.getElementById('equipmentGroupSelect');
        const trackingTypeSelect = document.getElementById('trackingTypeSelect');
        const maintenanceHourBlock = document.getElementById('maintenanceHourBlock');
        const maintenanceKmBlock = document.getElementById('maintenanceKmBlock');
        const maintenanceHourInput = document.getElementById('maintenanceHourInput');
        const maintenanceKmInput = document.getElementById('maintenanceKmInput');
        const currentMeterDisplay = document.getElementById('currentMeterDisplay');
        const typeSelect = document.getElementById('typeSelect');
        const customTypeBlock = document.getElementById('customTypeBlock');
        const customTypeInput = document.getElementById('customTypeInput');

        const currentHours = {{ (float) ($equipment->current_hours ?? 0) }};
        const currentKilometers = {{ (float) ($equipment->current_kilometers ?? 0) }};

        function syncMeterUI() {
            const trackingType = trackingTypeSelect.value;
            const equipmentGroup = groupSelect.value;

            if (equipmentGroup === 'drone' && trackingType !== 'hours') {
                trackingTypeSelect.value = 'hours';
            }

            const isHours = trackingTypeSelect.value === 'hours';
            maintenanceHourBlock.classList.toggle('hidden', !isHours);
            maintenanceKmBlock.classList.toggle('hidden', isHours);

            maintenanceHourInput.required = isHours;
            maintenanceKmInput.required = !isHours;

            currentMeterDisplay.value = isHours
                ? `${currentHours.toFixed(2)} ชั่วโมง`
                : `${currentKilometers.toFixed(2)} กิโลเมตร`;

            const isOtherType = typeSelect.value === 'other';
            customTypeBlock.classList.toggle('hidden', !isOtherType);
            customTypeInput.required = isOtherType;
        }

        groupSelect.addEventListener('change', function() {
            if (this.value === 'drone') {
                trackingTypeSelect.value = 'hours';
            }
            syncMeterUI();
        });

        trackingTypeSelect.addEventListener('change', syncMeterUI);
        typeSelect.addEventListener('change', syncMeterUI);
        syncMeterUI();
    });
</script>
@endsection