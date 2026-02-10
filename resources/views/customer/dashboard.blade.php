@extends('layouts.customer')

@section('content')
<div class="space-y-6">
    
    {{-- 1. การ์ดข้อมูลส่วนตัว (คงเดิม) --}}
    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
        <div class="bg-gradient-to-r from-agri-primary to-agri-secondary px-6 py-8 relative overflow-hidden">
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
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- 2. ส่วนแสดงประวัติการจอง --}}
    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
        <div class="px-6 py-5 border-b border-gray-100 flex items-center justify-between">
            <h3 class="text-lg font-bold text-gray-800 flex items-center gap-2">
                <i class="fa-solid fa-clock-rotate-left text-agri-primary"></i> รายการจองของคุณ
            </h3>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-sm text-left">
                <thead class="bg-gray-50 text-gray-600 uppercase text-xs font-semibold">
                    <tr>
                        <th class="px-6 py-4 rounded-tl-lg whitespace-nowrap">เลขที่ใบงาน</th>
                        <th class="px-6 py-4 whitespace-nowrap">วันที่จอง</th>
                        <th class="px-6 py-4 min-w-[150px]">เครื่องจักร</th>
                        <th class="px-6 py-4 text-center whitespace-nowrap">สถานะ</th>
                        <th class="px-6 py-4 text-right whitespace-nowrap">ยอดเงิน</th>
                        <th class="px-6 py-4 text-center rounded-tr-lg whitespace-nowrap">จัดการ</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @if(isset($bookings) && $bookings->count() > 0)
                        @foreach($bookings as $booking)
                        <tr class="hover:bg-gray-50/50 transition duration-150">
                            {{-- เลขที่งาน (คลิกได้) --}}
                            <td class="px-6 py-4 font-medium text-agri-primary whitespace-nowrap">
                                <a href="{{ route('customer.booking.show', $booking->id) }}" class="hover:underline hover:text-green-700">
                                    #{{ $booking->job_number }}
                                </a>
                            </td>
                            {{-- วันที่ --}}
                            <td class="px-6 py-4 text-gray-600 whitespace-nowrap">
                                <div class="flex flex-col">
                                    <span class="font-medium">{{ \Carbon\Carbon::parse($booking->scheduled_start)->format('d/m/Y') }}</span>
                                    <span class="text-xs text-gray-400">{{ \Carbon\Carbon::parse($booking->scheduled_start)->format('H:i') }} น.</span>
                                </div>
                            </td>
                            {{-- เครื่องจักร --}}
                            <td class="px-6 py-4 text-gray-600">
                                @if($booking->equipment)
                                    <span class="inline-flex items-center gap-1.5">
                                        <i class="fa-solid fa-tractor text-gray-400"></i> {{ $booking->equipment->name }}
                                    </span>
                                @else
                                    <span class="text-gray-400">-</span>
                                @endif
                            </td>
                            
                            {{-- สถานะ (รวมปุ่มจ่ายเงิน) --}}
                            <td class="px-6 py-4 text-center align-middle whitespace-nowrap">
                                <div class="flex flex-col gap-2 items-center">
                                    {{-- Status Label --}}
                                    @php
                                        $statusConfig = match($booking->status) {
                                            'completed' => ['bg' => 'bg-green-100', 'text' => 'text-green-700', 'label' => 'เสร็จสิ้น'],
                                            'in_progress' => ['bg' => 'bg-blue-100', 'text' => 'text-blue-700', 'label' => 'กำลังทำ'],
                                            'scheduled' => ['bg' => 'bg-yellow-100', 'text' => 'text-yellow-700', 'label' => 'รอคิว'],
                                            'cancelled' => ['bg' => 'bg-red-100', 'text' => 'text-red-700', 'label' => 'ยกเลิก'],
                                            default => ['bg' => 'bg-gray-100', 'text' => 'text-gray-700', 'label' => $booking->status]
                                        };
                                    @endphp
                                    <span class="px-3 py-1 rounded-full text-xs font-semibold {{ $statusConfig['bg'] }} {{ $statusConfig['text'] }}">
                                        {{ $statusConfig['label'] }}
                                    </span>

                                    {{-- Payment Button --}}
                                    @if($booking->status != 'cancelled')
                                        @if($booking->payment_status == 'pending')
                                            <a href="{{ route('customer.booking.payment', $booking->id) }}" 
                                               class="inline-flex items-center gap-1 bg-agri-primary text-white px-3 py-1 rounded-md text-[10px] font-bold shadow-sm hover:bg-green-700 transition animate-pulse">
                                                <i class="fa-solid fa-qrcode"></i> ชำระเงิน
                                            </a>
                                        @elseif($booking->payment_status == 'pending_approval')
                                            <span class="text-[10px] text-orange-500 font-medium"><i class="fa-solid fa-clock"></i> รอตรวจสลิป</span>
                                        @endif
                                    @endif
                                </div>
                            </td>

                            {{-- ยอดเงิน --}}
                            <td class="px-6 py-4 text-right font-medium text-gray-700 whitespace-nowrap">
                                {{ number_format($booking->total_price, 2) }}
                            </td>

                            {{-- ปุ่ม Action --}}
                            <td class="px-6 py-4 text-center whitespace-nowrap">
                                <a href="{{ route('customer.booking.show', $booking->id) }}" 
                                   class="text-gray-400 hover:text-agri-primary transition" 
                                   title="ดูรายละเอียด">
                                    <i class="fa-solid fa-chevron-right text-lg"></i>
                                </a>
                            </td>
                        </tr>
                        @endforeach
                    @else
                        <tr>
                            <td colspan="6" class="px-6 py-12 text-center text-gray-400">
                                <p>ยังไม่มีประวัติการจอง</p>
                            </td>
                        </tr>
                    @endif
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection