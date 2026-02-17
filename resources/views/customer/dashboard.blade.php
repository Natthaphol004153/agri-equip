@extends('layouts.customer')

@section('content')
    <div class="space-y-8 max-w-7xl mx-auto pb-10">

        {{-- 1. ✨ การ์ดข้อมูลส่วนตัว (Profile Card) --}}
        <div class="bg-white rounded-3xl shadow-lg border border-gray-100 overflow-hidden relative group">
            {{-- Background Gradient & Decorative Circles --}}
            <div class="absolute inset-0 bg-gradient-to-br from-green-600 to-green-800 opacity-90 transition group-hover:opacity-100 duration-500"></div>
            <div class="absolute -top-24 -right-24 w-64 h-64 bg-white/10 rounded-full blur-3xl"></div>
            <div class="absolute -bottom-24 -left-24 w-64 h-64 bg-yellow-400/20 rounded-full blur-3xl"></div>
            
            <div class="relative z-10 px-6 py-10 md:px-10 md:py-12 flex flex-col md:flex-row items-center gap-8">
                
                {{-- Avatar --}}
                <div class="relative shrink-0">
                    <div class="w-24 h-24 md:w-32 md:h-32 rounded-full border-4 border-white/30 shadow-2xl overflow-hidden flex items-center justify-center bg-white group-hover:scale-105 transition transform duration-500">
                        @if (Auth::guard('customer')->user()->profile_image)
                            <img src="{{ asset('storage/' . Auth::guard('customer')->user()->profile_image) }}"
                                 alt="Profile" class="w-full h-full object-cover">
                        @else
                            <span class="text-4xl md:text-5xl font-bold text-green-700 select-none">
                                {{ substr(Auth::guard('customer')->user()->name, 0, 1) }}
                            </span>
                        @endif
                    </div>
                    {{-- Status Indicator --}}
                    <div class="absolute bottom-2 right-2 w-6 h-6 bg-green-400 border-4 border-green-800 rounded-full"></div>
                </div>

                {{-- User Info --}}
                <div class="text-center md:text-left flex-1 text-white">
                    <h2 class="text-3xl md:text-4xl font-bold mb-2 tracking-tight">
                        สวัสดี, {{ Auth::guard('customer')->user()->name }} 👋
                    </h2>
                    <p class="text-green-100 text-sm md:text-base mb-6 opacity-90">
                        ยินดีต้อนรับสู่ระบบจัดการจองเครื่องจักรเกษตร
                    </p>

                    <div class="flex flex-wrap justify-center md:justify-start gap-3">
                        <span class="bg-white/10 backdrop-blur-md px-4 py-2 rounded-xl border border-white/20 flex items-center gap-2 text-sm hover:bg-white/20 transition cursor-default">
                            <i class="fa-solid fa-phone text-yellow-300"></i>
                            {{ Auth::guard('customer')->user()->phone }}
                        </span>
                        <span class="bg-white/10 backdrop-blur-md px-4 py-2 rounded-xl border border-white/20 flex items-center gap-2 text-sm hover:bg-white/20 transition cursor-default">
                            <i class="fa-solid fa-user-tag text-yellow-300"></i>
                            {{ ucfirst(Auth::guard('customer')->user()->customer_type) }}
                        </span>
                    </div>
                </div>

                {{-- Action Button (PC Only) --}}
                <div class="hidden md:block">
                     <a href="{{ route('customer.booking.create') }}" 
                       class="bg-yellow-400 text-green-900 px-8 py-4 rounded-2xl font-bold shadow-lg hover:bg-yellow-300 hover:-translate-y-1 hover:shadow-xl transition-all duration-300 flex items-center gap-3">
                        <i class="fa-solid fa-plus-circle text-xl"></i>
                        <span>จองคิวงานใหม่</span>
                    </a>
                </div>
            </div>
        </div>

        {{-- 2. 📅 ประวัติการจอง (Booking History) --}}
        <div>
            {{-- Header Section --}}
            <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 mb-6 px-2">
                <h3 class="text-2xl font-bold text-gray-800 flex items-center gap-3">
                    <span class="bg-green-100 text-green-700 p-2 rounded-lg text-lg">
                        <i class="fa-solid fa-clock-rotate-left"></i>
                    </span>
                    รายการจองของคุณ
                </h3>
                
                {{-- Mobile Action Button --}}
                <a href="{{ route('customer.booking.create') }}" 
                   class="md:hidden w-full bg-yellow-400 text-green-900 px-6 py-3 rounded-xl font-bold shadow-md hover:bg-yellow-300 flex items-center justify-center gap-2 transition active:scale-95">
                    <i class="fa-solid fa-plus"></i> จองคิวงานใหม่
                </a>
            </div>

            <div class="bg-white rounded-3xl shadow-sm border border-gray-100 overflow-hidden">
                
                {{-- 🚫 STATE: ไม่มีข้อมูล --}}
                @if (!isset($bookings) || $bookings->count() == 0)
                    <div class="flex flex-col items-center justify-center py-16 text-center px-4">
                        <div class="w-24 h-24 bg-gray-50 rounded-full flex items-center justify-center mb-4">
                            <i class="fa-regular fa-calendar-xmark text-4xl text-gray-300"></i>
                        </div>
                        <h4 class="text-lg font-bold text-gray-600">ยังไม่มีรายการจอง</h4>
                        <p class="text-gray-400 mb-6">เริ่มจองเครื่องจักรเพื่อช่วยงานเกษตรของคุณได้เลย</p>
                        <a href="{{ route('customer.booking.create') }}" class="text-green-600 font-semibold hover:underline">
                            จองคิวตอนนี้เลย &rarr;
                        </a>
                    </div>
                @else
                
                    {{-- 📱 MOBILE VIEW: Cards Stack (แสดงบนมือถือ) --}}
                    <div class="grid grid-cols-1 gap-4 p-4 md:hidden bg-gray-50">
                        @foreach ($bookings as $booking)
                            <div class="bg-white p-5 rounded-2xl shadow-sm border border-gray-100 hover:shadow-md transition-shadow relative overflow-hidden">
                                {{-- Status Stripe --}}
                                @php
                                    $stripeColor = match ($booking->status) {
                                        'completed' => 'bg-green-500',
                                        'in_progress' => 'bg-blue-500',
                                        'scheduled' => 'bg-yellow-400',
                                        'cancelled' => 'bg-red-500',
                                        default => 'bg-gray-300',
                                    };
                                @endphp
                                <div class="absolute left-0 top-0 bottom-0 w-1.5 {{ $stripeColor }}"></div>

                                <div class="flex justify-between items-start mb-3 pl-3">
                                    <div>
                                        <span class="text-xs text-gray-400 font-medium">เลขที่ใบงาน</span>
                                        <h4 class="text-lg font-bold text-gray-800 leading-tight">#{{ $booking->job_number }}</h4>
                                    </div>
                                    {{-- Status Badge (Mobile) --}}
                                    @php
                                        $statusConfig = match ($booking->status) {
                                            'completed' => ['bg' => 'bg-green-100', 'text' => 'text-green-700', 'label' => 'เสร็จสิ้น'],
                                            'in_progress' => ['bg' => 'bg-blue-100', 'text' => 'text-blue-700', 'label' => 'กำลังทำ'],
                                            'scheduled' => ['bg' => 'bg-yellow-100', 'text' => 'text-yellow-700', 'label' => 'รอคิว'],
                                            'cancelled' => ['bg' => 'bg-red-100', 'text' => 'text-red-700', 'label' => 'ยกเลิก'],
                                            default => ['bg' => 'bg-gray-100', 'text' => 'text-gray-700', 'label' => $booking->status],
                                        };
                                    @endphp
                                    <span class="px-3 py-1 rounded-full text-xs font-bold {{ $statusConfig['bg'] }} {{ $statusConfig['text'] }}">
                                        {{ $statusConfig['label'] }}
                                    </span>
                                </div>

                                <div class="space-y-2 pl-3 mb-4">
                                    <div class="flex items-center text-sm text-gray-600">
                                        <i class="fa-regular fa-calendar w-6 text-center mr-1 text-gray-400"></i>
                                        {{ \Carbon\Carbon::parse($booking->scheduled_start)->format('d/m/Y') }} 
                                        <span class="text-gray-400 text-xs ml-2">({{ \Carbon\Carbon::parse($booking->scheduled_start)->format('H:i') }})</span>
                                    </div>
                                    <div class="flex items-center text-sm text-gray-600">
                                        <i class="fa-solid fa-tractor w-6 text-center mr-1 text-gray-400"></i>
                                        {{ $booking->equipment->name ?? '-' }}
                                    </div>
                                    <div class="flex items-center text-sm text-gray-600">
                                        <i class="fa-solid fa-coins w-6 text-center mr-1 text-gray-400"></i>
                                        <span class="font-bold text-green-600">{{ number_format($booking->total_price, 2) }} บ.</span>
                                    </div>
                                </div>

                                <div class="flex justify-between items-center pl-3 pt-3 border-t border-gray-100">
                                    {{-- Payment Status Action --}}
                                    @if ($booking->status != 'cancelled' && $booking->payment_status == 'pending')
                                        <a href="{{ route('customer.booking.payment', $booking->id) }}" class="text-xs font-bold text-white bg-green-600 px-3 py-1.5 rounded-lg shadow hover:bg-green-700 transition animate-pulse">
                                            <i class="fa-solid fa-qrcode mr-1"></i> จ่ายเงิน
                                        </a>
                                    @elseif($booking->payment_status == 'pending_approval')
                                        <span class="text-xs text-orange-500 font-medium bg-orange-50 px-2 py-1 rounded-md">
                                            <i class="fa-solid fa-clock"></i> รอตรวจสอบ
                                        </span>
                                    @else
                                        <span></span> {{-- Spacer --}}
                                    @endif

                                    <a href="{{ route('customer.booking.show', $booking->id) }}" class="text-gray-400 hover:text-green-600 font-medium text-sm flex items-center transition">
                                        ดูรายละเอียด <i class="fa-solid fa-chevron-right ml-1 text-xs"></i>
                                    </a>
                                </div>
                            </div>
                        @endforeach
                    </div>

                    {{-- 💻 DESKTOP VIEW: Table (แสดงบนคอมพิวเตอร์) --}}
                    <div class="hidden md:block overflow-x-auto">
                        <table class="w-full text-left border-collapse">
                            <thead class="bg-gray-50/80 border-b border-gray-200">
                                <tr>
                                    <th class="px-8 py-5 text-xs font-bold text-gray-500 uppercase tracking-wider">ใบงาน</th>
                                    <th class="px-6 py-5 text-xs font-bold text-gray-500 uppercase tracking-wider">วัน-เวลา</th>
                                    <th class="px-6 py-5 text-xs font-bold text-gray-500 uppercase tracking-wider">เครื่องจักร</th>
                                    <th class="px-6 py-5 text-xs font-bold text-center text-gray-500 uppercase tracking-wider">สถานะ</th>
                                    <th class="px-6 py-5 text-xs font-bold text-right text-gray-500 uppercase tracking-wider">ยอดเงิน</th>
                                    <th class="px-6 py-5 text-xs font-bold text-center text-gray-500 uppercase tracking-wider">ดำเนินการ</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-100">
                                @foreach ($bookings as $booking)
                                    <tr class="hover:bg-green-50/30 transition duration-200 group">
                                        <td class="px-8 py-5">
                                            <a href="{{ route('customer.booking.show', $booking->id) }}" class="font-bold text-green-700 hover:underline group-hover:text-green-800">
                                                #{{ $booking->job_number }}
                                            </a>
                                        </td>
                                        <td class="px-6 py-5">
                                            <div class="flex flex-col">
                                                <span class="font-semibold text-gray-700">{{ \Carbon\Carbon::parse($booking->scheduled_start)->format('d/m/Y') }}</span>
                                                <span class="text-xs text-gray-400">{{ \Carbon\Carbon::parse($booking->scheduled_start)->format('H:i') }} น.</span>
                                            </div>
                                        </td>
                                        <td class="px-6 py-5">
                                            <div class="flex items-center gap-2">
                                                <div class="w-8 h-8 rounded-full bg-gray-100 flex items-center justify-center text-gray-400">
                                                    <i class="fa-solid fa-tractor"></i>
                                                </div>
                                                <span class="text-gray-700 font-medium">{{ $booking->equipment->name ?? '-' }}</span>
                                            </div>
                                        </td>
                                        <td class="px-6 py-5 text-center">
                                            @php
                                                // Re-use logic for consistency
                                                $statusConfig = match ($booking->status) {
                                                    'completed' => ['bg' => 'bg-green-100', 'text' => 'text-green-700', 'label' => 'เสร็จสิ้น'],
                                                    'in_progress' => ['bg' => 'bg-blue-100', 'text' => 'text-blue-700', 'label' => 'กำลังทำ'],
                                                    'scheduled' => ['bg' => 'bg-yellow-100', 'text' => 'text-yellow-700', 'label' => 'รอคิว'],
                                                    'cancelled' => ['bg' => 'bg-red-100', 'text' => 'text-red-700', 'label' => 'ยกเลิก'],
                                                    default => ['bg' => 'bg-gray-100', 'text' => 'text-gray-700', 'label' => $booking->status],
                                                };
                                            @endphp
                                            <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-bold border border-transparent {{ $statusConfig['bg'] }} {{ $statusConfig['text'] }}">
                                                {{ $statusConfig['label'] }}
                                            </span>
                                            
                                            {{-- Desktop Payment Button in Status Column (Optional, or keep in Actions) --}}
                                            @if ($booking->status != 'cancelled')
                                                @if ($booking->payment_status == 'pending')
                                                    <div class="mt-2">
                                                        <a href="{{ route('customer.booking.payment', $booking->id) }}" class="text-[10px] font-bold text-white bg-green-600 hover:bg-green-700 px-2 py-0.5 rounded shadow animate-pulse">
                                                            จ่ายเงิน
                                                        </a>
                                                    </div>
                                                @elseif($booking->payment_status == 'pending_approval')
                                                    <div class="mt-1 text-[10px] text-orange-500">รอตรวจสลิป</div>
                                                @endif
                                            @endif
                                        </td>
                                        <td class="px-6 py-5 text-right font-bold text-gray-700">
                                            {{ number_format($booking->total_price, 2) }}
                                        </td>
                                        <td class="px-6 py-5 text-center">
                                            <a href="{{ route('customer.booking.show', $booking->id) }}" 
                                               class="w-10 h-10 inline-flex items-center justify-center rounded-full bg-gray-50 text-gray-400 hover:bg-green-100 hover:text-green-600 transition shadow-sm"
                                               title="ดูรายละเอียด">
                                                <i class="fa-solid fa-chevron-right"></i>
                                            </a>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @endif
            </div>
        </div>
    </div>
@endsection