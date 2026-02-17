@extends('layouts.admin')

@section('title', 'แก้ไขงาน #' . $job->job_number)
@section('header', 'แก้ไขรายละเอียดงาน')

@section('content')
<div class="max-w-5xl mx-auto">

    {{-- ปุ่มย้อนกลับ --}}
    <div class="flex items-center justify-between mb-6">
        <a href="{{ route('admin.jobs.index') }}" class="text-gray-500 hover:text-gray-700 text-sm flex items-center gap-1 transition">
            <i class="fa-solid fa-arrow-left"></i> กลับหน้ารายการ
        </a>
    </div>

    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
        
        {{-- Header --}}
        <div class="bg-orange-50 px-6 py-4 border-b border-orange-100 flex justify-between items-center">
            <h3 class="font-bold text-lg text-orange-800 flex items-center gap-2">
                <i class="fa-solid fa-pen-to-square"></i> แก้ไขงาน #{{ $job->job_number }}
            </h3>
            <span class="px-3 py-1 bg-white text-orange-600 rounded-full text-xs font-bold border border-orange-200 shadow-sm">
                สถานะปัจจุบัน: {{ $job->status }}
            </span>
        </div>

        <div class="p-6 md:p-8">
            <form action="{{ route('admin.jobs.update', $job->id) }}" method="POST">
                @csrf
                @method('PUT')

                {{-- 🟢 SECTION 1: ข้อมูลทั่วไป (Read Only - แก้ไขไม่ได้) --}}
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-8">
                    {{-- ฝั่งซ้าย: ลูกค้า & รถ --}}
                    <div class="space-y-4">
                        <div>
                            <label class="block text-xs font-bold text-gray-400 uppercase tracking-wider mb-1">ลูกค้า</label>
                            <div class="bg-gray-50 text-gray-700 px-4 py-3 rounded-xl border border-gray-200 flex items-center gap-3">
                                <div class="w-10 h-10 rounded-full bg-white flex items-center justify-center border border-gray-100 text-gray-400 shadow-sm">
                                    <i class="fa-solid fa-user"></i>
                                </div>
                                <div>
                                    <p class="font-bold text-sm text-gray-800">{{ $job->customer->name }}</p>
                                    <p class="text-xs text-gray-500"><i class="fa-solid fa-phone mr-1"></i> {{ $job->customer->phone }}</p>
                                </div>
                            </div>
                        </div>
                        <div>
                            <label class="block text-xs font-bold text-gray-400 uppercase tracking-wider mb-1">เครื่องจักร</label>
                            <div class="bg-gray-50 text-gray-700 px-4 py-3 rounded-xl border border-gray-200 flex items-center gap-3">
                                <div class="w-10 h-10 rounded-full bg-white flex items-center justify-center border border-gray-100 text-gray-400 shadow-sm">
                                    <i class="fa-solid fa-tractor"></i>
                                </div>
                                <span class="font-medium text-sm">{{ $job->equipment->name }}</span>
                            </div>
                        </div>
                    </div>

                    {{-- ฝั่งขวา: วันเวลา --}}
                    <div class="bg-blue-50/50 rounded-xl p-5 border border-blue-100 flex flex-col justify-center">
                        <h4 class="text-blue-800 font-bold text-sm mb-4 flex items-center gap-2 border-b border-blue-100 pb-2">
                            <i class="fa-regular fa-calendar-days"></i> วันเวลาที่จอง
                        </h4>
                        <div class="space-y-3">
                            <div class="flex justify-between text-sm">
                                <span class="text-gray-500">เริ่มงาน</span>
                                <span class="font-bold text-gray-700 bg-white px-2 py-1 rounded border border-gray-200">
                                    {{ \Carbon\Carbon::parse($job->scheduled_start)->format('d/m/Y H:i') }}
                                </span>
                            </div>
                            <div class="flex justify-between text-sm">
                                <span class="text-gray-500">สิ้นสุด</span>
                                <span class="font-bold text-gray-700 bg-white px-2 py-1 rounded border border-gray-200">
                                    {{ \Carbon\Carbon::parse($job->scheduled_end)->format('d/m/Y H:i') }}
                                </span>
                            </div>
                            <div class="flex justify-between text-sm pt-2 mt-2 border-t border-blue-100">
                                <span class="text-gray-500">ระยะเวลา</span>
                                <span class="font-bold text-blue-700">
                                    {{ \Carbon\Carbon::parse($job->scheduled_start)->diffInHours($job->scheduled_end) }} ชั่วโมง
                                </span>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="relative flex py-5 items-center">
                    <div class="flex-grow border-t border-gray-200"></div>
                    <span class="flex-shrink-0 mx-4 text-gray-400 text-xs uppercase font-bold tracking-wider">ส่วนแก้ไขข้อมูล</span>
                    <div class="flex-grow border-t border-gray-200"></div>
                </div>

                {{-- 🟡 SECTION 2: การเงินและสถานะ (Editable) --}}
                <div class="bg-yellow-50/50 rounded-2xl p-6 border border-yellow-100 mb-8">
                    <h4 class="font-bold text-yellow-800 mb-4 flex items-center gap-2 text-lg">
                        <i class="fa-solid fa-file-invoice-dollar"></i> กำหนดราคาและสถานะ
                    </h4>

                    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                        {{-- 1. สถานะ --}}
                        <div>
                            <label class="block text-sm font-bold text-gray-700 mb-2">สถานะงาน <span class="text-red-500">*</span></label>
                            <div class="relative">
                                <select name="status" class="w-full pl-4 pr-10 py-3 rounded-xl border border-gray-300 focus:ring-2 focus:ring-yellow-400/50 focus:border-yellow-400 bg-white shadow-sm cursor-pointer">
                                    <option value="pending" {{ $job->status == 'pending' ? 'selected' : '' }}>⏳ รอตรวจสอบ</option>
                                    <option value="scheduled" {{ $job->status == 'scheduled' ? 'selected' : '' }}>✅ อนุมัติ / รอชำระเงิน</option>
                                    <option value="in_progress" {{ $job->status == 'in_progress' ? 'selected' : '' }}>🚜 กำลังดำเนินการ</option>
                                    <option value="completed" {{ $job->status == 'completed' ? 'selected' : '' }}>🎉 เสร็จสิ้น</option>
                                    <option value="cancelled" {{ $job->status == 'cancelled' ? 'selected' : '' }}>❌ ยกเลิก</option>
                                </select>
                            </div>
                        </div>

                        {{-- 2. ราคารวม --}}
                        <div>
                            <label class="block text-sm font-bold text-gray-700 mb-2">ราคารวม (บาท)</label>
                            <div class="relative">
                                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                    <span class="text-gray-500 font-bold">฿</span>
                                </div>
                                <input type="number" name="total_price" value="{{ old('total_price', $job->total_price) }}" step="0.01" min="0"
                                       class="w-full pl-8 pr-3 py-3 rounded-xl border border-gray-300 focus:ring-2 focus:ring-yellow-400/50 focus:border-yellow-400 shadow-sm font-bold text-gray-800"
                                       placeholder="0.00">
                            </div>
                            <p class="text-xs text-gray-500 mt-1 ml-1">ราคาที่ตกลงกับลูกค้า</p>
                        </div>

                        {{-- 3. มัดจำ --}}
                        <div>
                            <label class="block text-sm font-bold text-gray-700 mb-2">ยอดมัดจำ (บาท)</label>
                            <div class="relative">
                                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                    <span class="text-gray-500 font-bold">฿</span>
                                </div>
                                <input type="number" name="deposit_amount" value="{{ old('deposit_amount', $job->deposit_amount) }}" step="0.01" min="0"
                                       class="w-full pl-8 pr-3 py-3 rounded-xl border border-gray-300 focus:ring-2 focus:ring-yellow-400/50 focus:border-yellow-400 shadow-sm"
                                       placeholder="0.00">
                            </div>
                            <p class="text-xs text-gray-500 mt-1 ml-1">ใส่ 0 หากไม่ต้องมัดจำ</p>
                        </div>
                    </div>
                </div>

                {{-- 🟢 SECTION 3: คนขับ & หมายเหตุ --}}
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-8">
                    <div>
                        <label class="block text-sm font-bold text-gray-700 mb-2">พนักงานขับรถ (Assigned Staff)</label>
                        <div class="relative">
                            <i class="fa-solid fa-id-card absolute left-3 top-1/2 -translate-y-1/2 text-gray-400"></i>
                            <select name="assigned_staff_id" class="w-full pl-10 pr-4 py-3 rounded-xl border border-gray-300 focus:ring-2 focus:ring-green-500/20 focus:border-green-500 bg-white shadow-sm cursor-pointer">
                                <option value="">-- ยังไม่ระบุ --</option>
                                @foreach($staffs as $staff)
                                    <option value="{{ $staff->id }}" {{ $job->assigned_staff_id == $staff->id ? 'selected' : '' }}>
                                        {{ $staff->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div>
                        <label class="block text-sm font-bold text-gray-700 mb-2">หมายเหตุเพิ่มเติม</label>
                        <textarea name="note" rows="1" class="w-full px-4 py-3 rounded-xl border border-gray-300 focus:ring-2 focus:ring-green-500/20 focus:border-green-500 shadow-sm" placeholder="เช่น สถานที่หน้างาน, เบอร์โทรสำรอง...">{{ old('note', $job->note) }}</textarea>
                    </div>
                </div>

                {{-- Action Buttons --}}
                <div class="flex items-center justify-between pt-6 border-t border-gray-100 bg-gray-50 -mx-6 -mb-6 px-6 py-4 md:px-8 md:rounded-b-2xl mt-6">
                    <button type="button" onclick="if(confirm('⚠️ ยืนยันที่จะยกเลิกงานนี้? การกระทำนี้ไม่สามารถย้อนกลับได้')) document.getElementById('cancelForm').submit();" 
                            class="text-red-500 hover:text-red-700 text-sm font-bold px-4 py-2 hover:bg-red-50 rounded-lg transition flex items-center gap-2">
                        <i class="fa-solid fa-trash-can"></i> <span class="hidden md:inline">ยกเลิกงานนี้</span>
                    </button>

                    <div class="flex gap-3">
                        <a href="{{ route('admin.jobs.index') }}" class="px-6 py-2.5 rounded-xl text-sm font-bold text-gray-500 hover:bg-gray-200 transition">
                            ยกเลิก
                        </a>
                        <button type="submit" class="bg-green-600 text-white px-8 py-2.5 rounded-xl shadow-lg shadow-green-600/30 hover:bg-green-700 hover:-translate-y-0.5 transition font-bold flex items-center gap-2">
                            <i class="fa-solid fa-save"></i> บันทึกข้อมูล
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>
    
    {{-- Form สำหรับปุ่มยกเลิก --}}
    <form id="cancelForm" action="{{ route('admin.jobs.cancel', $job->id) }}" method="POST" class="hidden">
        @csrf
    </form>

</div>
@endsection