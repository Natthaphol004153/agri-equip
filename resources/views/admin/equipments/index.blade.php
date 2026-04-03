@extends('layouts.admin')

@section('title', 'จัดการเครื่องจักร')
@section('header', 'คลังเครื่องจักร (Equipments)')

@section('content')
<div class="max-w-7xl mx-auto">

    {{-- Header & Action --}}
    <div class="flex flex-col md:flex-row justify-between items-center mb-6 gap-4">
        <div>
            <h2 class="text-xl font-bold text-gray-800 flex items-center gap-2">
                <i class="fa-solid fa-tractor text-agri-primary"></i> รายการเครื่องจักรทั้งหมด
            </h2>
            <p class="text-sm text-gray-500">จัดการข้อมูลและติดตามสถานะการใช้งาน</p>
        </div>
        <a href="{{ route('admin.equipments.create') }}" class="bg-agri-primary text-white px-5 py-2.5 rounded-xl shadow-lg shadow-agri-primary/30 hover:bg-agri-hover hover:-translate-y-0.5 transition flex items-center gap-2 font-medium">
            <i class="fa-solid fa-plus"></i> เพิ่มเครื่องจักรใหม่
        </a>
    </div>

    {{-- Table Card --}}
    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-sm text-left">
                <thead class="text-xs text-gray-500 uppercase bg-gray-50 border-b border-gray-100">
                    <tr>
                        <th class="px-6 py-4 font-medium">เครื่องจักร</th>
                        <th class="px-6 py-4 font-medium">ประเภท</th>
                        <th class="px-6 py-4 font-medium text-center">มิเตอร์ใช้งาน / รอบซ่อม</th>
                        <th class="px-6 py-4 font-medium text-center">สถานะ</th>
                        <th class="px-6 py-4 font-medium text-right">จัดการ</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse($equipments as $eq)
                        <tr class="hover:bg-blue-50/30 transition duration-150">
                            {{-- เครื่องจักร (รูป + ชื่อ) --}}
                            <td class="px-6 py-4">
                                <div class="flex items-center gap-4">
                                    <div class="w-14 h-14 rounded-xl bg-gray-100 border border-gray-200 overflow-hidden shrink-0">
                                        @if($eq->image_path)
                                            <img src="{{ asset($eq->image_path) }}" class="w-full h-full object-cover">
                                        @else
                                            <div class="flex items-center justify-center h-full text-gray-400">
                                                <i class="fa-solid fa-image text-xl"></i>
                                            </div>
                                        @endif
                                    </div>
                                    <div>
                                        <div class="font-bold text-gray-800 text-base mb-0.5">{{ $eq->name }}</div>
                                        <div class="flex items-center gap-2">
                                            <span class="text-[10px] font-bold bg-gray-100 text-gray-500 px-1.5 py-0.5 rounded border border-gray-200">
                                                {{ $eq->equipment_code }}
                                            </span>
                                            <span class="text-xs text-gray-400">
                                                {{ $eq->registration_number ?? '-' }}
                                            </span>
                                        </div>
                                    </div>
                                </div>
                            </td>

                            {{-- ประเภท --}}
                            <td class="px-6 py-4">
                                @php
                                    $typeConfig = match($eq->type) {
                                        'drone' => ['icon' => 'fa-plane-up', 'label' => 'โดรน', 'color' => 'text-blue-500 bg-blue-50'],
                                        'tractor' => ['icon' => 'fa-tractor', 'label' => 'รถไถ', 'color' => 'text-orange-500 bg-orange-50'],
                                        'harvester' => ['icon' => 'fa-wheat-awn', 'label' => 'รถเกี่ยว', 'color' => 'text-green-600 bg-green-50'],
                                        'sprayer' => ['icon' => 'fa-spray-can', 'label' => 'รถพ่นยา', 'color' => 'text-purple-500 bg-purple-50'],
                                        default => ['icon' => 'fa-cogs', 'label' => ($eq->custom_type_name ?: 'อื่นๆ'), 'color' => 'text-gray-500 bg-gray-50'],
                                    };
                                @endphp
                                <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-lg text-xs font-bold {{ $typeConfig['color'] }}">
                                    <i class="fa-solid {{ $typeConfig['icon'] }}"></i> {{ $typeConfig['label'] }}
                                </span>
                            </td>

                            {{-- สถานะการซ่อมบำรุง (Progress Bar) --}}
                            <td class="px-6 py-4 align-middle">
                                @php
                                    $isKmTracking = $eq->tracking_type === 'kilometers';
                                    $currentMeter = $isKmTracking ? ($eq->current_kilometers ?? 0) : ($eq->current_hours ?? 0);
                                    $maintenanceThreshold = $isKmTracking ? ($eq->maintenance_km_threshold ?? 0) : ($eq->maintenance_hour_threshold ?? 0);
                                    $meterUnit = $isKmTracking ? 'กม.' : 'ชม.';
                                    $percent = $maintenanceThreshold > 0 ? ($currentMeter / $maintenanceThreshold) * 100 : 0;
                                    $barColor = 'bg-green-500';
                                    if($percent > 70) $barColor = 'bg-yellow-400';
                                    if($percent >= 100) $barColor = 'bg-red-500';
                                @endphp
                                <div class="w-full max-w-[140px] mx-auto">
                                    <div class="flex justify-between text-xs mb-1">
                                        <span class="font-bold text-gray-700">{{ number_format($currentMeter) }} {{ $meterUnit }}</span>
                                        <span class="text-gray-400">/ {{ number_format($maintenanceThreshold) }}</span>
                                    </div>
                                    <div class="w-full bg-gray-200 rounded-full h-1.5 overflow-hidden">
                                        <div class="{{ $barColor }} h-1.5 rounded-full" style="width: {{ min($percent, 100) }}%"></div>
                                    </div>
                                    @if($percent >= 100)
                                        <p class="text-[10px] text-red-500 font-bold mt-1 text-center"><i class="fa-solid fa-wrench"></i> ถึงรอบซ่อม!</p>
                                    @endif
                                </div>
                            </td>

                            {{-- สถานะปัจจุบัน --}}
                            <td class="px-6 py-4 text-center">
                                @php
                                    $status = match($eq->current_status) {
                                        'available' => ['label' => 'ว่าง', 'bg' => 'bg-green-100', 'text' => 'text-green-700', 'dot' => 'bg-green-500'],
                                        'in_use' => ['label' => 'กำลังใช้งาน', 'bg' => 'bg-blue-100', 'text' => 'text-blue-700', 'dot' => 'bg-blue-500 animate-pulse'],
                                        'maintenance' => ['label' => 'ซ่อมบำรุง', 'bg' => 'bg-yellow-100', 'text' => 'text-yellow-800', 'dot' => 'bg-yellow-500'],
                                        'breakdown' => ['label' => 'เสีย/งดใช้', 'bg' => 'bg-red-100', 'text' => 'text-red-700', 'dot' => 'bg-red-500'],
                                        default => ['label' => $eq->current_status, 'bg' => 'bg-gray-100', 'text' => 'text-gray-600', 'dot' => 'bg-gray-400'],
                                    };
                                @endphp
                                <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-bold border border-opacity-10 {{ $status['bg'] }} {{ $status['text'] }}">
                                    <span class="w-1.5 h-1.5 rounded-full {{ $status['dot'] }}"></span>
                                    {{ $status['label'] }}
                                </span>
                            </td>

                            {{-- จัดการ (แสดงตลอดเวลา ไม่ Hidden แล้ว) --}}
                            <td class="px-6 py-4 text-right">
                                <div class="flex items-center justify-end gap-2">
                                    @if($eq->current_status !== 'maintenance')
                                        <form action="{{ route('admin.maintenance.start', $eq->id) }}" method="POST"
                                            onsubmit="return confirm('ส่ง {{ $eq->name }} เข้าซ่อมทันทีใช่ไหม?');">
                                            @csrf
                                            <input type="hidden" name="description" value="ส่งเข้าซ่อมจากหน้าคลังเครื่องจักร (Quick Action)">
                                            <button type="submit"
                                                class="w-8 h-8 flex items-center justify-center rounded-lg border border-yellow-200 text-yellow-700 hover:text-yellow-800 hover:border-yellow-400 hover:bg-yellow-50 transition"
                                                title="ส่งเข้าซ่อมทันที">
                                                <i class="fa-solid fa-screwdriver-wrench"></i>
                                            </button>
                                        </form>
                                    @else
                                        <span
                                            class="w-8 h-8 inline-flex items-center justify-center rounded-lg border border-yellow-200 bg-yellow-50 text-yellow-500"
                                            title="กำลังซ่อมอยู่แล้ว">
                                            <i class="fa-solid fa-screwdriver-wrench"></i>
                                        </span>
                                    @endif

                                    <a href="{{ route('admin.equipments.show', $eq->id) }}" class="w-8 h-8 flex items-center justify-center rounded-lg border border-gray-200 text-gray-500 hover:text-blue-600 hover:border-blue-600 hover:bg-blue-50 transition" title="รายละเอียด">
                                        <i class="fa-solid fa-eye"></i>
                                    </a>
                                    <a href="{{ route('admin.equipments.edit', $eq->id) }}" class="w-8 h-8 flex items-center justify-center rounded-lg border border-gray-200 text-gray-500 hover:text-orange-500 hover:border-orange-500 hover:bg-orange-50 transition" title="แก้ไข">
                                        <i class="fa-solid fa-pen"></i>
                                    </a>
                                    <form action="{{ route('admin.equipments.destroy', $eq->id) }}" method="POST" onsubmit="return confirm('ยืนยันลบเครื่องจักร {{ $eq->name }}?');">
                                        @csrf @method('DELETE')
                                        <button type="submit" class="w-8 h-8 flex items-center justify-center rounded-lg border border-gray-200 text-gray-400 hover:text-red-600 hover:border-red-200 hover:bg-red-50 transition" title="ลบ">
                                            <i class="fa-solid fa-trash"></i>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="text-center py-12">
                                <div class="flex flex-col items-center justify-center text-gray-300">
                                    <i class="fa-solid fa-tractor text-6xl mb-4 bg-gray-50 p-4 rounded-full"></i>
                                    <p class="text-gray-500 font-medium">ยังไม่มีเครื่องจักรในระบบ</p>
                                    <p class="text-xs text-gray-400 mt-1">เริ่มเพิ่มเครื่องจักรเพื่อเริ่มใช้งาน</p>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        
        {{-- Pagination --}}
        @if($equipments->hasPages())
            <div class="px-6 py-4 border-t border-gray-100">
                {{ $equipments->links() }}
            </div>
        @endif
    </div>
</div>
@endsection