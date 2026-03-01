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

                        <div class="md:col-span-2">
                            <label class="block text-sm font-medium text-gray-700 mb-2">กลุ่มอุปกรณ์ <span class="text-red-500">*</span></label>
                            <input type="hidden" name="equipment_group" id="equipmentGroupInput" value="{{ old('equipment_group', 'machine') }}" required>
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-3" id="equipmentGroupCards">
                                <button type="button" data-group="machine"
                                    class="equipment-group-card w-full text-left border-2 rounded-xl p-4 transition hover:bg-gray-50">
                                    <p class="font-bold text-gray-800">🚜 เครื่องจักรภาคพื้น</p>
                                    <p class="text-xs text-gray-500 mt-1">รถไถ รถเกี่ยว รถพ่นยา และอุปกรณ์ภาคพื้น</p>
                                </button>
                                <button type="button" data-group="drone"
                                    class="equipment-group-card w-full text-left border-2 rounded-xl p-4 transition hover:bg-gray-50">
                                    <p class="font-bold text-gray-800">🔋 โดรน/อุปกรณ์ไฟฟ้า</p>
                                    <p class="text-xs text-gray-500 mt-1">โดรนพ่นยา อุปกรณ์ไฟฟ้า และอากาศยานเกษตร</p>
                                </button>
                            </div>
                        </div>

                        <div class="md:col-span-2">
                            <label class="block text-sm font-medium text-gray-700 mb-2">รูปแบบมิเตอร์ <span class="text-red-500">*</span></label>
                            <input type="hidden" name="tracking_type" id="trackingTypeInput" value="{{ old('tracking_type', 'hours') }}" required>
                            <div class="grid grid-cols-2 gap-3" id="trackingTypeCards">
                                <button type="button" data-tracking="hours"
                                    class="tracking-type-card w-full text-left border-2 rounded-xl p-3 transition hover:bg-gray-50">
                                    <p class="font-bold text-gray-800">⏱️ ชั่วโมง</p>
                                    <p class="text-xs text-gray-500 mt-0.5">เหมาะกับรถไถ/โดรน</p>
                                </button>
                                <button type="button" data-tracking="kilometers"
                                    class="tracking-type-card w-full text-left border-2 rounded-xl p-3 transition hover:bg-gray-50">
                                    <p class="font-bold text-gray-800">🛣️ กิโลเมตร</p>
                                    <p class="text-xs text-gray-500 mt-0.5">เหมาะกับรถวิ่งระยะทาง</p>
                                </button>
                            </div>
                            <p class="text-xs text-gray-400 mt-1.5">เมื่อเลือกกลุ่มโดรน ระบบจะล็อกมิเตอร์เป็นชั่วโมงอัตโนมัติ</p>
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
                            <div id="quickTypeButtons" class="flex flex-wrap gap-2 mt-2">
                                <button type="button" data-type="tractor" class="quick-type-btn px-3 py-1.5 rounded-full text-xs font-bold border border-gray-200 bg-white text-gray-700">รถไถ</button>
                                <button type="button" data-type="harvester" class="quick-type-btn px-3 py-1.5 rounded-full text-xs font-bold border border-gray-200 bg-white text-gray-700">รถเกี่ยว</button>
                                <button type="button" data-type="sprayer" class="quick-type-btn px-3 py-1.5 rounded-full text-xs font-bold border border-gray-200 bg-white text-gray-700">รถพ่นยา</button>
                                <button type="button" data-type="drone" class="quick-type-btn px-3 py-1.5 rounded-full text-xs font-bold border border-gray-200 bg-white text-gray-700">โดรน</button>
                                <button type="button" data-type="other" class="quick-type-btn px-3 py-1.5 rounded-full text-xs font-bold border border-gray-200 bg-white text-gray-700">อื่นๆ</button>
                            </div>
                        </div>

                        <div id="customTypeBlock" class="md:col-span-2 hidden">
                            <label class="block text-sm font-bold text-gray-700 mb-1.5">ระบุประเภทเพิ่มเติม (นวัตกรรมใหม่) <span class="text-red-500">*</span></label>
                            <input type="text" name="custom_type_name" id="customTypeInput" class="w-full px-4 py-2.5 rounded-xl border border-gray-200 focus:ring-2 focus:ring-agri-primary/20 focus:border-agri-primary outline-none transition" value="{{ old('custom_type_name') }}" placeholder="เช่น โดรนหว่านเมล็ดอัตโนมัติ / UGV เกษตร">
                            <p class="text-xs text-gray-400 mt-1.5">ใช้เมื่อประเภทหลักยังไม่ครอบคลุม เพื่อรองรับนวัตกรรมใหม่ในอนาคต</p>
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

                        <div class="md:col-span-2">
                            <label class="block text-sm font-bold text-gray-700 mb-1.5">มิเตอร์เริ่มต้น</label>
                            <div class="relative">
                                <input type="number" name="initial_meter" min="0" step="0.1" class="w-full px-4 py-2.5 rounded-xl border border-gray-200 focus:ring-2 focus:ring-agri-primary/20 focus:border-agri-primary outline-none transition" value="{{ old('initial_meter', 0) }}">
                                <span id="initialMeterUnit" class="absolute right-4 top-1/2 -translate-y-1/2 text-gray-400 text-sm font-bold">ชม.</span>
                            </div>
                            <p class="text-xs text-gray-400 mt-1.5">ใช้เป็นค่ามิเตอร์ปัจจุบันก่อนเริ่มใช้งานคันนี้</p>
                        </div>
                    </div>
                </div>

                {{-- การตั้งค่าซ่อมบำรุงและราคา --}}
                <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6 mt-6">
                    <h3 class="font-bold text-gray-800 text-lg mb-6 flex items-center gap-2 border-b border-gray-100 pb-3">
                        <i class="fa-solid fa-screwdriver-wrench text-orange-500"></i> การตั้งค่าระบบ และ ราคา
                    </h3>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                        
                        {{-- รอบซ่อมบำรุง (ตาม tracking type) --}}
                        <div id="maintenanceHourBlock" class="md:col-span-2 p-4 bg-orange-50 rounded-xl border border-orange-100">
                            <label class="block text-sm font-bold text-orange-800 mb-1.5">รอบซ่อมบำรุง (ชั่วโมง) <span class="text-red-500">*</span></label>
                            <div class="relative">
                                <input type="number" name="maintenance_hour_threshold" id="maintenanceHourInput" class="w-full px-4 py-2.5 rounded-xl border border-orange-200 focus:ring-2 focus:ring-orange-500/20 focus:border-orange-500 outline-none transition" value="{{ old('maintenance_hour_threshold', 100) }}">
                                <span class="absolute right-4 top-1/2 -translate-y-1/2 text-orange-400 text-sm font-bold">ชม.</span>
                            </div>
                            <p class="text-xs text-orange-600/70 mt-1.5"><i class="fa-solid fa-bell"></i> ระบบจะแจ้งเตือนเมื่อใช้งานครบกำหนด</p>
                        </div>

                        <div id="maintenanceKmBlock" class="md:col-span-2 p-4 bg-orange-50 rounded-xl border border-orange-100 hidden">
                            <label class="block text-sm font-bold text-orange-800 mb-1.5">รอบซ่อมบำรุง (กิโลเมตร) <span class="text-red-500">*</span></label>
                            <div class="relative">
                                <input type="number" name="maintenance_km_threshold" id="maintenanceKmInput" class="w-full px-4 py-2.5 rounded-xl border border-orange-200 focus:ring-2 focus:ring-orange-500/20 focus:border-orange-500 outline-none transition" value="{{ old('maintenance_km_threshold', 1000) }}">
                                <span class="absolute right-4 top-1/2 -translate-y-1/2 text-orange-400 text-sm font-bold">กม.</span>
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
        const groupInput = document.getElementById('equipmentGroupInput');
        const trackingTypeInput = document.getElementById('trackingTypeInput');
        const groupCards = document.querySelectorAll('.equipment-group-card');
        const trackingCards = document.querySelectorAll('.tracking-type-card');
        const maintenanceHourBlock = document.getElementById('maintenanceHourBlock');
        const maintenanceKmBlock = document.getElementById('maintenanceKmBlock');
        const maintenanceHourInput = document.getElementById('maintenanceHourInput');
        const maintenanceKmInput = document.getElementById('maintenanceKmInput');
        const initialMeterUnit = document.getElementById('initialMeterUnit');
        const quickTypeButtons = document.querySelectorAll('.quick-type-btn');
        const customTypeBlock = document.getElementById('customTypeBlock');
        const customTypeInput = document.getElementById('customTypeInput');

        const typeSelect = document.getElementById('typeSelect');
        const codeInput = document.getElementById('equipmentCode');
        const prefixes = { 'tractor': 'TR-', 'excavator': 'EX-', 'drone': 'DR-', 'harvester': 'HV-', 'sprayer': 'SP-', 'other': 'OT-' };

        const MACHINE_TYPES = ['tractor', 'excavator', 'harvester', 'sprayer', 'other'];
        const DRONE_TYPES = ['drone', 'other'];

        function setGroup(group) {
            groupInput.value = group;
            groupCards.forEach((card) => {
                const isActive = card.dataset.group === group;
                card.classList.toggle('border-agri-primary', isActive);
                card.classList.toggle('bg-agri-primary/5', isActive);
                card.classList.toggle('border-gray-200', !isActive);
            });
        }

        function setTracking(tracking) {
            trackingTypeInput.value = tracking;
            trackingCards.forEach((card) => {
                const isActive = card.dataset.tracking === tracking;
                card.classList.toggle('border-agri-primary', isActive);
                card.classList.toggle('bg-agri-primary/5', isActive);
                card.classList.toggle('border-gray-200', !isActive);
            });
        }

        function syncTypeOptionsByGroup() {
            const equipmentGroup = groupInput.value;
            const allowTypes = equipmentGroup === 'drone' ? DRONE_TYPES : MACHINE_TYPES;

            Array.from(typeSelect.options).forEach((opt) => {
                if (!opt.value) return;
                opt.hidden = !allowTypes.includes(opt.value);
            });

            if (!allowTypes.includes(typeSelect.value)) {
                typeSelect.value = equipmentGroup === 'drone' ? 'drone' : 'tractor';
            }

            quickTypeButtons.forEach((btn) => {
                const allowed = allowTypes.includes(btn.dataset.type);
                btn.classList.toggle('hidden', !allowed);
                const isActive = allowed && btn.dataset.type === typeSelect.value;
                btn.classList.toggle('bg-agri-primary', isActive);
                btn.classList.toggle('text-white', isActive);
                btn.classList.toggle('border-agri-primary', isActive);
                btn.classList.toggle('bg-white', !isActive);
                btn.classList.toggle('text-gray-700', !isActive);
                btn.classList.toggle('border-gray-200', !isActive);
            });

            const isOtherType = typeSelect.value === 'other';
            customTypeBlock.classList.toggle('hidden', !isOtherType);
            customTypeInput.required = isOtherType;
        }

        function syncMeterUI() {
            const trackingType = trackingTypeInput.value;
            const equipmentGroup = groupInput.value;

            if (equipmentGroup === 'drone' && trackingType !== 'hours') {
                setTracking('hours');
            }

            const isHours = trackingTypeInput.value === 'hours';
            maintenanceHourBlock.classList.toggle('hidden', !isHours);
            maintenanceKmBlock.classList.toggle('hidden', isHours);

            maintenanceHourInput.required = isHours;
            maintenanceKmInput.required = !isHours;

            const kmCard = document.querySelector('[data-tracking="kilometers"]');
            if (kmCard) {
                kmCard.classList.toggle('opacity-50', equipmentGroup === 'drone');
                kmCard.classList.toggle('cursor-not-allowed', equipmentGroup === 'drone');
            }

            initialMeterUnit.textContent = isHours ? 'ชม.' : 'กม.';

            syncTypeOptionsByGroup();
        }

        groupCards.forEach((card) => {
            card.addEventListener('click', function() {
                setGroup(this.dataset.group);
                if (this.dataset.group === 'drone') {
                    setTracking('hours');
                }
                syncMeterUI();
            });
        });

        trackingCards.forEach((card) => {
            card.addEventListener('click', function() {
                if (groupInput.value === 'drone' && this.dataset.tracking === 'kilometers') {
                    return;
                }
                setTracking(this.dataset.tracking);
                syncMeterUI();
            });
        });

        quickTypeButtons.forEach((btn) => {
            btn.addEventListener('click', function() {
                typeSelect.value = this.dataset.type;
                syncTypeOptionsByGroup();
                updateCodeFromType();
            });
        });

        function updateCodeFromType() {
            const prefix = prefixes[typeSelect.value] || 'EQ-';
            const random = Math.floor(Math.random() * 900) + 100;
            codeInput.value = prefix + random;
        }

        typeSelect.addEventListener('change', function() {
            syncTypeOptionsByGroup();
            updateCodeFromType();
        });

        setGroup(groupInput.value || 'machine');
        setTracking(trackingTypeInput.value || 'hours');

        if (!typeSelect.value) {
            typeSelect.value = groupInput.value === 'drone' ? 'drone' : 'tractor';
        }

        if (!codeInput.value) {
            updateCodeFromType();
        }

        if (groupInput.value === 'drone') {
            setTracking('hours');
        }

        syncMeterUI();

    });
</script>
@endsection