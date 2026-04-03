@extends('layouts.admin')
@section('title', 'รายงานสรุป')
@section('header', 'รายงานผลการดำเนินงาน')

@section('content')
<div class="max-w-6xl mx-auto space-y-6">

    <div class="bg-white p-4 rounded-2xl shadow-sm border border-gray-100 flex items-center justify-between">
        <div>
            <p class="text-xs text-gray-500 font-bold">รายงานเฉพาะทาง</p>
            <p class="text-lg font-bold text-gray-800">กำไร/ขาดทุนรายตัวรถ</p>
        </div>
        <a href="{{ route('admin.reports.equipment_profit') }}"
            class="bg-agri-primary text-white px-5 py-2.5 rounded-xl font-bold shadow-lg shadow-agri-primary/30 hover:bg-agri-hover transition">
            ดูรายงาน
        </a>
    </div>

    {{-- Filter Bar: เปลี่ยนเป็น Form เพื่อให้ส่งค่า Filter ได้ --}}
    <form action="{{ route('admin.reports.index') }}" method="GET" class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4 bg-white p-4 rounded-2xl shadow-sm border border-gray-100">
        <div class="flex items-center gap-3 text-gray-600">
            <div class="p-2 bg-agri-bg rounded-lg"><i class="fa-regular fa-calendar-check"></i></div>
            <span class="font-medium">สรุปข้อมูลประจำเดือน</span>
        </div>
        <select name="month" onchange="this.form.submit()" class="w-full sm:w-auto bg-gray-50 border-none text-sm font-semibold text-agri-primary rounded-xl py-2 pl-4 pr-10 focus:ring-2 focus:ring-agri-accent/50 cursor-pointer hover:bg-gray-100 transition">
            @foreach(($monthOptions ?? []) as $monthKey => $monthLabel)
                <option value="{{ $monthKey }}" {{ ($selectedMonth ?? request('month')) == $monthKey ? 'selected' : '' }}>{{ $monthLabel }}</option>
            @endforeach
        </select>
    </form>

    {{-- 1. Stats Grid --}}
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
        
        {{-- Card: รายได้ (ลิงก์ไปหน้างานที่เสร็จแล้ว หรือหน้ารายรับถ้ามี) --}}
        <a href="{{ route('admin.jobs.index', ['status' => 'completed']) }}" class="bg-white p-5 rounded-2xl shadow-sm border border-gray-100 flex flex-col justify-between h-32 relative overflow-hidden group hover:shadow-md hover:border-green-200 transition cursor-pointer">
            <div class="absolute right-0 top-0 p-4 opacity-10 transform translate-x-2 -translate-y-2 group-hover:scale-110 transition">
                <i class="fa-solid fa-wallet text-6xl text-green-600"></i>
            </div>
            <div>
                <p class="text-gray-500 text-xs font-medium uppercase tracking-wide group-hover:text-green-600 transition">รายได้รวม</p>
                <h3 class="text-3xl font-bold text-gray-800 mt-1">฿{{ number_format($summary->total_revenue ?? 0, 2) }}</h3>
            </div>
            <div class="text-xs text-gray-400 mt-2">ยอดงานตามช่วงเดือนที่เลือก</div>
        </a>

        {{-- Card: งานเสร็จ (ลิงก์ไปหน้างานเสร็จ) --}}
        <a href="{{ route('admin.jobs.index', ['status' => 'completed']) }}" class="bg-white p-5 rounded-2xl shadow-sm border border-gray-100 flex flex-col justify-between h-32 relative overflow-hidden group hover:shadow-md hover:border-blue-200 transition cursor-pointer">
            <div class="absolute right-0 top-0 p-4 opacity-10 transform translate-x-2 -translate-y-2 group-hover:scale-110 transition">
                <i class="fa-solid fa-check-circle text-6xl text-blue-600"></i>
            </div>
            <div>
                <p class="text-gray-500 text-xs font-medium uppercase tracking-wide group-hover:text-blue-600 transition">งานเสร็จสิ้น</p>
                <h3 class="text-3xl font-bold text-gray-800 mt-1">{{ $summary->completed_jobs ?? 0 }} <span class="text-sm font-normal text-gray-400">งาน</span></h3>
            </div>
            <div class="text-xs text-gray-400 mt-2">สถานะ completed</div>
        </a>

        {{-- Card: ยกเลิก --}}
        <a href="{{ route('admin.jobs.index', ['status' => 'cancelled']) }}" class="bg-white p-5 rounded-2xl shadow-sm border border-gray-100 flex flex-col justify-between h-32 relative overflow-hidden group hover:shadow-md hover:border-orange-200 transition cursor-pointer">
            <div class="absolute right-0 top-0 p-4 opacity-10 transform translate-x-2 -translate-y-2 group-hover:scale-110 transition">
                <i class="fa-solid fa-ban text-6xl text-orange-500"></i>
            </div>
            <div>
                <p class="text-gray-500 text-xs font-medium uppercase tracking-wide group-hover:text-orange-600 transition">ยกเลิก</p>
                <h3 class="text-3xl font-bold text-gray-800 mt-1">{{ $summary->cancelled_jobs ?? 0 }} <span class="text-sm font-normal text-gray-400">งาน</span></h3>
            </div>
            <span class="text-xs text-orange-500 bg-orange-50 w-fit px-2 py-0.5 rounded-md font-medium mt-auto group-hover:bg-orange-100 transition">สถานะ cancelled</span>
        </a>

        {{-- Card: ซ่อมบำรุง --}}
        <a href="{{ route('admin.maintenance.index') }}" class="bg-white p-5 rounded-2xl shadow-sm border border-gray-100 flex flex-col justify-between h-32 relative overflow-hidden group hover:shadow-md hover:border-red-200 transition cursor-pointer">
            <div class="absolute right-0 top-0 p-4 opacity-10 transform translate-x-2 -translate-y-2 group-hover:scale-110 transition">
                <i class="fa-solid fa-wrench text-6xl text-red-500"></i>
            </div>
            <div>
                <p class="text-gray-500 text-xs font-medium uppercase tracking-wide group-hover:text-red-600 transition">ซ่อมบำรุง</p>
                <h3 class="text-3xl font-bold text-gray-800 mt-1">{{ $summary->maintenance_jobs ?? 0 }} <span class="text-sm font-normal text-gray-400">รายการ</span></h3>
            </div>
            <div class="text-xs text-gray-400 mt-2">ค่าใช้จ่ายรวม ฿{{ number_format($summary->maintenance_cost ?? 0, 2) }}</div>
        </a>
    </div>

    {{-- 2. Recent Transactions Table --}}
    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-50 flex justify-between items-center bg-gray-50/50">
            <h3 class="font-bold text-gray-800 flex items-center gap-2">
                <i class="fa-solid fa-list-check text-agri-primary"></i> รายการล่าสุด
            </h3>
            <a href="{{ route('admin.jobs.index') }}" class="text-xs font-medium text-agri-primary hover:text-agri-hover hover:underline flex items-center gap-1 transition">
                ดูทั้งหมด <i class="fa-solid fa-arrow-right"></i>
            </a>
        </div>
        
        <div class="overflow-x-auto">
            <table class="w-full text-sm text-left">
                <thead class="bg-gray-50 text-gray-500 font-medium">
                    <tr>
                        <th class="px-6 py-3 whitespace-nowrap">วันที่</th>
                        <th class="px-6 py-3 whitespace-nowrap">ลูกค้า</th>
                        <th class="px-6 py-3 whitespace-nowrap">รายละเอียดงาน</th>
                        <th class="px-6 py-3 text-right whitespace-nowrap">จำนวนเงิน</th>
                        <th class="px-6 py-3 text-center whitespace-nowrap">สถานะ</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-50">
                    @forelse(($recentTransactions ?? []) as $job)
                        @php
                            $statusLabel = match($job->status) {
                                'completed' => ['text' => 'เสร็จสิ้น', 'class' => 'bg-green-100 text-green-700 border-green-200'],
                                'completed_pending_approval' => ['text' => 'รอตรวจสอบ', 'class' => 'bg-orange-50 text-orange-700 border-orange-200'],
                                'in_progress' => ['text' => 'กำลังทำงาน', 'class' => 'bg-blue-100 text-blue-700 border-blue-200'],
                                'pending_approval' => ['text' => 'รออนุมัติ', 'class' => 'bg-yellow-50 text-yellow-700 border-yellow-200'],
                                'scheduled' => ['text' => 'นัดหมายแล้ว', 'class' => 'bg-indigo-50 text-indigo-700 border-indigo-200'],
                                'cancelled' => ['text' => 'ยกเลิก', 'class' => 'bg-red-100 text-red-700 border-red-200'],
                                default => ['text' => $job->status, 'class' => 'bg-gray-100 text-gray-700 border-gray-200'],
                            };
                        @endphp
                        <tr class="hover:bg-gray-50 transition cursor-pointer group" onclick="window.location='{{ route('admin.jobs.show', $job->id) }}'">
                            <td class="px-6 py-4 text-gray-500 whitespace-nowrap group-hover:text-agri-primary transition">{{ optional($job->scheduled_start)->format('d M y') }}</td>
                            <td class="px-6 py-4 font-medium text-gray-800 whitespace-nowrap">
                                <div class="flex items-center gap-2">
                                    <div class="w-6 h-6 rounded-full bg-teal-100 text-teal-600 flex items-center justify-center text-xs"><i class="fa-solid fa-user"></i></div>
                                    {{ $job->customer->name ?? '-' }}
                                </div>
                            </td>
                            <td class="px-6 py-4 text-gray-600">{{ $job->equipment->name ?? '-' }} ({{ $job->job_number ?? 'JOB-'.$job->id }})</td>
                            <td class="px-6 py-4 text-right font-bold text-agri-primary">฿{{ number_format((float) $job->total_price, 2) }}</td>
                            <td class="px-6 py-4 text-center">
                                <span class="px-2 py-1 rounded-full text-xs font-bold border {{ $statusLabel['class'] }}">{{ $statusLabel['text'] }}</span>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-6 py-6 text-center text-gray-400">ไม่มีรายการในเดือนที่เลือก</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection