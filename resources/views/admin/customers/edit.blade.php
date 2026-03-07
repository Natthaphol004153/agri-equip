@extends('layouts.admin')

@section('title', 'Edit Customer')
@section('header', 'แก้ไขข้อมูลลูกค้า')

@section('content')
<div class="max-w-4xl mx-auto">
    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
        
        <div class="bg-yellow-500 px-6 py-4 flex justify-between items-center">
            <h2 class="text-white font-bold text-lg flex items-center gap-2">
                <i class="fa-solid fa-user-pen text-white/90"></i> แก้ไขข้อมูลลูกค้า
            </h2>
            <a href="{{ route('admin.customers.index') }}" class="text-white/80 hover:text-white transition">
                <i class="fa-solid fa-xmark text-lg"></i>
            </a>
        </div>

        <form action="{{ route('admin.customers.update', $customer->id) }}" method="POST" enctype="multipart/form-data" class="p-6 md:p-8">
            @csrf
            @method('PUT')

            <div class="mb-8">
                <h3 class="text-gray-800 font-bold text-lg mb-4 flex items-center gap-2 pb-2 border-b border-gray-100">
                    <span class="w-8 h-8 rounded-lg bg-blue-50 text-blue-600 flex items-center justify-center text-sm">
                        <i class="fa-regular fa-id-card"></i>
                    </span>
                    ข้อมูลทั่วไป
                </h3>

                <div class="flex flex-col md:flex-row gap-6 mb-6 items-center md:items-start">
                    <div class="shrink-0">
                        <div class="w-24 h-24 rounded-full bg-gray-100 border-4 border-white shadow-md flex items-center justify-center overflow-hidden relative group">
                            <img id="image-preview" src="{{ $customer->profile_image ? asset('storage/' . $customer->profile_image) : '#' }}" alt="Preview" class="w-full h-full object-cover {{ $customer->profile_image ? '' : 'hidden' }}">
                            <div class="absolute inset-0 flex items-center justify-center bg-gray-200 text-gray-400 {{ $customer->profile_image ? 'hidden' : '' }}" id="placeholder-icon">
                                <i class="fa-solid fa-camera text-3xl"></i>
                            </div>
                        </div>
                    </div>
                    <div class="flex-1 w-full">
                        <label class="block text-sm font-semibold text-gray-700 mb-2">เปลี่ยนรูปโปรไฟล์</label>
                        <input type="file" name="profile_image" accept="image/*" onchange="previewImage(this)"
                               class="block w-full text-sm text-slate-500 file:mr-4 file:py-2 file:px-4 file:rounded-xl file:border-0 file:text-sm file:font-semibold file:bg-yellow-50 file:text-yellow-700 hover:file:bg-yellow-100 cursor-pointer border border-gray-200 rounded-xl">
                    </div>
                </div>
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">รหัสลูกค้า</label>
                        <input type="text" value="{{ $customer->customer_code }}" disabled class="block w-full px-4 py-2.5 bg-gray-50 border border-gray-200 rounded-xl text-gray-600 font-bold text-sm cursor-not-allowed">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">ประเภทลูกค้า *</label>
                        <select name="customer_type" class="block w-full border-gray-300 rounded-xl shadow-sm focus:ring-yellow-500 focus:border-yellow-500 sm:text-sm py-2.5">
                            <option value="individual" {{ $customer->customer_type == 'individual' ? 'selected' : '' }}>บุคคลธรรมดา</option>
                            <option value="farm" {{ $customer->customer_type == 'farm' ? 'selected' : '' }}>ฟาร์มเกษตร</option>
                            <option value="company" {{ $customer->customer_type == 'company' ? 'selected' : '' }}>บริษัท/นิติบุคคล</option>
                        </select>
                    </div>
                    <div class="md:col-span-2">
                        <label class="block text-sm font-medium text-gray-700 mb-1">ชื่อลูกค้า / ชื่อฟาร์ม *</label>
                        <input type="text" name="name" value="{{ old('name', $customer->name) }}" required class="block w-full border-gray-300 rounded-xl shadow-sm focus:ring-yellow-500 focus:border-yellow-500 sm:text-sm py-2.5">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">เลขประจำตัวผู้เสียภาษี</label>
                        <input type="text" name="tax_id" value="{{ old('tax_id', $customer->tax_id) }}" class="block w-full border-gray-300 rounded-xl shadow-sm focus:ring-yellow-500 focus:border-yellow-500 sm:text-sm py-2.5">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">เบอร์โทรศัพท์ *</label>
                        <input type="text" name="phone" value="{{ old('phone', $customer->phone) }}" required class="block w-full border-gray-300 rounded-xl shadow-sm focus:ring-yellow-500 focus:border-yellow-500 sm:text-sm py-2.5">
                    </div>
                    <div class="md:col-span-2 p-5 bg-yellow-50/50 rounded-2xl border border-yellow-100">
                        <label class="block text-sm font-bold text-yellow-800 mb-2">พื้นที่เพาะปลูกรวม (ไร่)</label>
                        <input type="number" name="farm_area" step="0.1" min="0" value="{{ old('farm_area', $customer->farm_area) }}" class="block w-full md:w-1/2 border-yellow-300 rounded-xl shadow-sm focus:ring-yellow-500 focus:border-yellow-500 font-medium sm:text-sm py-2.5">
                    </div>
                </div>
            </div>

            <div class="mb-8">
                <h3 class="text-gray-800 font-bold text-lg mb-4 flex items-center gap-2 pb-2 border-b border-gray-100">
                    <span class="w-8 h-8 rounded-lg bg-green-50 text-green-600 flex items-center justify-center text-sm">
                        <i class="fa-solid fa-map-location-dot"></i>
                    </span>
                    ข้อมูลที่อยู่และแผนที่
                </h3>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div class="md:col-span-2">
                        <label class="block text-sm font-medium text-gray-700 mb-1">ที่อยู่ (เลขที่, หมู่, ถนน) *</label>
                        <textarea name="address" rows="2" required class="block w-full border-gray-300 rounded-xl shadow-sm focus:ring-yellow-500 focus:border-yellow-500 sm:text-sm p-3">{{ old('address', $customer->address) }}</textarea>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">ตำบล/แขวง</label>
                        <input type="text" id="district" class="block w-full border-gray-300 rounded-xl shadow-sm focus:ring-yellow-500 focus:border-yellow-500 sm:text-sm py-2.5">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">อำเภอ/เขต</label>
                        <input type="text" id="amphure" name="district" value="{{ old('district', $customer->district) }}" class="block w-full border-gray-300 rounded-xl shadow-sm focus:ring-yellow-500 focus:border-yellow-500 sm:text-sm py-2.5">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">จังหวัด</label>
                        <input type="text" id="province" name="province" value="{{ old('province', $customer->province) }}" class="block w-full border-gray-300 rounded-xl shadow-sm focus:ring-yellow-500 focus:border-yellow-500 sm:text-sm py-2.5">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">รหัสไปรษณีย์</label>
                        <input type="text" id="zipcode" name="postal_code" value="{{ old('postal_code', $customer->postal_code) }}" class="block w-full border-gray-300 rounded-xl shadow-sm focus:ring-yellow-500 focus:border-yellow-500 sm:text-sm py-2.5">
                    </div>

                    {{-- ✅ ส่วนพิกัด Latitude / Longitude --}}
                    <div class="md:col-span-2 mt-2 p-5 bg-gray-50 rounded-2xl border border-gray-200">
                        <label class="block text-sm font-bold text-gray-700 mb-3"><i class="fa-solid fa-map-pin text-red-500 mr-1"></i> พิกัดแผนที่ (Google Maps)</label>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <label class="block text-xs font-medium text-gray-500 mb-1">ละติจูด (Latitude)</label>
                                <input type="text" name="latitude" value="{{ old('latitude', $customer->latitude) }}" class="block w-full border-gray-300 rounded-xl shadow-sm focus:ring-yellow-500 focus:border-yellow-500 sm:text-sm py-2.5" placeholder="ตัวอย่าง: 14.038481">
                            </div>
                            <div>
                                <label class="block text-xs font-medium text-gray-500 mb-1">ลองจิจูด (Longitude)</label>
                                <input type="text" name="longitude" value="{{ old('longitude', $customer->longitude) }}" class="block w-full border-gray-300 rounded-xl shadow-sm focus:ring-yellow-500 focus:border-yellow-500 sm:text-sm py-2.5" placeholder="ตัวอย่าง: 100.728956">
                            </div>
                        </div>
                    </div>

                    <div class="md:col-span-2 mt-2 p-5 bg-blue-50 rounded-2xl border border-blue-200">
                        <label class="block text-sm font-bold text-blue-800 mb-3"><i class="fa-solid fa-location-crosshairs mr-1"></i> สถานที่ปฏิบัติงาน (แยกจากที่อยู่ลูกค้า)</label>
                        <div class="mb-4">
                            <label class="block text-xs font-medium text-blue-700 mb-1">ลิงก์ Google Maps ของหน้างาน</label>
                            <input type="url" name="work_map_url" value="{{ old('work_map_url', $customer->work_map_url) }}" class="block w-full border-blue-300 rounded-xl shadow-sm focus:ring-blue-500 focus:border-blue-500 sm:text-sm py-2.5 px-3" placeholder="https://maps.app.goo.gl/... หรือ https://www.google.com/maps?q=...">
                        </div>
                        <div class="mb-4">
                            <label class="block text-xs font-medium text-blue-700 mb-1">คำอธิบายสถานที่/จุดนัดหมาย</label>
                            <textarea name="work_location_address" rows="2" class="block w-full border-blue-300 rounded-xl shadow-sm focus:ring-blue-500 focus:border-blue-500 sm:text-sm p-3">{{ old('work_location_address', $customer->work_location_address) }}</textarea>
                        </div>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <label class="block text-xs font-medium text-blue-700 mb-1">ละติจูดหน้างาน (LAT)</label>
                                <input type="text" id="work_latitude" name="work_latitude" value="{{ old('work_latitude', $customer->work_latitude) }}" class="block w-full border-blue-300 rounded-xl shadow-sm focus:ring-blue-500 focus:border-blue-500 sm:text-sm py-2.5" placeholder="ตัวอย่าง: 14.038481">
                            </div>
                            <div>
                                <label class="block text-xs font-medium text-blue-700 mb-1">ลองจิจูดหน้างาน (LNG)</label>
                                <input type="text" id="work_longitude" name="work_longitude" value="{{ old('work_longitude', $customer->work_longitude) }}" class="block w-full border-blue-300 rounded-xl shadow-sm focus:ring-blue-500 focus:border-blue-500 sm:text-sm py-2.5" placeholder="ตัวอย่าง: 100.728956">
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="flex items-center justify-end gap-3 pt-4 border-t border-gray-100">
                <a href="{{ route('admin.customers.index') }}" class="px-5 py-2.5 rounded-xl border border-gray-200 text-gray-600 font-medium hover:bg-gray-50 transition">ยกเลิก</a>
                <button type="submit" class="px-6 py-2.5 rounded-xl bg-yellow-500 text-white font-bold shadow-lg hover:bg-yellow-600 hover:-translate-y-0.5 transition duration-200 flex items-center gap-2">
                    <i class="fa-solid fa-save"></i> บันทึกการแก้ไข
                </button>
            </div>
        </form>
    </div>
</div>

@push('scripts')
<link rel="stylesheet" href="{{ asset('vendor/jquery.thailand/jquery.Thailand.min.css') }}">
<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
<script src="{{ asset('vendor/jquery.thailand/JQL.min.js') }}"></script>
<script src="{{ asset('vendor/jquery.thailand/typeahead.bundle.js') }}"></script>
<script src="{{ asset('vendor/jquery.thailand/jquery.Thailand.min.js') }}"></script>

<script>
    $.Thailand({
        database: '{{ asset("vendor/jquery.thailand/db.json") }}', 
        $district: $('#district'),
        $amphoe: $('#amphure'),
        $province: $('#province'),
        $zipcode: $('#zipcode'),
    });

    function previewImage(input) {
        const preview = document.getElementById('image-preview');
        const placeholder = document.getElementById('placeholder-icon');
        if (input.files && input.files[0]) {
            const reader = new FileReader();
            reader.onload = function(e) {
                preview.src = e.target.result;
                preview.classList.remove('hidden');
                placeholder.classList.add('hidden');
            }
            reader.readAsDataURL(input.files[0]);
        }
    }

</script>
@endpush
@endsection