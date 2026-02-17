@extends('layouts.admin')

@section('content')
<div class="container mx-auto px-4 py-6">
    <h1 class="text-2xl font-bold mb-6 text-gray-800">⚙️ ตั้งค่าระบบ</h1>

    <form action="{{ route('admin.settings.update') }}" method="POST" enctype="multipart/form-data">
        @csrf
        
        <div class="bg-white rounded-lg shadow-md p-6 mb-6">
            <h2 class="text-xl font-bold mb-4 text-gray-700 border-b pb-2">
                <i class="fa-solid fa-images mr-2"></i> รูปภาพสไลด์หน้าแรก (Banner)
            </h2>
            
            {{-- 1. แสดงรูปภาพที่มีอยู่ --}}
            @php
                $banners = json_decode($settings['home_banners'] ?? '[]', true);
            @endphp

            @if(count($banners) > 0)
                <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-6">
                    @foreach($banners as $banner)
                        <div class="relative group border rounded-lg overflow-hidden">
                            <img src="{{ asset('storage/' . $banner) }}" class="w-full h-32 object-cover">
                            
                            {{-- Checkbox สำหรับลบ (ซ่อนไว้แล้วใช้ label ครอบ หรือแสดงตรงๆ) --}}
                            <div class="absolute top-2 right-2">
                                <label class="bg-red-500 text-white text-xs px-2 py-1 rounded cursor-pointer hover:bg-red-600 shadow">
                                    <input type="checkbox" name="delete_banners[]" value="{{ $banner }}" class="mr-1">
                                    ลบรูปนี้
                                </label>
                            </div>
                        </div>
                    @endforeach
                </div>
            @else
                <p class="text-gray-400 mb-4 text-sm italic">ยังไม่มีรูปภาพสไลด์ ใช้รูปภาพเริ่มต้นของระบบอยู่</p>
            @endif

            {{-- 2. อัปโหลดรูปใหม่ --}}
            <div class="border-2 border-dashed border-gray-300 rounded-lg p-6 text-center hover:bg-gray-50 transition">
                <label class="cursor-pointer block">
                    <i class="fa-solid fa-cloud-arrow-up text-4xl text-gray-400 mb-2"></i>
                    <p class="text-gray-600 font-semibold">คลิกเพื่อเพิ่มรูปภาพสไลด์</p>
                    <p class="text-xs text-gray-400 mt-1">(รองรับ .jpg, .png ขนาดไม่เกิน 5MB, เลือกได้หลายรูปพร้อมกัน)</p>
                    <input type="file" name="banner_images[]" multiple accept="image/*" class="hidden" onchange="previewFiles(this)">
                </label>
                {{-- แสดงชื่อไฟล์ที่เลือก --}}
                <div id="file-list" class="mt-4 text-sm text-green-600"></div>
            </div>
        </div>

        <div class="flex justify-end">
            <button type="submit" class="bg-green-600 hover:bg-green-700 text-white px-6 py-3 rounded-lg font-bold shadow-lg transition">
                <i class="fa-solid fa-save mr-2"></i> บันทึกการตั้งค่าทั้งหมด
            </button>
        </div>
    </form>
</div>

<script>
    function previewFiles(input) {
        const fileList = document.getElementById('file-list');
        fileList.innerHTML = '';
        if (input.files && input.files.length > 0) {
            let list = '<ul class="list-disc list-inside">';
            for (let i = 0; i < input.files.length; i++) {
                list += `<li>${input.files[i].name}</li>`;
            }
            list += '</ul>';
            fileList.innerHTML = list;
        }
    }
</script>
@endsection