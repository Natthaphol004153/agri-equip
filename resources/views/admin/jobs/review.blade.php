@extends('layouts.admin')

@section('title', 'ตรวจรับงาน #' . $job->job_number)
@section('header', 'ตรวจสอบและอนุมัติจบงาน')

@section('content')
<div class="max-w-4xl mx-auto space-y-6">

    <div class="flex items-center justify-between mb-2">
        <a href="{{ route('admin.jobs.index') }}" class="text-gray-500 hover:text-gray-700 flex items-center gap-2">
            <i class="fa-solid fa-arrow-left"></i> กลับหน้ารายการ
        </a>
        <span class="bg-orange-100 text-orange-700 px-3 py-1 rounded-full text-sm font-bold border border-orange-200 animate-pulse">
            <i class="fa-solid fa-clipboard-check"></i> รอแอดมินตรวจสอบ
        </span>
    </div>

    {{-- ข้อมูลหลัก --}}
    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
        <h2 class="text-xl font-bold text-gray-800 mb-4 border-b pb-3">ข้อมูลงาน: {{ $job->job_number }}</h2>
        
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 text-sm">
            <div>
                <p class="text-gray-500 mb-1">ลูกค้า:</p>
                <p class="font-bold text-gray-800 text-base">{{ $job->customer->name }} <a href="tel:{{ $job->customer->phone }}" class="text-blue-500 text-sm ml-2"><i class="fa-solid fa-phone"></i> โทร</a></p>
            </div>
            <div>
                <p class="text-gray-500 mb-1">พนักงานที่ปฏิบัติงาน:</p>
                <p class="font-bold text-gray-800 text-base">
                    <i class="fa-solid fa-user-gear text-orange-500 mr-1"></i> {{ $job->assignedStaff->name ?? 'ไม่ระบุ' }}
                </p>
            </div>
            <div>
                <p class="text-gray-500 mb-1">พื้นที่ประเมิน (คิดเงิน): <span class="font-bold text-gray-800">{{ number_format($job->estimated_area, 1) }} ไร่</span></p>
                <p class="text-gray-500">พื้นที่ทำจริง (เก็บสถิติ): <span class="font-bold text-green-600">{{ number_format($job->actual_area, 1) }} ไร่</span></p>
            </div>
            <div>
                <p class="text-gray-500 mb-1">เวลาจบงานจริง:</p>
                <p class="font-bold text-gray-800">{{ \Carbon\Carbon::parse($job->actual_end)->format('d/m/Y H:i น.') }}</p>
            </div>
        </div>

        @if($job->note)
        <div class="mt-4 bg-yellow-50 p-3 rounded-xl border border-yellow-100">
            <p class="text-xs font-bold text-yellow-800 mb-1">📝 หมายเหตุจากพนักงาน:</p>
            <p class="text-sm text-yellow-700">{{ $job->note }}</p>
        </div>
        @endif
    </div>

    {{-- สรุปยอดเงินและหลักฐาน --}}
    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
        
        {{-- ยอดเงิน --}}
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
            <h3 class="font-bold text-gray-800 mb-4 flex items-center gap-2"><i class="fa-solid fa-money-check-dollar text-green-600"></i> สรุปการเงิน</h3>
            <div class="space-y-3 text-sm">
                <div class="flex justify-between">
                    <span class="text-gray-500">ยอดรวมตกลงไว้ ({{ number_format($job->estimated_area, 1) }} ไร่)</span>
                    <span class="font-bold">{{ number_format($job->total_price, 2) }} ฿</span>
                </div>
                <div class="flex justify-between text-red-500">
                    <span>หักมัดจำแล้ว</span>
                    <span>-{{ number_format($job->deposit_amount, 2) }} ฿</span>
                </div>
                <div class="border-t border-dashed pt-2 mt-2 flex justify-between items-center">
                    <span class="font-bold text-gray-700">ยอดที่พนักงานต้องเก็บเพิ่ม</span>
                    <span class="text-2xl font-black text-green-600">{{ number_format($job->total_price - $job->deposit_amount, 2) }} ฿</span>
                </div>
                <div class="mt-4 bg-gray-50 p-3 rounded-lg flex justify-between items-center">
                    <span class="text-gray-500">วิธีชำระเงินที่พนักงานแจ้ง:</span>
                    <span class="font-bold text-blue-600 uppercase">{{ $job->payment_method == 'cash' ? 'เงินสด (Cash)' : 'โอนเงิน (Transfer)' }}</span>
                </div>
            </div>
        </div>

        {{-- รูปหลักฐาน --}}
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
            <h3 class="font-bold text-gray-800 mb-4 flex items-center gap-2"><i class="fa-solid fa-images text-blue-500"></i> หลักฐานจากหน้างาน</h3>
            <div class="grid grid-cols-2 gap-4">
                {{-- รูปหน้างาน --}}
                <div>
                    <p class="text-xs text-gray-500 mb-2 text-center">📸 รูปผลงาน</p>
                    @if($job->image_path)
                        <a href="{{ asset('storage/' . $job->image_path) }}" target="_blank">
                            <img src="{{ asset('storage/' . $job->image_path) }}" class="w-full h-32 object-cover rounded-xl border hover:opacity-80 transition">
                        </a>
                    @else
                        <div class="w-full h-32 bg-gray-100 rounded-xl flex items-center justify-center text-gray-400 text-xs border border-dashed">ไม่มีรูป</div>
                    @endif
                </div>

                {{-- สลิป --}}
                <div>
                    <p class="text-xs text-gray-500 mb-2 text-center">💸 สลิปโอนเงิน</p>
                    @if($job->payment_method == 'cash')
                        <div class="w-full h-32 bg-blue-50 rounded-xl flex flex-col items-center justify-center text-blue-500 text-xs border border-blue-200">
                            <i class="fa-solid fa-hand-holding-dollar text-2xl mb-1"></i>
                            รับเงินสด
                        </div>
                    @elseif($job->payment_proof)
                        <a href="{{ asset('storage/' . $job->payment_proof) }}" target="_blank">
                            <img src="{{ asset('storage/' . $job->payment_proof) }}" class="w-full h-32 object-cover rounded-xl border hover:opacity-80 transition">
                        </a>
                    @else
                        <div class="w-full h-32 bg-red-50 rounded-xl flex items-center justify-center text-red-400 text-xs border border-red-200 border-dashed">ไม่พบสลิป</div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    {{-- ปุ่ม Action --}}
    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6 flex flex-col items-center justify-center gap-4">
        <p class="text-gray-500 text-sm text-center">ตรวจสอบรูปผลงานและยอดเงินให้เรียบร้อย หากถูกต้องให้กดปุ่มด้านล่างเพื่อปิดงานและออกใบเสร็จ</p>
        
        <form action="{{ route('admin.jobs.approve', $job->id) }}" method="POST" class="w-full md:w-1/2">
            @csrf
            <button type="submit" class="w-full bg-green-600 hover:bg-green-700 text-white font-bold py-4 rounded-xl shadow-lg shadow-green-200 transition-all flex items-center justify-center gap-2 text-lg">
                <i class="fa-solid fa-check-circle"></i> อนุมัติและปิดงาน (Complete)
            </button>
        </form>
    </div>

</div>
@endsection