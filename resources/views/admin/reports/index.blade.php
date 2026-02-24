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
            <option value="2026-01" {{ request('month') == '2026-01' ? 'selected' : '' }}>มกราคม 2026</option>
            <option value="2025-12" {{ request('month') == '2025-12' ? 'selected' : '' }}>ธันวาคม 2025</option>
            <option value="2025-11" {{ request('month') == '2025-11' ? 'selected' : '' }}>พฤศจิกายน 2025</option>
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
                <h3 class="text-3xl font-bold text-gray-800 mt-1">฿124,500</h3>
            </div>
            <div class="flex items-center gap-2 mt-2">
                <span class="bg-green-100 text-green-700 text-xs px-2 py-0.5 rounded-full font-bold">+12%</span>
                <span class="text-xs text-gray-400">จากเดือนก่อน</span>
            </div>
        </a>

        {{-- Card: งานเสร็จ (ลิงก์ไปหน้างานเสร็จ) --}}
        <a href="{{ route('admin.jobs.index', ['status' => 'completed']) }}" class="bg-white p-5 rounded-2xl shadow-sm border border-gray-100 flex flex-col justify-between h-32 relative overflow-hidden group hover:shadow-md hover:border-blue-200 transition cursor-pointer">
            <div class="absolute right-0 top-0 p-4 opacity-10 transform translate-x-2 -translate-y-2 group-hover:scale-110 transition">
                <i class="fa-solid fa-check-circle text-6xl text-blue-600"></i>
            </div>
            <div>
                <p class="text-gray-500 text-xs font-medium uppercase tracking-wide group-hover:text-blue-600 transition">งานเสร็จสิ้น</p>
                <h3 class="text-3xl font-bold text-gray-800 mt-1">45 <span class="text-sm font-normal text-gray-400">งาน</span></h3>
            </div>
            <div class="flex items-center gap-2 mt-2">
                <div class="w-full bg-gray-100 rounded-full h-1.5 max-w-[100px]">
                    <div class="bg-blue-500 h-1.5 rounded-full" style="width: 85%"></div>
                </div>
                <span class="text-xs text-gray-400">เป้าหมาย 85%</span>
            </div>
        </a>

        {{-- Card: รอตรวจสอบ/กำลังทำ (ลิงก์ไปหน้างานค้าง) --}}
        <a href="{{ route('admin.jobs.index', ['status' => 'in_progress']) }}" class="bg-white p-5 rounded-2xl shadow-sm border border-gray-100 flex flex-col justify-between h-32 relative overflow-hidden group hover:shadow-md hover:border-orange-200 transition cursor-pointer">
            <div class="absolute right-0 top-0 p-4 opacity-10 transform translate-x-2 -translate-y-2 group-hover:scale-110 transition">
                <i class="fa-solid fa-clock text-6xl text-orange-500"></i>
            </div>
            <div>
                <p class="text-gray-500 text-xs font-medium uppercase tracking-wide group-hover:text-orange-600 transition">กำลังดำเนินการ</p>
                <h3 class="text-3xl font-bold text-gray-800 mt-1">8 <span class="text-sm font-normal text-gray-400">งาน</span></h3>
            </div>
            <span class="text-xs text-orange-500 bg-orange-50 w-fit px-2 py-0.5 rounded-md font-medium mt-auto group-hover:bg-orange-100 transition">ต้องการการตรวจสอบ</span>
        </a>

        {{-- Card: ค่าใช้จ่าย (ลิงก์ไปหน้าซ่อมบำรุง) --}}
        <a href="{{ route('admin.maintenance.index') }}" class="bg-white p-5 rounded-2xl shadow-sm border border-gray-100 flex flex-col justify-between h-32 relative overflow-hidden group hover:shadow-md hover:border-red-200 transition cursor-pointer">
            <div class="absolute right-0 top-0 p-4 opacity-10 transform translate-x-2 -translate-y-2 group-hover:scale-110 transition">
                <i class="fa-solid fa-wrench text-6xl text-red-500"></i>
            </div>
            <div>
                <p class="text-gray-500 text-xs font-medium uppercase tracking-wide group-hover:text-red-600 transition">ค่าซ่อมบำรุง</p>
                <h3 class="text-3xl font-bold text-gray-800 mt-1">฿8,400</h3>
            </div>
            <div class="flex items-center gap-2 mt-2">
                <span class="bg-red-100 text-red-700 text-xs px-2 py-0.5 rounded-full font-bold">+5%</span>
                <span class="text-xs text-gray-400">สูงกว่าปกติ</span>
            </div>
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
                    {{-- Row 1: ใส่ onclick ให้ทั้งแถว --}}
                    <tr class="hover:bg-gray-50 transition cursor-pointer group" onclick="window.location='{{ route('admin.jobs.show', 1) }}'">
                        <td class="px-6 py-4 text-gray-500 whitespace-nowrap group-hover:text-agri-primary transition">01 ม.ค. 69</td>
                        <td class="px-6 py-4 font-medium text-gray-800 whitespace-nowrap">
                            <div class="flex items-center gap-2">
                                <div class="w-6 h-6 rounded-full bg-teal-100 text-teal-600 flex items-center justify-center text-xs"><i class="fa-solid fa-user"></i></div>
                                คุณสมชาย (ไร่อ้อย)
                            </div>
                        </td>
                        <td class="px-6 py-4 text-gray-600">ไถเตรียมดิน 10 ไร่</td>
                        <td class="px-6 py-4 text-right font-bold text-agri-primary">฿5,000</td>
                        <td class="px-6 py-4 text-center">
                            <span class="px-2 py-1 rounded-full bg-green-100 text-green-700 text-xs font-bold border border-green-200">จ่ายแล้ว</span>
                        </td>
                    </tr>
                    {{-- Row 2 --}}
                    <tr class="hover:bg-gray-50 transition cursor-pointer group" onclick="window.location='{{ route('admin.jobs.show', 2) }}'">
                        <td class="px-6 py-4 text-gray-500 whitespace-nowrap group-hover:text-agri-primary transition">30 ธ.ค. 68</td>
                        <td class="px-6 py-4 font-medium text-gray-800 whitespace-nowrap">
                            <div class="flex items-center gap-2">
                                <div class="w-6 h-6 rounded-full bg-teal-100 text-teal-600 flex items-center justify-center text-xs"><i class="fa-solid fa-user"></i></div>
                                คุณวิภา (นาข้าว)
                            </div>
                        </td>
                        <td class="px-6 py-4 text-gray-600">เกี่ยวข้าว 15 ไร่</td>
                        <td class="px-6 py-4 text-right font-bold text-gray-800">฿9,000</td>
                        <td class="px-6 py-4 text-center">
                            <span class="px-2 py-1 rounded-full bg-yellow-50 text-yellow-700 text-xs font-bold border border-yellow-200">รอโอน</span>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection