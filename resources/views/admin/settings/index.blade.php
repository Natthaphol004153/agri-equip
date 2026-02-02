@extends('layouts.admin')

@section('title', 'ตั้งค่าระบบ')
@section('header', 'Configuration')

@section('content')
<div class="max-w-5xl mx-auto pb-20">

    {{-- 🌟 Hero Header --}}
    <div class="bg-gradient-to-r from-agri-primary to-green-700 rounded-2xl p-8 text-white shadow-lg mb-8 relative overflow-hidden">
        <div class="relative z-10 flex justify-between items-center">
            <div>
                <h2 class="text-3xl font-bold mb-2">ตั้งค่าระบบ (Settings) ⚙️</h2>
                <p class="text-green-100 opacity-90">จัดการข้อมูลบริษัท, การเงิน และการแจ้งเตือนต่างๆ</p>
            </div>
            {{-- Decoration Icon --}}
            <div class="hidden md:block bg-white/20 p-4 rounded-2xl backdrop-blur-sm">
                <i class="fa-solid fa-sliders text-4xl text-white"></i>
            </div>
        </div>
        {{-- Background Shapes --}}
        <div class="absolute top-0 right-0 w-64 h-64 bg-white/5 rounded-full -mr-20 -mt-20 blur-2xl"></div>
        <div class="absolute bottom-0 left-0 w-32 h-32 bg-white/10 rounded-full -ml-10 -mb-10 blur-xl"></div>
    </div>

    {{-- ✅ Flash Message --}}
    @if(session('success'))
        <div x-data="{ show: true }" x-show="show" class="bg-green-50 border-l-4 border-green-500 p-4 mb-6 rounded-r-xl shadow-sm flex items-start gap-3 animate-fade-in-down">
            <div class="text-green-500 mt-0.5"><i class="fa-solid fa-circle-check text-xl"></i></div>
            <div class="flex-1">
                <h3 class="text-sm font-bold text-green-800">บันทึกสำเร็จ!</h3>
                <p class="text-sm text-green-700 mt-1">{{ session('success') }}</p>
            </div>
            <button @click="show = false" class="text-green-400 hover:text-green-600"><i class="fa-solid fa-xmark"></i></button>
        </div>
    @endif

    <form action="{{ route('admin.settings.update') }}" method="POST">
        @csrf

        {{-- 🏢 Section 1: ข้อมูลบริษัท --}}
        <div class="bg-white rounded-2xl shadow-md border border-gray-100 overflow-hidden mb-8 hover:shadow-lg transition-shadow duration-300">
            <div class="bg-gradient-to-r from-blue-50 to-white px-6 py-5 border-b border-blue-100 flex items-center gap-4">
                <div class="w-12 h-12 rounded-xl bg-blue-100 text-blue-600 flex items-center justify-center shadow-inner">
                    <i class="fa-solid fa-building text-xl"></i>
                </div>
                <div>
                    <h3 class="font-bold text-gray-800 text-lg">ข้อมูลบริษัท</h3>
                    <p class="text-xs text-gray-500">ใช้สำหรับแสดงในใบเสร็จและเอกสารต่างๆ</p>
                </div>
            </div>
            
            <div class="p-6 md:p-8 grid grid-cols-1 md:grid-cols-2 gap-x-8 gap-y-6">
                <div class="col-span-2">
                    <label class="block text-sm font-bold text-gray-700 mb-2">ชื่อกิจการ / บริษัท <span class="text-red-500">*</span></label>
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-gray-400">
                            <i class="fa-solid fa-font"></i>
                        </div>
                        <input type="text" name="company_name" value="{{ $settings['company_name'] ?? '' }}" 
                            class="w-full pl-10 border-gray-200 bg-gray-50 rounded-xl focus:bg-white focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 transition-all py-2.5" 
                            placeholder="เช่น บริษัท แอกกริเทค เซอร์วิส จำกัด">
                    </div>
                </div>

                <div>
                    <label class="block text-sm font-bold text-gray-700 mb-2">เบอร์โทรศัพท์ติดต่อ</label>
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-gray-400">
                            <i class="fa-solid fa-phone"></i>
                        </div>
                        <input type="text" name="company_phone" value="{{ $settings['company_phone'] ?? '' }}" 
                            class="w-full pl-10 border-gray-200 bg-gray-50 rounded-xl focus:bg-white focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 transition-all py-2.5"
                            placeholder="0xx-xxx-xxxx">
                    </div>
                </div>

                <div>
                    <label class="block text-sm font-bold text-gray-700 mb-2">เลขประจำตัวผู้เสียภาษี</label>
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-gray-400">
                            <i class="fa-solid fa-id-card"></i>
                        </div>
                        <input type="text" name="company_tax_id" value="{{ $settings['company_tax_id'] ?? '' }}" 
                            class="w-full pl-10 border-gray-200 bg-gray-50 rounded-xl focus:bg-white focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 transition-all py-2.5"
                            placeholder="13 หลัก">
                    </div>
                </div>

                <div class="col-span-2">
                    <label class="block text-sm font-bold text-gray-700 mb-2">ที่อยู่</label>
                    <div class="relative">
                        <div class="absolute top-3 left-3 pointer-events-none text-gray-400">
                            <i class="fa-solid fa-map-location-dot"></i>
                        </div>
                        <textarea name="company_address" rows="3" 
                            class="w-full pl-10 border-gray-200 bg-gray-50 rounded-xl focus:bg-white focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 transition-all py-2.5"
                            placeholder="ที่อยู่บริษัท...">{{ $settings['company_address'] ?? '' }}</textarea>
                    </div>
                </div>
            </div>
        </div>

       

        {{-- 💾 Floating Save Button --}}
        <div class="fixed bottom-6 right-6 md:bottom-10 md:right-10 z-30">
            <button type="submit" class="bg-gradient-to-r from-agri-primary to-green-700 text-white font-bold py-4 px-8 rounded-full shadow-2xl hover:scale-105 hover:shadow-green-500/30 transition-all flex items-center gap-3 border-4 border-white/20 backdrop-blur-md">
                <i class="fa-solid fa-save text-xl"></i> 
                <span class="text-lg">บันทึกการตั้งค่า</span>
            </button>
        </div>

    </form>
</div>
@endsection