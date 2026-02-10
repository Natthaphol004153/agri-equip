@extends('layouts.customer')

@section('content')
<div class="max-w-4xl mx-auto space-y-6">
    {{-- ปุ่มย้อนกลับ --}}
    <a href="{{ route('customer.dashboard') }}" class="inline-flex items-center text-gray-500 hover:text-agri-primary transition">
        <i class="fa-solid fa-arrow-left mr-2"></i> กลับไปหน้าประวัติ
    </a>

    {{-- การ์ดรายละเอียดงาน --}}
    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
        <div class="bg-gray-50 px-6 py-4 border-b border-gray-100 flex justify-between items-center">
            <h3 class="font-bold text-lg text-gray-800">รายละเอียดใบงาน #{{ $booking->job_number }}</h3>
            <span class="px-3 py-1 rounded-full text-xs font-bold 
                {{ $booking->status == 'completed' ? 'bg-green-100 text-green-700' : 'bg-blue-100 text-blue-700' }}">
                {{ $booking->status }}
            </span>
        </div>
        
        <div class="p-6 grid grid-cols-1 md:grid-cols-2 gap-6">
            {{-- ข้อมูลเครื่องจักร --}}
            <div>
                <h4 class="text-sm font-semibold text-gray-500 mb-2">บริการ/เครื่องจักร</h4>
                <div class="flex items-center gap-3">
                    @if($booking->equipment && $booking->equipment->image_path)
                        <img src="{{ asset('storage/'.$booking->equipment->image_path) }}" class="w-16 h-16 rounded-lg object-cover">
                    @else
                        <div class="w-16 h-16 rounded-lg bg-gray-100 flex items-center justify-center text-gray-400">
                            <i class="fa-solid fa-tractor text-2xl"></i>
                        </div>
                    @endif
                    <div>
                        <p class="font-bold text-gray-800">{{ $booking->equipment->name ?? '-' }}</p>
                        <p class="text-sm text-gray-500">{{ $booking->equipment->registration_number ?? '' }}</p>
                    </div>
                </div>
            </div>

            {{-- ข้อมูลวันเวลา --}}
            <div>
                <h4 class="text-sm font-semibold text-gray-500 mb-2">วันและเวลาที่จอง</h4>
                <p class="text-gray-800"><i class="fa-regular fa-calendar mr-2 text-agri-primary"></i> {{ \Carbon\Carbon::parse($booking->scheduled_start)->format('d/m/Y') }}</p>
                <p class="text-gray-800 mt-1"><i class="fa-regular fa-clock mr-2 text-agri-primary"></i> {{ \Carbon\Carbon::parse($booking->scheduled_start)->format('H:i') }} - {{ \Carbon\Carbon::parse($booking->scheduled_end)->format('H:i') }}</p>
            </div>
        </div>
        
        {{-- รูปภาพผลงาน (ถ้ามี) --}}
        @if($booking->image_path)
        <div class="p-6 border-t border-gray-100">
            <h4 class="text-sm font-semibold text-gray-500 mb-3">รูปภาพผลงาน</h4>
            <img src="{{ asset('storage/'.$booking->image_path) }}" class="rounded-xl w-full md:w-1/2 shadow-md">
        </div>
        @endif
    </div>
</div>
@endsection