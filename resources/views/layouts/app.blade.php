<meta http-equiv="Content-Security-Policy" content="upgrade-insecure-requests">
@extends('layouts.admin') {{-- 🔴 ตรวจสอบว่าไฟล์ Layout หลักคุณชื่อ admin.blade.php หรือไม่ --}}

@section('title', 'Dashboard Overview')
@section('header', 'Dashboard')

@section('content')
    {{-- 1. ส่วนหัวและตัวเลือกช่วงเวลา --}}
    <div class="flex flex-col md:flex-row justify-between items-center mb-6 gap-4">
        <div>
            <h2 class="text-2xl font-bold text-gray-800">สรุปภาพรวมประจำเดือน</h2>
            <p class="text-sm text-gray-500">ข้อมูลล่าสุดเมื่อ: {{ date('d M Y H:i') }}</p>
        </div>
        
        <div class="flex gap-2">
            <select class="border-gray-300 rounded-lg text-sm shadow-sm focus:border-agri-primary focus:ring focus:ring-agri-primary/20">
                <option>เดือนนี้ (Dec 2025)</option>
                <option>เดือนที่แล้ว (Nov 2025)</option>
                <option>ปีนี้ (2025)</option>
            </select>
            <button class="bg-white text-gray-700 border border-gray-300 px-4 py-2 rounded-lg text-sm font-medium hover:bg-gray-50 flex items-center gap-2 shadow-sm">
                <i class="fa-solid fa-print"></i> Report
            </button>
        </div>
    </div>

    {{-- 2. การ์ดแสดงสถานะ (Stats Cards) --}}
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
        
        <div class="bg-white rounded-xl shadow-sm p-5 border border-gray-100 relative overflow-hidden group hover:shadow-md transition">
            <div class="flex justify-between items-start">
                <div>
                    <p class="text-sm font-medium text-gray-500">รายรับ (Income)</p>
                    <h3 class="text-2xl font-bold text-gray-800 mt-1">฿21,000</h3>
                </div>
                <div class="p-2 bg-green-100 rounded-lg text-green-600">
                    <i class="fa-solid fa-sack-dollar text-xl"></i>
                </div>
            </div>
            <div class="mt-4 flex items-center text-xs">
                <span class="text-green-600 font-bold flex items-center gap-1 bg-green-50 px-1.5 py-0.5 rounded">
                    <i class="fa-solid fa-arrow-trend-up"></i> +12%
                </span>
                <span class="text-gray-400 ml-2">จากเดือนที่แล้ว</span>
            </div>
        </div>

        <div class="bg-white rounded-xl shadow-sm p-5 border border-gray-100 relative overflow-hidden group hover:shadow-md transition">
            <div class="flex justify-between items-start">
                <div>
                    <p class="text-sm font-medium text-gray-500">รายจ่าย (Expense)</p>
                    <h3 class="text-2xl font-bold text-gray-800 mt-1">฿8,576</h3>
                </div>
                <div class="p-2 bg-red-100 rounded-lg text-red-600">
                    <i class="fa-solid fa-file-invoice-dollar text-xl"></i>
                </div>
            </div>
            <div class="mt-4 flex items-center text-xs">
                <span class="text-red-500 font-bold flex items-center gap-1 bg-red-50 px-1.5 py-0.5 rounded">
                    <i class="fa-solid fa-arrow-trend-up"></i> +5%
                </span>
                <span class="text-gray-400 ml-2">ค่าอะไหล่สูงขึ้น</span>
            </div>
        </div>

        <div class="bg-white rounded-xl shadow-sm p-5 border border-gray-100 relative overflow-hidden group hover:shadow-md transition">
            <div class="flex justify-between items-start">
                <div>
                    <p class="text-sm font-medium text-gray-500">กำไรสุทธิ (Net Profit)</p>
                    <h3 class="text-2xl font-bold text-agri-primary mt-1">฿12,424</h3>
                </div>
                <div class="p-2 bg-blue-100 rounded-lg text-blue-600">
                    <i class="fa-solid fa-chart-pie text-xl"></i>
                </div>
            </div>
            <div class="mt-4 flex items-center text-xs">
                <span class="text-gray-500 font-medium">Stable</span>
                <span class="text-gray-400 ml-2">สถานะการเงินแข็งแกร่ง</span>
            </div>
        </div>

        <div class="bg-white rounded-xl shadow-sm p-5 border border-gray-100 relative overflow-hidden group hover:shadow-md transition">
            <div class="flex justify-between items-start">
                <div>
                    <p class="text-sm font-medium text-gray-500">งานรอดำเนินการ</p>
                    <h3 class="text-2xl font-bold text-orange-500 mt-1">5 งาน</h3>
                </div>
                <div class="p-2 bg-orange-100 rounded-lg text-orange-600">
                    <i class="fa-solid fa-clock text-xl"></i>
                </div>
            </div>
            <div class="mt-4 flex items-center text-xs">
                <a href="#" class="text-orange-600 hover:underline">ดูงานทั้งหมด &rarr;</a>
            </div>
        </div>
    </div>

    {{-- 3. ส่วนกราฟและข้อมูลสรุปงาน (Charts & Lists) --}}
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        
        {{-- กราฟแนวโน้ม (Chart Section) - ใช้พื้นที่ 2 ใน 3 --}}
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6 lg:col-span-2">
            <div class="flex justify-between items-center mb-4">
                <h3 class="font-bold text-gray-800 flex items-center gap-2">
                    <i class="fa-solid fa-chart-area text-agri-primary"></i> แนวโน้มรายรับ-รายจ่าย
                </h3>
                <button class="text-gray-400 hover:text-gray-600"><i class="fa-solid fa-ellipsis"></i></button>
            </div>
            
            {{-- พื้นที่วางกราฟ (Placeholder) --}}
            <div class="h-64 bg-gray-50 rounded-lg border-2 border-dashed border-gray-200 flex flex-col items-center justify-center text-gray-400">
                <i class="fa-solid fa-chart-simple text-4xl mb-2"></i>
                <span>Chart Area (ใส่ Chart.js หรือ ApexCharts ตรงนี้)</span>
            </div>
        </div>

        {{-- สรุปสถานะงาน (Job Summary) - ใช้พื้นที่ 1 ใน 3 --}}
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
            <h3 class="font-bold text-gray-800 mb-4">สถานะงานปัจจุบัน</h3>
            
            <div class="space-y-4">
                {{-- Item 1 --}}
                <div class="flex items-center justify-between p-3 bg-green-50 rounded-lg border border-green-100">
                    <div class="flex items-center gap-3">
                        <div class="w-8 h-8 rounded-full bg-green-200 flex items-center justify-center text-green-700">
                            <i class="fa-solid fa-check"></i>
                        </div>
                        <div>
                            <p class="text-sm font-bold text-gray-700">งานเสร็จสิ้น</p>
                            <p class="text-xs text-gray-500">ในเดือนนี้</p>
                        </div>
                    </div>
                    <span class="text-xl font-bold text-green-700">12</span>
                </div>

                {{-- Item 2 --}}
                <div class="flex items-center justify-between p-3 bg-orange-50 rounded-lg border border-orange-100">
                    <div class="flex items-center gap-3">
                        <div class="w-8 h-8 rounded-full bg-orange-200 flex items-center justify-center text-orange-700">
                            <i class="fa-solid fa-hourglass-half"></i>
                        </div>
                        <div>
                            <p class="text-sm font-bold text-gray-700">รอตรวจสอบ</p>
                            <p class="text-xs text-gray-500">ต้องรีบดำเนินการ</p>
                        </div>
                    </div>
                    <span class="text-xl font-bold text-orange-700">2</span>
                </div>

                {{-- Item 3 --}}
                <div class="flex items-center justify-between p-3 bg-blue-50 rounded-lg border border-blue-100">
                    <div class="flex items-center gap-3">
                        <div class="w-8 h-8 rounded-full bg-blue-200 flex items-center justify-center text-blue-700">
                            <i class="fa-solid fa-tractor"></i>
                        </div>
                        <div>
                            <p class="text-sm font-bold text-gray-700">เครื่องจักรทำงาน</p>
                            <p class="text-xs text-gray-500">กำลังใช้งานอยู่</p>
                        </div>
                    </div>
                    <span class="text-xl font-bold text-blue-700">4</span>
                </div>
            </div>
        </div>
    </div>
@endsection