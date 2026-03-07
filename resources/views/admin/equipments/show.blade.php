@extends('layouts.admin')

@section('title', 'รายละเอียดเครื่องจักร')
@section('header', 'ข้อมูล: ' . $equipment->name)

@section('content')
<div class="max-w-6xl mx-auto space-y-6">
    @php
        $isKmTracking = $equipment->tracking_type === 'kilometers';
        $currentMeter = $isKmTracking ? ($equipment->current_kilometers ?? 0) : ($equipment->current_hours ?? 0);
        $thresholdMeter = $isKmTracking ? ($equipment->maintenance_km_threshold ?? 0) : ($equipment->maintenance_hour_threshold ?? 0);
        $meterUnit = $isKmTracking ? 'กม.' : 'ชม.';
    @endphp
    
    {{-- Header Action --}}
    <div class="flex justify-between items-center">
        <a href="{{ route('admin.equipments.index') }}" class="text-gray-500 hover:text-gray-700 text-sm flex items-center gap-1 transition">
            <i class="fa-solid fa-arrow-left"></i> กลับหน้ารายการ
        </a>
        <a href="{{ route('admin.equipments.edit', $equipment->id) }}" class="bg-orange-500 text-white px-4 py-2 rounded-lg hover:bg-orange-600 transition shadow-sm flex items-center gap-2 text-sm font-bold">
            <i class="fa-solid fa-pen-to-square"></i> แก้ไขข้อมูล
        </a>
    </div>

    {{-- 🔵 ส่วนที่ 1: ข้อมูลหลัก และ สถิติ --}}
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        
        {{-- Card: รูปภาพและข้อมูลพื้นฐาน --}}
        <div class="lg:col-span-1 bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
            <div class="aspect-video w-full bg-gray-50 flex items-center justify-center border-b border-gray-100">
                @if($equipment->image_path)
                    <img src="{{ asset($equipment->image_path) }}" class="w-full h-full object-cover">
                @else
                    <div class="text-gray-400 text-center">
                        <i class="fa-solid fa-image text-4xl mb-2 opacity-50"></i>
                        <p class="text-sm">ไม่มีรูปภาพ</p>
                    </div>
                @endif
            </div>
            <div class="p-6 space-y-4">
                <div>
                    <h2 class="text-xl font-bold text-gray-800">{{ $equipment->name }}</h2>
                    <span class="inline-block bg-blue-100 text-blue-700 text-xs px-2 py-0.5 rounded font-bold mt-1">
                        {{ $equipment->equipment_code }}
                    </span>
                </div>
                <div class="space-y-2 text-sm">
                    <div class="flex justify-between border-b border-gray-50 pb-2">
                        <span class="text-gray-500">ประเภท</span>
                        <span class="font-medium text-gray-800">{{ $equipment->type === 'other' ? ($equipment->custom_type_name ?: 'อื่นๆ') : ucfirst($equipment->type) }}</span>
                    </div>
                    <div class="flex justify-between border-b border-gray-50 pb-2">
                        <span class="text-gray-500">ทะเบียน</span>
                        <span class="font-medium text-gray-800">{{ $equipment->registration_number ?? '-' }}</span>
                    </div>
                    <div class="flex justify-between border-b border-gray-50 pb-2">
                        <span class="text-gray-500">ค่าเช่า/ไร่</span>
                        <span class="font-medium text-green-600">{{ number_format($equipment->price_per_rai ?? 0, 2) }} บาท</span>
                    </div>
                </div>
            </div>
        </div>

        {{-- Card: สถิติการเงินและการใช้งาน --}}
        <div class="lg:col-span-2 space-y-6">
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                {{-- Stat 1 --}}
                <div class="bg-white p-5 rounded-2xl border border-gray-100 shadow-sm flex items-center gap-4">
                    <div class="w-12 h-12 rounded-full bg-green-100 flex items-center justify-center text-green-600 text-xl">
                        <i class="fa-solid fa-sack-dollar"></i>
                    </div>
                    <div>
                        <p class="text-xs text-gray-500">รายได้รวม</p>
                        <p class="text-xl font-bold text-gray-800">{{ number_format($totalEarnings) }} ฿</p>
                    </div>
                </div>
                {{-- Stat 2 --}}
                <div class="bg-white p-5 rounded-2xl border border-gray-100 shadow-sm flex items-center gap-4">
                    <div class="w-12 h-12 rounded-full bg-red-100 flex items-center justify-center text-red-600 text-xl">
                        <i class="fa-solid fa-wrench"></i>
                    </div>
                    <div>
                        <p class="text-xs text-gray-500">ค่าซ่อมรวม</p>
                        <p class="text-xl font-bold text-gray-800">{{ number_format($totalMaintenanceCost) }} ฿</p>
                    </div>
                </div>
                {{-- Stat 3 --}}
                <div class="bg-white p-5 rounded-2xl border border-gray-100 shadow-sm flex items-center gap-4">
                    <div class="w-12 h-12 rounded-full bg-blue-100 flex items-center justify-center text-blue-600 text-xl">
                        <i class="fa-solid fa-clock"></i>
                    </div>
                    <div>
                        <p class="text-xs text-gray-500">มิเตอร์ใช้งาน</p>
                        <p class="text-xl font-bold text-gray-800">{{ number_format($currentMeter, 2) }} {{ $meterUnit }}</p>
                        <p class="text-[10px] text-gray-400">ครบกำหนดที่ {{ number_format($thresholdMeter, 2) }} {{ $meterUnit }}</p>
                    </div>
                </div>
            </div>

            {{-- 🟠 ส่วนที่ 2: ประวัติการซ่อมบำรุง --}}
            <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-6">
                <h3 class="font-bold text-gray-800 mb-4 flex items-center gap-2">
                    <i class="fa-solid fa-clipboard-list text-agri-primary"></i> ประวัติการซ่อมบำรุงล่าสุด
                </h3>
                <div class="overflow-x-auto">
                    <table class="w-full text-sm text-left">
                        <thead class="text-gray-500 border-b border-gray-100 bg-gray-50/50">
                            <tr>
                                <th class="py-3 px-4">วันที่</th>
                                <th class="py-3 px-4">รายการ</th>
                                <th class="py-3 px-4">ค่าใช้จ่าย</th>
                                <th class="py-3 px-4">สถานะ</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-50">
                            @forelse($maintenanceHistory->take(5) as $log)
                                <tr class="hover:bg-gray-50 transition">
                                    <td class="py-3 px-4">{{ $log->created_at->format('d/m/Y') }}</td>
                                    <td class="py-3 px-4">{{ Str::limit($log->description, 30) }}</td>
                                    <td class="py-3 px-4 font-medium text-red-600">-{{ number_format($log->total_cost) }}</td>
                                    <td class="py-3 px-4">
                                        <span class="px-2 py-1 rounded text-xs font-bold 
                                            {{ $log->completion_date ? 'bg-green-100 text-green-700' : 'bg-yellow-100 text-yellow-700' }}">
                                            {{ $log->completion_date ? 'เสร็จสิ้น' : 'กำลังซ่อม' }}
                                        </span>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="4" class="py-4 text-center text-gray-400">ยังไม่มีประวัติการซ่อม</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                @if($maintenanceHistory->count() > 5)
                <div class="mt-4 text-center">
                    <a href="{{ route('admin.maintenance.index') }}" class="text-sm text-agri-primary hover:underline">ดูประวัติทั้งหมด</a>
                </div>
                @endif
            </div>

            {{-- 🟢 ส่วนที่ 3: ประวัติการทำงาน --}}
            <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-6">
                <h3 class="font-bold text-gray-800 mb-4 flex items-center gap-2">
                    <i class="fa-solid fa-briefcase text-agri-primary"></i> ประวัติงานล่าสุด
                </h3>
                <div class="overflow-x-auto">
                    <table class="w-full text-sm text-left">
                        <thead class="text-gray-500 border-b border-gray-100 bg-gray-50/50">
                            <tr>
                                <th class="py-3 px-4">วันที่เริ่ม</th>
                                <th class="py-3 px-4">ลูกค้า</th>
                                <th class="py-3 px-4">พนักงานขับ</th>
                                <th class="py-3 px-4">รายได้</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-50">
                            @forelse($jobHistory as $job)
                                <tr class="hover:bg-gray-50 transition">
                                    <td class="py-3 px-4">{{ \Carbon\Carbon::parse($job->scheduled_start)->format('d/m/Y') }}</td>
                                    <td class="py-3 px-4">{{ $job->customer->name ?? '-' }}</td>
                                    <td class="py-3 px-4">{{ $job->assignedStaff->name ?? '-' }}</td>
                                    <td class="py-3 px-4 font-medium text-green-600">+{{ number_format($job->total_price) }}</td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="4" class="py-4 text-center text-gray-400">ยังไม่มีประวัติงาน</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

        </div>
    </div>
</div>
@endsection