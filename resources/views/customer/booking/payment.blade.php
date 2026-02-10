@extends('layouts.customer')

@section('content')
<div class="max-w-lg mx-auto py-10">
    <div class="bg-white rounded-3xl shadow-xl overflow-hidden border border-gray-100">
        
        {{-- Header --}}
        <div class="bg-agri-primary p-6 text-center text-white">
            <h2 class="text-xl font-bold">ชำระค่าบริการ</h2>
            <p class="text-sm text-green-100">ใบงาน #{{ $booking->job_number }}</p>
        </div>

        <div class="p-8">
            {{-- ยอดเงิน --}}
            <div class="text-center mb-8">
                <p class="text-gray-500 text-sm">ยอดชำระทั้งหมด</p>
                <div class="text-4xl font-bold text-agri-primary mt-2">
                    {{ number_format($booking->total_price, 2) }} <span class="text-lg text-gray-400">฿</span>
                </div>
            </div>

            {{-- QR Code --}}
            <div class="flex justify-center mb-8">
                <div class="p-4 bg-white border-2 border-agri-primary/20 rounded-2xl shadow-sm">
                    {{-- ⚠️ ใส่ URL ของ QR Code ที่เจนมาจาก Controller --}}
                    <img src="{{ $qrUrl }}" alt="PromptPay QR" class="w-48 h-48">
                </div>
            </div>

            <p class="text-center text-sm text-gray-500 mb-6">
                สแกนด้วยแอปธนาคารเพื่อชำระเงิน<br>
                เมื่อโอนเสร็จแล้ว <b>กรุณาแนบสลิปด้านล่าง</b>
            </p>

            <hr class="border-gray-100 mb-6">

            {{-- ฟอร์มอัปโหลดสลิป --}}
            <form action="{{ route('customer.booking.upload_slip', $booking->id) }}" method="POST" enctype="multipart/form-data">
                @csrf
                
                <div class="mb-6">
                    <label class="block text-sm font-medium text-gray-700 mb-2">แนบหลักฐานการโอนเงิน (Slip)</label>
                    <input type="file" name="slip_image" required accept="image/*"
                           class="block w-full text-sm text-slate-500
                                  file:mr-4 file:py-2.5 file:px-4
                                  file:rounded-full file:border-0
                                  file:text-sm file:font-semibold
                                  file:bg-green-50 file:text-green-700
                                  hover:file:bg-green-100 cursor-pointer border border-gray-200 rounded-xl">
                </div>

                <button type="submit" class="w-full py-3.5 bg-agri-primary text-white rounded-xl font-bold shadow-lg hover:bg-green-800 transition">
                    <i class="fa-solid fa-paper-plane mr-2"></i> แจ้งชำระเงิน
                </button>
                
                <a href="{{ route('customer.dashboard') }}" class="block text-center mt-4 text-sm text-gray-400 hover:text-gray-600">
                    ย้อนกลับ
                </a>
            </form>
        </div>
    </div>
</div>
@endsection