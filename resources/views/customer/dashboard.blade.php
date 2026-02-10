@extends('layouts.customer') {{-- ✅ เปลี่ยนมาใช้ Layout ใหม่ --}}

@section('content')
<div class="space-y-6">
    
    {{-- 1. การ์ดข้อมูลส่วนตัว --}}
    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
        <div class="bg-gradient-to-r from-agri-primary to-agri-secondary px-6 py-8 relative overflow-hidden">
            {{-- Decoration --}}
            <div class="absolute top-0 right-0 w-32 h-32 bg-white/5 rounded-full -mr-10 -mt-10 blur-xl"></div>
            
            <div class="relative z-10 flex flex-col md:flex-row items-center md:items-start gap-6 text-center md:text-left">
                <div class="w-20 h-20 bg-white rounded-full flex items-center justify-center shadow-lg text-3xl text-agri-primary font-bold border-4 border-agri-accent/30">
                    {{ substr(Auth::guard('customer')->user()->name, 0, 1) }}
                </div>
                <div class="text-white flex-1">
                    <h2 class="text-2xl font-bold">{{ Auth::guard('customer')->user()->name }}</h2>
                    <div class="flex flex-wrap justify-center md:justify-start gap-3 mt-2 text-sm text-gray-200">
                        <span class="bg-white/10 px-3 py-1 rounded-full border border-white/20 flex items-center gap-2">
                            <i class="fa-solid fa-phone text-agri-accent"></i> {{ Auth::guard('customer')->user()->phone }}
                        </span>
                        <span class="bg-white/10 px-3 py-1 rounded-full border border-white/20 flex items-center gap-2">
                            <i class="fa-solid fa-user-tag text-agri-accent"></i> 
                            {{ ucfirst(Auth::guard('customer')->user()->customer_type) }}
                        </span>
                        @if(Auth::guard('customer')->user()->customer_code)
                        <span class="bg-white/10 px-3 py-1 rounded-full border border-white/20 flex items-center gap-2">
                            <i class="fa-solid fa-barcode text-agri-accent"></i> {{ Auth::guard('customer')->user()->customer_code }}
                        </span>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- 2. ส่วนแสดงประวัติการจอง --}}
    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
        <div class="px-6 py-5 border-b border-gray-100 flex items-center justify-between">
            <h3 class="text-lg font-bold text-gray-800 flex items-center gap-2">
                <i class="fa-solid fa-clock-rotate-left text-agri-primary"></i> ประวัติการเข้าใช้บริการ
            </h3>
            <span class="text-xs font-medium text-gray-500 bg-gray-100 px-2 py-1 rounded-lg">
                ทั้งหมด {{ isset($bookings) ? $bookings->count() : 0 }} รายการ
            </span>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-sm text-left">
                <thead class="bg-gray-50 text-gray-600 uppercase text-xs font-semibold">
                    <tr>
                        <th class="px-6 py-4 rounded-tl-lg">เลขที่ใบงาน</th>
                        <th class="px-6 py-4">วันที่จอง</th>
                        <th class="px-6 py-4">บริการ/เครื่องจักร</th>
                        <th class="px-6 py-4 text-center">สถานะ</th>
                        <th class="px-6 py-4 text-right">ยอดเงิน</th>
                        <th class="px-6 py-4 text-center rounded-tr-lg">เพิ่มเติม</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @if(isset($bookings) && $bookings->count() > 0)
                        @foreach($bookings as $booking)
                        <tr class="hover:bg-gray-50/50 transition duration-150">
                            <td class="px-6 py-4 font-medium text-agri-primary">
                                #{{ $booking->job_number }}
                            </td>
                            <td class="px-6 py-4 text-gray-600">
                                <div class="flex flex-col">
                                    <span class="font-medium">{{ \Carbon\Carbon::parse($booking->scheduled_start)->format('d/m/Y') }}</span>
                                    <span class="text-xs text-gray-400">{{ \Carbon\Carbon::parse($booking->scheduled_start)->format('H:i') }} น.</span>
                                </div>
                            </td>
                            <td class="px-6 py-4 text-gray-600">
                                @if($booking->equipment)
                                    <span class="inline-flex items-center gap-1.5">
                                        <i class="fa-solid fa-wrench text-gray-400"></i> {{ $booking->equipment->name }}
                                    </span>
                                @else
                                    <span class="text-gray-400">-</span>
                                @endif
                            </td>
                            <td class="px-6 py-4 text-center">
                                @php
                                    $statusClass = match($booking->status) {
                                        'completed' => 'bg-green-100 text-green-700 border-green-200',
                                        'in_progress' => 'bg-blue-100 text-blue-700 border-blue-200',
                                        'scheduled' => 'bg-yellow-100 text-yellow-700 border-yellow-200',
                                        'cancelled' => 'bg-red-100 text-red-700 border-red-200',
                                        default => 'bg-gray-100 text-gray-700 border-gray-200'
                                    };
                                    
                                    $statusLabel = match($booking->status) {
                                        'completed' => 'เสร็จสิ้น',
                                        'in_progress' => 'กำลังดำเนินการ',
                                        'scheduled' => 'รอคิว',
                                        'cancelled' => 'ยกเลิก',
                                        default => $booking->status
                                    };
                                @endphp
                                <span class="px-3 py-1 rounded-full text-xs font-semibold border {{ $statusClass }}">
                                    {{ $statusLabel }}
                                </span>
                            </td>
                            <td class="px-6 py-4 text-right font-medium text-gray-700">
                                {{ number_format($booking->total_price, 2) }} ฿
                            </td>
                            <td class="px-6 py-4 text-center">
                                @if($booking->image_path)
                                    <a href="{{ asset('storage/' . $booking->image_path) }}" target="_blank" 
                                       class="text-agri-primary hover:text-agri-accent transition text-lg" title="ดูรูปผลงาน">
                                        <i class="fa-regular fa-image"></i>
                                    </a>
                                @else
                                    <span class="text-gray-300 text-lg"><i class="fa-regular fa-image-slash"></i></span>
                                @endif
                            </td>
                        </tr>
                        @endforeach
                    @else
                        <tr>
                            <td colspan="6" class="px-6 py-12 text-center text-gray-400">
                                <div class="flex flex-col items-center justify-center">
                                    <div class="w-16 h-16 bg-gray-100 rounded-full flex items-center justify-center mb-3">
                                        <i class="fa-regular fa-folder-open text-2xl text-gray-300"></i>
                                    </div>
                                    <p>ยังไม่มีประวัติการจองในขณะนี้</p>
                                </div>
                            </td>
                        </tr>
                    @endif
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection