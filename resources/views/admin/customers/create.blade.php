@extends('layouts.admin')

@section('title', 'Create Customer')
@section('header', 'เพิ่มลูกค้าใหม่')

@section('content')
<div class="max-w-5xl mx-auto py-6">
    
    {{-- Main Card --}}
    <div class="bg-white rounded-3xl shadow-xl border border-gray-100 overflow-hidden">
        
        {{-- Header Section --}}
        <div class="bg-gradient-to-r from-agri-primary to-green-600 px-8 py-6 flex justify-between items-center relative overflow-hidden">
            <div class="absolute top-0 right-0 w-64 h-64 bg-white/10 rounded-full blur-3xl -mr-16 -mt-16 pointer-events-none"></div>
            
            <div class="relative z-10 flex items-center gap-4">
                <div class="w-12 h-12 bg-white/20 backdrop-blur-sm rounded-2xl flex items-center justify-center text-white shadow-inner">
                    <i class="fa-solid fa-user-plus text-2xl"></i>
                </div>
                <div>
                    <h2 class="text-white font-bold text-xl tracking-wide">แบบฟอร์มลงทะเบียนลูกค้า</h2>
                    <p class="text-green-100 text-sm mt-0.5">กรอกข้อมูลเพื่อสร้างบัญชีลูกค้าใหม่ในระบบ</p>
                </div>
            </div>

            <a href="{{ route('admin.customers.index') }}" 
               class="relative z-10 w-10 h-10 flex items-center justify-center bg-white/10 hover:bg-white/20 rounded-full text-white transition duration-200 group">
                <i class="fa-solid fa-xmark text-lg group-hover:scale-110 transition-transform"></i>
            </a>
        </div>

        {{-- Form Content: ต้องมี enctype="multipart/form-data" --}}
        <form action="{{ route('admin.customers.store') }}" method="POST" enctype="multipart/form-data" class="p-8 md:p-10">
            @csrf

            {{-- ================= Section 1: ข้อมูลทั่วไป ================= --}}
            <div class="mb-10">
                <div class="flex items-center gap-3 mb-6 border-b border-gray-100 pb-4">
                    <div class="w-10 h-10 rounded-xl bg-blue-50 text-blue-600 flex items-center justify-center shadow-sm">
                        <i class="fa-regular fa-id-card text-lg"></i>
                    </div>
                    <div>
                        <h3 class="text-gray-800 font-bold text-lg">ข้อมูลทั่วไป</h3>
                        <p class="text-gray-400 text-xs">รายละเอียดพื้นฐานและข้อมูลติดต่อ</p>
                    </div>
                </div>

                {{-- ✅ ส่วนอัปโหลดรูปภาพ --}}
                <div class="flex flex-col md:flex-row gap-6 mb-8 items-start">
                    <div class="shrink-0 mx-auto md:mx-0">
                        <div class="w-32 h-32 rounded-full bg-gray-100 border-4 border-white shadow-md flex items-center justify-center overflow-hidden relative group">
                            <img id="image-preview" src="#" alt="Preview" class="w-full h-full object-cover hidden">
                            <div class="absolute inset-0 flex items-center justify-center bg-gray-200 text-gray-400" id="placeholder-icon">
                                <i class="fa-solid fa-camera text-4xl"></i>
                            </div>
                        </div>
                    </div>
                    <div class="flex-1 w-full">
                        <label class="block text-sm font-semibold text-gray-700 mb-2">รูปโปรไฟล์ (ถ้ามี)</label>
                        <input type="file" name="profile_image" accept="image/*" onchange="previewImage(this)"
                               class="block w-full text-sm text-slate-500
                                      file:mr-4 file:py-2.5 file:px-4
                                      file:rounded-xl file:border-0
                                      file:text-sm file:font-semibold
                                      file:bg-blue-50 file:text-blue-700
                                      hover:file:bg-blue-100 cursor-pointer border border-gray-200 rounded-xl">
                        <p class="mt-2 text-xs text-gray-400">รองรับไฟล์ JPG, PNG, JPEG ขนาดไม่เกิน 5MB</p>
                    </div>
                </div>
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-x-8 gap-y-6">
                    {{-- รหัสลูกค้า (Auto) --}}
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-2 ml-1">รหัสลูกค้า</label>
                        <div class="relative group">
                            <input type="text" value="Auto Generate (ระบบสร้างอัตโนมัติ)" disabled 
                                   class="block w-full pl-11 pr-4 py-3 bg-gray-50 border border-gray-200 rounded-xl text-gray-500 text-sm font-medium italic cursor-not-allowed select-none">
                            <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none">
                                <i class="fa-solid fa-barcode text-gray-400 group-hover:text-gray-500 transition-colors"></i>
                            </div>
                        </div>
                    </div>

                    {{-- ประเภทลูกค้า --}}
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-2 ml-1">ประเภทลูกค้า <span class="text-red-500">*</span></label>
                        <div class="relative">
                            <select name="customer_type" class="block w-full pl-11 pr-10 py-3 border-gray-300 rounded-xl shadow-sm focus:ring-2 focus:ring-agri-primary/20 focus:border-agri-primary text-sm transition duration-200 appearance-none bg-white">
                                <option value="individual">บุคคลธรรมดา</option>
                                <option value="farm">ฟาร์มเกษตร</option>
                                <option value="company">บริษัท/นิติบุคคล</option>
                            </select>
                            <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none">
                                <i class="fa-solid fa-user-tag text-gray-400"></i>
                            </div>
                            <div class="absolute inset-y-0 right-0 pr-3 flex items-center pointer-events-none">
                                <i class="fa-solid fa-chevron-down text-gray-400 text-xs"></i>
                            </div>
                        </div>
                    </div>

                    {{-- ชื่อลูกค้า --}}
                    <div class="md:col-span-2">
                        <label class="block text-sm font-semibold text-gray-700 mb-2 ml-1">ชื่อลูกค้า / ชื่อฟาร์ม <span class="text-red-500">*</span></label>
                        <div class="relative">
                            <input type="text" name="name" required 
                                   class="block w-full pl-11 pr-4 py-3 border-gray-300 rounded-xl shadow-sm focus:ring-2 focus:ring-agri-primary/20 focus:border-agri-primary text-sm transition duration-200 placeholder-gray-300"
                                   placeholder="เช่น นายสมชาย ใจดี หรือ ไร่อ้อยสุขใจ">
                            <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none">
                                <i class="fa-regular fa-user text-gray-400"></i>
                            </div>
                        </div>
                    </div>

                    {{-- เลขผู้เสียภาษี --}}
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-2 ml-1">เลขประจำตัวผู้เสียภาษี</label>
                        <div class="relative">
                            <input type="text" name="tax_id" 
                                   class="block w-full pl-11 pr-4 py-3 border-gray-300 rounded-xl shadow-sm focus:ring-2 focus:ring-agri-primary/20 focus:border-agri-primary text-sm transition duration-200"
                                   placeholder="ถ้ามี">
                            <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none">
                                <i class="fa-solid fa-file-invoice-dollar text-gray-400"></i>
                            </div>
                        </div>
                    </div>

                    {{-- เบอร์โทรศัพท์ --}}
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-2 ml-1">เบอร์โทรศัพท์ <span class="text-red-500">*</span></label>
                        <div class="relative">
                            <input type="text" name="phone" required 
                                   class="block w-full pl-11 pr-4 py-3 border-gray-300 rounded-xl shadow-sm focus:ring-2 focus:ring-agri-primary/20 focus:border-agri-primary text-sm transition duration-200"
                                   placeholder="08X-XXX-XXXX">
                            <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none">
                                <i class="fa-solid fa-phone text-gray-400"></i>
                            </div>
                        </div>
                        <p class="text-xs text-gray-400 mt-1 ml-1">* ใช้เป็น Username และ Password เริ่มต้น (4 ตัวท้าย)</p>
                    </div>
                </div>
            </div>

            {{-- ================= Section 2: ที่อยู่ ================= --}}
            <div class="mb-8">
                <div class="flex items-center gap-3 mb-6 border-b border-gray-100 pb-4">
                    <div class="w-10 h-10 rounded-xl bg-green-50 text-green-600 flex items-center justify-center shadow-sm">
                        <i class="fa-solid fa-map-location-dot text-lg"></i>
                    </div>
                    <div>
                        <h3 class="text-gray-800 font-bold text-lg">ข้อมูลที่อยู่</h3>
                        <p class="text-gray-400 text-xs">สำหรับออกใบเสร็จและติดต่อประสานงาน</p>
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-x-8 gap-y-6">
                    <div class="md:col-span-2">
                        <label class="block text-sm font-semibold text-gray-700 mb-2 ml-1">ที่อยู่ (เลขที่, หมู่, ถนน)</label>
                        <div class="relative">
                            <textarea name="address" rows="2" 
                                      class="block w-full pl-11 pr-4 py-3 border-gray-300 rounded-xl shadow-sm focus:ring-2 focus:ring-agri-primary/20 focus:border-agri-primary text-sm transition duration-200 resize-none"
                                      placeholder="บ้านเลขที่, หมู่บ้าน, ซอย, ถนน..."></textarea>
                            <div class="absolute top-3.5 left-3.5 pointer-events-none">
                                <i class="fa-solid fa-house text-gray-400"></i>
                            </div>
                        </div>
                    </div>
                    
                    {{-- ตำบล --}}
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-2 ml-1">ตำบล/แขวง</label>
                        <div class="relative">
                            <input type="text" id="district" 
                                   class="block w-full pl-11 pr-4 py-3 border-gray-300 rounded-xl shadow-sm focus:ring-2 focus:ring-agri-primary/20 focus:border-agri-primary text-sm transition duration-200 bg-white">
                            <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none">
                                <i class="fa-solid fa-map-pin text-gray-400"></i>
                            </div>
                        </div>
                    </div>

                    {{-- อำเภอ --}}
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-2 ml-1">อำเภอ/เขต</label>
                        <div class="relative">
                            <input type="text" id="amphure" name="district" 
                                   class="block w-full pl-11 pr-4 py-3 border-gray-300 rounded-xl shadow-sm focus:ring-2 focus:ring-agri-primary/20 focus:border-agri-primary text-sm transition duration-200 bg-white">
                            <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none">
                                <i class="fa-solid fa-city text-gray-400"></i>
                            </div>
                        </div>
                    </div>

                    {{-- จังหวัด --}}
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-2 ml-1">จังหวัด</label>
                        <div class="relative">
                            <input type="text" id="province" name="province" 
                                   class="block w-full pl-11 pr-4 py-3 border-gray-300 rounded-xl shadow-sm focus:ring-2 focus:ring-agri-primary/20 focus:border-agri-primary text-sm transition duration-200 bg-white">
                            <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none">
                                <i class="fa-solid fa-tree-city text-gray-400"></i>
                            </div>
                        </div>
                    </div>

                    {{-- รหัสไปรษณีย์ --}}
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-2 ml-1">รหัสไปรษณีย์</label>
                        <div class="relative">
                            <input type="text" id="zipcode" name="postal_code" 
                                   class="block w-full pl-11 pr-4 py-3 border-gray-300 rounded-xl shadow-sm focus:ring-2 focus:ring-agri-primary/20 focus:border-agri-primary text-sm transition duration-200 bg-white">
                            <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none">
                                <i class="fa-solid fa-envelope text-gray-400"></i>
                            </div>
                        </div>
                    </div>
                    
                    {{-- อีเมล --}}
                    <div class="md:col-span-2 mt-2">
                        <label class="block text-sm font-semibold text-gray-700 mb-2 ml-1">อีเมลติดต่อ (ถ้ามี)</label>
                        <div class="relative">
                            <input type="email" name="email" 
                                   class="block w-full pl-11 pr-4 py-3 border-gray-300 rounded-xl shadow-sm focus:ring-2 focus:ring-agri-primary/20 focus:border-agri-primary text-sm transition duration-200"
                                   placeholder="example@email.com">
                            <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none">
                                <i class="fa-solid fa-at text-gray-400"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- ================= Action Buttons ================= --}}
            <div class="flex items-center justify-end gap-4 pt-8 border-t border-gray-100 bg-gray-50/50 -mx-8 -mb-10 p-8">
                <a href="{{ route('admin.customers.index') }}" 
                   class="px-6 py-3 rounded-xl border border-gray-200 text-gray-600 font-semibold hover:bg-white hover:border-gray-300 hover:shadow-sm transition duration-200">
                    <i class="fa-solid fa-arrow-left mr-2"></i> ยกเลิก
                </a>
                <button type="submit" 
                        class="px-8 py-3 rounded-xl bg-gradient-to-r from-agri-primary to-green-600 text-white font-bold shadow-lg shadow-green-200 hover:shadow-green-300 hover:-translate-y-0.5 transition duration-200 flex items-center gap-2 group">
                    <span class="group-hover:scale-110 transition-transform"><i class="fa-solid fa-save"></i></span> 
                    บันทึกข้อมูลลูกค้า
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
    // Thai Address Auto Complete
    $.Thailand({
        database: '{{ asset("vendor/jquery.thailand/db.json") }}', 
        $district: $('#district'),
        $amphoe: $('#amphure'),
        $province: $('#province'),
        $zipcode: $('#zipcode'),
    });

    // Image Preview Script
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