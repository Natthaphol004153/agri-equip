@extends('layouts.admin')

@section('title', 'Edit Customer')
@section('header', 'แก้ไขข้อมูลลูกค้า')

@section('content')
<div class="max-w-4xl mx-auto">
    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
        
        {{-- Header --}}
        <div class="bg-yellow-500 px-6 py-4 flex justify-between items-center">
            <h2 class="text-white font-bold text-lg flex items-center gap-2">
                <i class="fa-solid fa-user-pen text-white/90"></i> แก้ไขข้อมูลลูกค้า
            </h2>
            <a href="{{ route('admin.customers.index') }}" class="text-white/80 hover:text-white transition">
                <i class="fa-solid fa-xmark text-lg"></i>
            </a>
        </div>

        <form action="{{ route('admin.customers.update', $customer->id) }}" method="POST" class="p-6 md:p-8">
            @csrf
            @method('PUT')

            {{-- Section 1: ข้อมูลทั่วไป --}}
            <div class="mb-8">
                <h3 class="text-gray-800 font-bold text-lg mb-4 flex items-center gap-2 pb-2 border-b border-gray-100">
                    <span class="w-8 h-8 rounded-lg bg-blue-50 text-blue-600 flex items-center justify-center text-sm">
                        <i class="fa-regular fa-id-card"></i>
                    </span>
                    ข้อมูลทั่วไป
                </h3>
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    {{-- รหัสลูกค้า (แก้ไขไม่ได้) --}}
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">รหัสลูกค้า</label>
                        <div class="relative">
                            <input type="text" value="{{ $customer->customer_code }}" disabled 
                                   class="block w-full pl-10 pr-3 py-2.5 bg-gray-50 border border-gray-200 rounded-xl text-gray-600 font-bold text-sm">
                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                <i class="fa-solid fa-barcode text-gray-400"></i>
                            </div>
                        </div>
                    </div>

                    {{-- ประเภทลูกค้า --}}
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">ประเภทลูกค้า *</label>
                        <select name="customer_type" class="block w-full border-gray-300 rounded-xl shadow-sm focus:ring-yellow-500 focus:border-yellow-500 sm:text-sm py-2.5">
                            <option value="individual" {{ $customer->customer_type == 'individual' ? 'selected' : '' }}>บุคคลธรรมดา</option>
                            <option value="farm" {{ $customer->customer_type == 'farm' ? 'selected' : '' }}>ฟาร์มเกษตร</option>
                            <option value="company" {{ $customer->customer_type == 'company' ? 'selected' : '' }}>บริษัท/นิติบุคคล</option>
                        </select>
                    </div>

                    {{-- ชื่อลูกค้า --}}
                    <div class="md:col-span-2">
                        <label class="block text-sm font-medium text-gray-700 mb-1">ชื่อลูกค้า / ชื่อฟาร์ม *</label>
                        <input type="text" name="name" value="{{ old('name', $customer->name) }}" required 
                               class="block w-full border-gray-300 rounded-xl shadow-sm focus:ring-yellow-500 focus:border-yellow-500 sm:text-sm py-2.5">
                    </div>

                    {{-- เลขผู้เสียภาษี --}}
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">เลขประจำตัวผู้เสียภาษี (ถ้ามี)</label>
                        <input type="text" name="tax_id" value="{{ old('tax_id', $customer->tax_id) }}" 
                               class="block w-full border-gray-300 rounded-xl shadow-sm focus:ring-yellow-500 focus:border-yellow-500 sm:text-sm py-2.5">
                    </div>

                    {{-- เบอร์โทรศัพท์ --}}
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">เบอร์โทรศัพท์ *</label>
                        <div class="relative">
                            <input type="text" name="phone" value="{{ old('phone', $customer->phone) }}" required 
                                   class="block w-full pl-10 border-gray-300 rounded-xl shadow-sm focus:ring-yellow-500 focus:border-yellow-500 sm:text-sm py-2.5">
                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                <i class="fa-solid fa-phone text-gray-400 text-xs"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Section 2: ที่อยู่ --}}
            <div class="mb-8">
                <h3 class="text-gray-800 font-bold text-lg mb-4 flex items-center gap-2 pb-2 border-b border-gray-100">
                    <span class="w-8 h-8 rounded-lg bg-green-50 text-green-600 flex items-center justify-center text-sm">
                        <i class="fa-solid fa-map-location-dot"></i>
                    </span>
                    ข้อมูลที่อยู่
                </h3>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div class="md:col-span-2">
                        <label class="block text-sm font-medium text-gray-700 mb-1">ที่อยู่ (เลขที่, หมู่, ถนน)</label>
                        <textarea name="address" rows="2" class="block w-full border-gray-300 rounded-xl shadow-sm focus:ring-yellow-500 focus:border-yellow-500 sm:text-sm p-3">{{ old('address', $customer->address) }}</textarea>
                    </div>
                    
                    {{-- ✅ เพิ่มช่องตำบล และกำหนด ID --}}
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">ตำบล/แขวง</label>
                        <input type="text" id="district" class="block w-full border-gray-300 rounded-xl shadow-sm focus:ring-yellow-500 focus:border-yellow-500 sm:text-sm py-2.5">
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">อำเภอ/เขต</label>
                        <input type="text" id="amphure" name="district" value="{{ old('district', $customer->district) }}" 
                               class="block w-full border-gray-300 rounded-xl shadow-sm focus:ring-yellow-500 focus:border-yellow-500 sm:text-sm py-2.5">
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">จังหวัด</label>
                        <input type="text" id="province" name="province" value="{{ old('province', $customer->province) }}" 
                               class="block w-full border-gray-300 rounded-xl shadow-sm focus:ring-yellow-500 focus:border-yellow-500 sm:text-sm py-2.5">
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">รหัสไปรษณีย์</label>
                        <input type="text" id="zipcode" name="postal_code" value="{{ old('postal_code', $customer->postal_code) }}" 
                               class="block w-full border-gray-300 rounded-xl shadow-sm focus:ring-yellow-500 focus:border-yellow-500 sm:text-sm py-2.5">
                    </div>
                    
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">อีเมลติดต่อ (ถ้ามี)</label>
                        <input type="email" name="email" value="{{ old('email', $customer->email) }}" 
                               class="block w-full border-gray-300 rounded-xl shadow-sm focus:ring-yellow-500 focus:border-yellow-500 sm:text-sm py-2.5">
                    </div>
                </div>
            </div>

            {{-- Action Buttons --}}
            <div class="flex items-center justify-end gap-3 pt-4 border-t border-gray-100">
                <a href="{{ route('admin.customers.index') }}" class="px-5 py-2.5 rounded-xl border border-gray-200 text-gray-600 font-medium hover:bg-gray-50 transition">
                    ยกเลิก
                </a>
                <button type="submit" class="px-6 py-2.5 rounded-xl bg-yellow-500 text-white font-bold shadow-lg hover:bg-yellow-600 hover:-translate-y-0.5 transition duration-200 flex items-center gap-2">
                    <i class="fa-solid fa-save"></i> บันทึกการแก้ไข
                </button>
            </div>

        </form>
    </div>
</div>

@push('scripts')
{{-- ✅ เรียกใช้ Thai Address Auto Complete --}}
<link rel="stylesheet" href="{{ asset('vendor/jquery.thailand/jquery.Thailand.min.css') }}">
<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
<script src="{{ asset('vendor/jquery.thailand/JQL.min.js') }}"></script>
<script src="{{ asset('vendor/jquery.thailand/typeahead.bundle.js') }}"></script>
<script src="{{ asset('vendor/jquery.thailand/jquery.Thailand.min.js') }}"></script>

<script>
    $.Thailand({
        database: '{{ asset("vendor/jquery.thailand/db.json") }}', 
        $district: $('#district'), // ตำบล
        $amphoe: $('#amphure'),    // อำเภอ
        $province: $('#province'), // จังหวัด
        $zipcode: $('#zipcode'),   // รหัสไปรษณีย์
    });
</script>
@endpush

@endsection