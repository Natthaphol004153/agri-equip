@extends('layouts.customer')

@section('content')
<div class="max-w-4xl mx-auto space-y-6">

    {{-- Breadcrumb --}}
    <a href="{{ route('customer.dashboard') }}" class="inline-flex items-center text-sm text-gray-500 hover:text-agri-primary transition mb-2">
        <i class="fa-solid fa-arrow-left mr-2"></i> กลับไปหน้าหลัก
    </a>

    {{-- Header Status Card --}}
    <div class="bg-white rounded-2xl p-6 shadow-sm border border-gray-100 flex flex-col md:flex-row justify-between items-start md:items-center gap-4">
        <div>
            <h1 class="text-2xl font-bold text-gray-800 flex items-center gap-2">
                ใบงาน #{{ $booking->job_number }}
            </h1>
            <p class="text-sm text-gray-500 mt-1">
                <i class="fa-regular fa-calendar mr-1"></i> วันที่จอง: {{ \Carbon\Carbon::parse($booking->scheduled_start)->format('d/m/Y H:i') }}
            </p>
        </div>
        
        <div class="flex items-center gap-3">
            @php
                $statusConfig = match($booking->status) {
                    'completed' => ['bg' => 'bg-green-100', 'text' => 'text-green-700', 'label' => 'เสร็จสิ้น', 'icon' => 'fa-check-circle'],
                    'in_progress' => ['bg' => 'bg-blue-100', 'text' => 'text-blue-700', 'label' => 'กำลังดำเนินการ', 'icon' => 'fa-spinner fa-spin'],
                    'scheduled' => ['bg' => 'bg-yellow-100', 'text' => 'text-yellow-700', 'label' => 'รอคิว', 'icon' => 'fa-clock'],
                    'cancelled' => ['bg' => 'bg-red-100', 'text' => 'text-red-700', 'label' => 'ยกเลิก', 'icon' => 'fa-ban'],
                    default => ['bg' => 'bg-gray-100', 'text' => 'text-gray-700', 'label' => $booking->status, 'icon' => 'fa-circle']
                };
            @endphp
            <div class="px-4 py-2 rounded-xl {{ $statusConfig['bg'] }} {{ $statusConfig['text'] }} font-bold text-sm flex items-center gap-2">
                <i class="fa-solid {{ $statusConfig['icon'] }}"></i> {{ $statusConfig['label'] }}
            </div>
        </div>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
        
        {{-- Left Column: รายละเอียดงาน --}}
        <div class="md:col-span-2 space-y-6">
            {{-- สถานที่ให้บริการ --}}
            <div class="bg-white rounded-2xl p-6 shadow-sm border border-gray-100">
                <h3 class="font-bold text-gray-800 mb-4 pb-2 border-b border-gray-100 flex items-center gap-2">
                    <i class="fa-solid fa-map-location-dot text-agri-primary"></i> สถานที่ให้บริการ
                </h3>
                <div class="space-y-2 text-sm text-gray-600">
                    <p>{{ $booking->customer->work_location_address ?? $booking->customer->address ?? 'ไม่ระบุที่อยู่' }}</p>
                    @php
                        $manualWorkMapUrl = $booking->customer->work_map_url ?? null;
                        $workLat = $booking->customer->work_latitude ?? null;
                        $workLng = $booking->customer->work_longitude ?? null;
                        $workAddress = $booking->customer->work_location_address ?? null;

                        $customerMapLink = !empty($manualWorkMapUrl)
                            ? $manualWorkMapUrl
                            : ((!empty($workLat) && !empty($workLng))
                                ? "https://maps.google.com/maps?q={$workLat},{$workLng}"
                                : (isset($booking->customer->latitude)
                                    ? "https://maps.google.com/maps?q={$booking->customer->latitude},{$booking->customer->longitude}"
                                    : "https://maps.google.com/maps?q=" . urlencode($workAddress ?: ($booking->customer->address ?? ''))));
                    @endphp
                    <a href="{{ $customerMapLink }}" target="_blank"
                        class="inline-flex items-center gap-2 text-blue-600 font-medium hover:underline">
                        <i class="fa-solid fa-location-dot"></i> เปิด Google Maps
                    </a>
                </div>
            </div>

            {{-- ข้อมูลเครื่องจักร --}}
            <div class="bg-white rounded-2xl p-6 shadow-sm border border-gray-100">
                <h3 class="font-bold text-gray-800 mb-4 pb-2 border-b border-gray-100 flex items-center gap-2">
                    <i class="fa-solid fa-tractor text-agri-primary"></i> เครื่องจักร/บริการ
                </h3>
                <div class="flex items-center gap-4">
                    <div class="w-16 h-16 rounded-xl bg-gray-50 flex items-center justify-center border border-gray-100 text-gray-400 text-2xl">
                        <i class="fa-solid fa-wrench"></i>
                    </div>
                    <div>
                        <h4 class="font-bold text-lg text-gray-800">{{ $booking->equipment->name ?? 'ไม่ระบุ' }}</h4>
                        <p class="text-sm text-gray-500">รหัส: {{ $booking->equipment->equipment_code ?? '-' }}</p>
                    </div>
                </div>
            </div>

            {{-- ข้อมูลพนักงาน (ถ้ามี) --}}
            @if($booking->assignedStaff)
            <div class="bg-white rounded-2xl p-6 shadow-sm border border-gray-100">
                <h3 class="font-bold text-gray-800 mb-4 pb-2 border-b border-gray-100 flex items-center gap-2">
                    <i class="fa-solid fa-user-gear text-agri-primary"></i> เจ้าหน้าที่ดูแล
                </h3>
                <div class="flex items-center gap-4">
                    <div class="w-12 h-12 rounded-full bg-blue-50 flex items-center justify-center text-blue-600 font-bold border border-blue-100">
                        {{ substr($booking->assignedStaff->name, 0, 1) }}
                    </div>
                    <div>
                        <h4 class="font-bold text-gray-800">{{ $booking->assignedStaff->name }}</h4>
                        <p class="text-xs text-gray-500">พนักงานปฏิบัติงาน</p>
                    </div>
                </div>
            </div>
            @endif

            {{-- ไทม์ไลน์ (ถ้ามี Activities) --}}
            {{-- (สามารถเพิ่ม Loop activities ตรงนี้ได้ถ้ามีข้อมูล) --}}
        </div>

        {{-- Right Column: สรุปยอด & จ่ายเงิน --}}
        <div class="space-y-6">
            <div class="bg-white rounded-2xl p-6 shadow-sm border border-gray-100">
                <h3 class="font-bold text-gray-800 mb-4 pb-2 border-b border-gray-100">สรุปค่าใช้จ่าย</h3>
                
                <div class="flex justify-between items-center mb-2 text-sm">
                    <span class="text-gray-500">ค่าบริการรวม</span>
                    <span class="font-bold">{{ number_format($booking->total_price, 2) }} ฿</span>
                </div>
                
                @if($booking->deposit_amount > 0)
                <div class="flex justify-between items-center mb-2 text-sm text-green-600">
                    <span>มัดจำแล้ว</span>
                    <span>-{{ number_format($booking->deposit_amount, 2) }} ฿</span>
                </div>
                @endif

                <div class="mt-4 pt-4 border-t border-gray-100 flex justify-between items-center">
                    <span class="font-bold text-gray-800">ยอดสุทธิ</span>
                    <span class="font-bold text-xl text-agri-primary">{{ number_format($booking->total_price - $booking->deposit_amount, 2) }} ฿</span>
                </div>

                {{-- ปุ่ม Action จ่ายเงิน --}}
                <div class="mt-6">
                    @if($booking->status != 'cancelled')
                        @if($booking->payment_status == 'pending')
                            <a href="{{ route('customer.booking.payment', $booking->id) }}" class="block w-full py-3 bg-agri-primary text-white text-center rounded-xl font-bold hover:bg-green-700 transition shadow-lg shadow-green-200">
                                <i class="fa-solid fa-qrcode mr-2"></i> ชำระเงินทันที
                            </a>
                        @elseif($booking->payment_status == 'pending_approval')
                            <div class="w-full py-3 bg-orange-50 text-orange-600 text-center rounded-xl font-bold border border-orange-200">
                                <i class="fa-solid fa-clock mr-2"></i> รอตรวจสอบสลิป
                            </div>
                        @elseif($booking->payment_status == 'paid' || $booking->payment_status == 'deposit_paid')
                            <div class="w-full py-3 bg-green-50 text-green-600 text-center rounded-xl font-bold border border-green-200">
                                <i class="fa-solid fa-check-circle mr-2"></i> ชำระเงินเรียบร้อย
                            </div>
                        @endif
                    @endif
                </div>
            </div>

            {{-- รูปผลงาน (ถ้ามี) --}}
            @if($booking->image_path)
            <div class="bg-white rounded-2xl p-6 shadow-sm border border-gray-100">
                <h3 class="font-bold text-gray-800 mb-4">รูปผลงาน</h3>
                <a href="{{ asset('storage/' . $booking->image_path) }}" target="_blank">
                    <img src="{{ asset('storage/' . $booking->image_path) }}" class="w-full rounded-xl hover:opacity-90 transition border border-gray-200">
                </a>
            </div>
            @endif
        </div>
    </div>
</div>
@endsection