@extends('layouts.admin')

@section('title', 'Maintenance Management')
@section('header', 'ระบบจัดการซ่อมบำรุง')

@section('content')
<div class="space-y-6" x-data="{ activeModal: null }">

    {{-- Header & Actions --}}
    <div class="flex flex-col md:flex-row justify-between items-center gap-4 bg-white p-4 rounded-2xl shadow-sm border border-gray-100">
        <div>
            <h2 class="text-xl font-bold text-gray-800 flex items-center gap-2">
                <i class="fa-solid fa-screwdriver-wrench text-agri-primary"></i> ภาพรวมงานซ่อมบำรุง
            </h2>
            <p class="text-sm text-gray-500">ติดตามสถานะการแจ้งซ่อม เช็คระยะ และประวัติการซ่อมบำรุง</p>
        </div>
        <a href="{{ route('admin.maintenance.create') }}" class="bg-agri-primary text-white px-5 py-2.5 rounded-xl shadow-lg shadow-agri-primary/30 hover:bg-agri-hover hover:-translate-y-0.5 transition flex items-center gap-2 font-bold text-sm">
            <i class="fa-solid fa-plus-circle"></i> เปิดใบงานซ่อมใหม่
        </a>
    </div>

    {{-- 1. รายการที่พนักงานแจ้งเข้ามา (Pending Requests) --}}
    @if(isset($reportedIssues) && $reportedIssues->count() > 0)
    <div class="bg-white rounded-2xl shadow-sm border border-red-100 overflow-hidden relative">
        <div class="absolute top-0 left-0 w-1 h-full bg-red-500"></div>
        <div class="bg-red-50/50 px-6 py-4 border-b border-red-100 flex items-center justify-between">
            <h3 class="font-bold text-red-800 flex items-center gap-2">
                <div class="w-8 h-8 rounded-full bg-red-100 flex items-center justify-center animate-pulse">
                    <i class="fa-solid fa-bell text-red-600"></i>
                </div>
                รายการแจ้งซ่อมใหม่ ({{ $reportedIssues->count() }})
            </h3>
            <span class="text-xs font-bold bg-white text-red-600 border border-red-200 px-3 py-1 rounded-full shadow-sm">ด่วน</span>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-sm text-left">
                <thead class="bg-gray-50 text-gray-500 font-medium border-b border-gray-100">
                    <tr>
                        <th class="px-6 py-3">วันที่แจ้ง</th>
                        <th class="px-6 py-3">เครื่องจักร</th>
                        <th class="px-6 py-3">รายละเอียดอาการ</th>
                        <th class="px-6 py-3 text-right">จัดการ</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @foreach($reportedIssues as $issue)
                    <tr class="hover:bg-red-50/30 transition">
                        <td class="px-6 py-4 text-gray-600">
                            {{ \Carbon\Carbon::parse($issue->created_at)->format('d/m/Y H:i') }}
                        </td>
                        <td class="px-6 py-4">
                            <div class="font-bold text-gray-800">{{ $issue->equipment->name ?? '-' }}</div>
                            <span class="text-xs bg-gray-100 text-gray-500 px-2 py-0.5 rounded border border-gray-200">{{ $issue->equipment->equipment_code ?? '-' }}</span>
                        </td>
                        <td class="px-6 py-4 text-red-600 font-medium">
                            "{{ $issue->description }}"
                        </td>
                        <td class="px-6 py-4 text-right">
                            <a href="{{ route('admin.maintenance.accept_form', $issue->id) }}" class="inline-flex items-center gap-2 bg-red-600 text-white px-4 py-1.5 rounded-lg text-xs font-bold hover:bg-red-700 transition shadow-sm">
                                <i class="fa-solid fa-tools"></i> รับเรื่อง
                            </a>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
    @endif

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        
        {{-- 2. ถึงกำหนดเช็คระยะ (Preventive Maintenance) --}}
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 flex flex-col h-full overflow-hidden">
            <div class="px-6 py-4 border-b border-gray-100 bg-yellow-50/30 flex justify-between items-center">
                <h3 class="font-bold text-gray-800 flex items-center gap-2">
                    <i class="fa-solid fa-clock text-yellow-500"></i> ถึงกำหนดเช็คระยะ ({{ isset($needMaintenance) ? $needMaintenance->count() : 0 }})
                </h3>
            </div>
            <div class="flex-1 overflow-x-auto">
                <table class="w-full text-sm text-left">
                    <tbody class="divide-y divide-gray-100">
                        @forelse($needMaintenance ?? [] as $eq)
                        <tr class="group hover:bg-yellow-50/20 transition">
                            <td class="px-6 py-4">
                                <div class="font-bold text-gray-800">{{ $eq->name }}</div>
                                <span class="text-xs text-gray-400">{{ $eq->equipment_code }}</span>
                            </td>
                            <td class="px-6 py-4">
                                <div class="flex items-center gap-2">
                                    <div class="w-full bg-gray-200 rounded-full h-1.5 w-24">
                                        <div class="bg-yellow-500 h-1.5 rounded-full" style="width: {{ min(100, ($eq->current_hours / ($eq->maintenance_hour_threshold ?: 1)) * 100) }}%"></div>
                                    </div>
                                    <span class="text-xs font-bold text-red-600 whitespace-nowrap">{{ number_format($eq->current_hours, 1) }}/{{ $eq->maintenance_hour_threshold }} ชม.</span>
                                </div>
                            </td>
                            <td class="px-6 py-4 text-right">
                                <form action="{{ route('admin.maintenance.start', $eq->id) }}" method="POST">
                                    @csrf
                                    <button class="text-xs bg-gray-800 text-white px-3 py-1.5 rounded-lg hover:bg-black transition font-medium shadow-sm">
                                        ส่งเช็ค
                                    </button>
                                </form>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="3" class="px-6 py-8 text-center text-gray-400 text-sm">
                                <div class="flex flex-col items-center">
                                    <i class="fa-regular fa-circle-check text-3xl mb-2 text-green-400"></i>
                                    <span>เครื่องจักรทุกเครื่องสภาพดี</span>
                                </div>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        {{-- 3. กำลังดำเนินการซ่อม (In Progress) --}}
        <div class="bg-gray-50 rounded-2xl border border-gray-200 flex flex-col h-full p-4">
            <div class="flex justify-between items-center mb-4 px-1">
                <h3 class="font-bold text-gray-800 flex items-center gap-2">
                    <div class="w-6 h-6 rounded bg-blue-100 flex items-center justify-center">
                        <i class="fa-solid fa-wrench text-blue-600 text-xs"></i>
                    </div>
                    กำลังซ่อม ({{ isset($inMaintenance) ? $inMaintenance->count() : 0 }})
                </h3>
            </div>
            
            <div class="space-y-3 max-h-[400px] overflow-y-auto pr-2 custom-scrollbar">
                @forelse($inMaintenance ?? [] as $log)
                    <div class="bg-white p-4 rounded-xl shadow-sm border border-gray-100 group hover:border-blue-300 transition relative">
                        <div class="flex justify-between items-start mb-2">
                            <div>
                                <h4 class="font-bold text-gray-800 text-sm">{{ $log->equipment->name ?? 'Unknown' }}</h4>
                                <p class="text-[10px] text-gray-400 mt-0.5">
                                    <i class="fa-regular fa-calendar mr-1"></i> เริ่ม: {{ \Carbon\Carbon::parse($log->maintenance_date)->format('d/m/Y') }}
                                </p>
                            </div>
                            <span class="bg-blue-50 text-blue-600 text-[10px] font-bold px-2 py-1 rounded-md border border-blue-100">
                                In Progress
                            </span>
                        </div>
                        <p class="text-sm text-gray-600 border-l-2 border-gray-200 pl-2 mb-3 line-clamp-2">
                            {{ $log->description }}
                        </p>
                        
                        {{-- Trigger Modal --}}
                        <button @click="activeModal = {{ $log->id }}" class="w-full bg-green-50 text-green-700 border border-green-200 py-2 rounded-lg text-sm font-bold hover:bg-green-600 hover:text-white transition shadow-sm flex items-center justify-center gap-2">
                            <i class="fa-solid fa-check-circle"></i> แจ้งซ่อมเสร็จสิ้น
                        </button>

                        {{-- Modal Finish (Alpine.js) --}}
                        <div x-show="activeModal === {{ $log->id }}" style="display: none;" 
                             class="fixed inset-0 z-[60] flex items-center justify-center p-4 bg-black/60 backdrop-blur-sm"
                             x-transition.opacity>
                            
                            <div @click.away="activeModal = null" class="bg-white rounded-2xl shadow-2xl w-full max-w-md overflow-hidden animate-fade-in-up text-left">
                                <div class="bg-green-600 px-6 py-4 flex justify-between items-center text-white">
                                    <h5 class="font-bold text-lg"><i class="fa-solid fa-clipboard-check mr-2"></i> สรุปผลการซ่อม</h5>
                                    <button @click="activeModal = null" class="hover:bg-white/20 w-8 h-8 rounded-full flex items-center justify-center transition"><i class="fa-solid fa-xmark"></i></button>
                                </div>
                                
                                <form action="{{ route('admin.maintenance.finish', $log->id) }}" method="POST" class="p-6">
                                    @csrf
                                    <div class="space-y-4">
                                        <div>
                                            <label class="block text-sm font-bold text-gray-700 mb-1">เครื่องจักร</label>
                                            <div class="bg-gray-100 px-3 py-2 rounded-lg text-gray-600 text-sm font-medium">{{ $log->equipment->name ?? '-' }}</div>
                                        </div>
                                        
                                        {{-- ✅ แก้ไข: เปลี่ยน name="cost" เป็น "total_cost" --}}
                                        <div>
                                            <label class="block text-sm font-bold text-gray-700 mb-1">ค่าใช้จ่ายทั้งหมด (บาท) *</label>
                                            <div class="relative">
                                                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                                    <span class="text-gray-500">฿</span>
                                                </div>
                                                <input type="number" name="total_cost" required placeholder="0.00" min="0" step="0.01" class="pl-8 w-full border-gray-300 rounded-lg focus:ring-green-500 focus:border-green-500">
                                            </div>
                                        </div>
                                        
                                        {{-- ✅ แก้ไข: เปลี่ยน name="technician_name" เป็น "service_provider" --}}
                                        <div>
                                            <label class="block text-sm font-bold text-gray-700 mb-1">ผู้ซ่อม/ชื่ออู่ (Repairer) *</label>
                                            <input type="text" name="service_provider" required placeholder="เช่น ช่างสมชาย, อู่ช่างดำ" class="w-full border-gray-300 rounded-lg focus:ring-green-500 focus:border-green-500">
                                        </div>
                                        
                                        <div>
                                            <label class="block text-sm font-bold text-gray-700 mb-1">รายละเอียดการซ่อม (Note)</label>
                                            <textarea name="note" rows="2" placeholder="สิ่งที่ทำไป เช่น เปลี่ยนถ่ายน้ำมัน..." class="w-full border-gray-300 rounded-lg focus:ring-green-500 focus:border-green-500"></textarea>
                                        </div>
                                        
                                        <div class="bg-yellow-50 border border-yellow-100 p-3 rounded-lg flex items-start gap-3">
                                            <input type="checkbox" name="reset_hours" value="1" id="reset{{ $log->id }}" class="mt-1 rounded border-gray-300 text-green-600 focus:ring-green-500">
                                            <label for="reset{{ $log->id }}" class="text-sm text-gray-700 cursor-pointer">
                                                <span class="font-bold block text-gray-800">รีเซ็ตชั่วโมงทำงานเป็น 0</span>
                                                <span class="text-xs text-gray-500">เลือกเมื่อเป็นการ "เช็คระยะใหญ่" หรือ "เปลี่ยนถ่ายน้ำมันเครื่อง" เพื่อเริ่มนับรอบใหม่</span>
                                            </label>
                                        </div>
                                    </div>
                                    <div class="mt-6 flex gap-3">
                                        <button type="button" @click="activeModal = null" class="w-1/3 py-2.5 rounded-xl border border-gray-200 text-gray-600 font-bold hover:bg-gray-50 transition">ยกเลิก</button>
                                        <button type="submit" class="w-2/3 bg-green-600 text-white py-2.5 rounded-xl font-bold hover:bg-green-700 transition shadow-lg shadow-green-200">
                                            บันทึกและปิดงาน
                                        </button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="text-center py-10 text-gray-400">
                        <i class="fa-solid fa-check-circle text-4xl mb-2 text-gray-300"></i>
                        <p class="text-sm">ไม่มีงานซ่อมค้างอยู่</p>
                    </div>
                @endforelse
            </div>
        </div>

    </div>

    {{-- 4. ประวัติการซ่อมบำรุงล่าสุด (History) --}}
    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden mt-6">
        <div class="px-6 py-4 border-b border-gray-100 flex items-center gap-2 bg-gray-50/50">
            <i class="fa-solid fa-clock-rotate-left text-gray-400"></i>
            <h3 class="font-bold text-gray-800">ประวัติการซ่อมบำรุงล่าสุด</h3>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-sm text-left">
                <thead class="bg-gray-50 text-gray-500 text-xs uppercase font-semibold">
                    <tr>
                        <th class="px-6 py-3">วันที่เสร็จ</th>
                        <th class="px-6 py-3">เครื่องจักร</th>
                        <th class="px-6 py-3">ผู้ซ่อม</th>
                        <th class="px-6 py-3">รายการซ่อม</th>
                        <th class="px-6 py-3 text-center">รีเซ็ตชั่วโมง</th> {{-- ✅ เพิ่มคอลัมน์ --}}
                        <th class="px-6 py-3 text-right">ค่าซ่อม</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse ($history ?? [] as $h)
                        <tr class="hover:bg-gray-50 transition">
                            <td class="px-6 py-4 text-gray-600">{{ \Carbon\Carbon::parse($h->completion_date)->format('d/m/Y') }}</td>
                            <td class="px-6 py-4 font-bold text-gray-800">{{ $h->equipment->name ?? '-' }}</td>
                            
                            {{-- ✅ ดึงข้อมูล service_provider --}}
                            <td class="px-6 py-4">
                                @if(isset($h->service_provider))
                                    <span class="bg-blue-50 text-blue-600 px-2 py-1 rounded text-xs font-bold">{{ $h->service_provider }}</span>
                                @else
                                    <span class="text-gray-300">-</span>
                                @endif
                            </td>
                            
                            <td class="px-6 py-4 text-gray-600 truncate max-w-xs" title="{{ $h->description }}">{{ Str::limit($h->description, 50) }}</td>
                            
                            {{-- ✅ แสดงสถานะการรีเซ็ต --}}
                            <td class="px-6 py-4 text-center">
                                @if($h->reset_counter)
                                    <span class="text-green-500 font-bold"><i class="fa-solid fa-check"></i></span>
                                @else
                                    <span class="text-gray-300">-</span>
                                @endif
                            </td>

                            {{-- ✅ ดึงข้อมูล total_cost --}}
                            <td class="px-6 py-4 text-right font-bold text-red-600">{{ number_format($h->total_cost, 2) }} ฿</td>
                        </tr>
                    @empty
                        <tr><td colspan="6" class="px-6 py-8 text-center text-gray-400">ยังไม่มีประวัติการซ่อม</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

</div>

<style>
    .custom-scrollbar::-webkit-scrollbar { width: 4px; }
    .custom-scrollbar::-webkit-scrollbar-thumb { background: #e2e8f0; border-radius: 2px; }
</style>
@endsection