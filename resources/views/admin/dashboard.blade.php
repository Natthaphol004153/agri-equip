@extends('layouts.admin')

@section('title', 'Executive Dashboard')
@section('header', 'ภาพรวมระบบบริหารจัดการ')

@section('content')
    <div class="max-w-7xl mx-auto space-y-6 pb-12" x-data="calendarApp()" x-init="initCalendar()">

        {{-- 🎯 NEW: Quick Action Bar --}}
        <div
            class="bg-gradient-to-r from-agri-primary via-green-700 to-agri-primary bg-size-200 animate-gradient rounded-2xl shadow-xl p-6 text-white relative overflow-hidden">
            <div class="absolute top-0 right-0 w-64 h-64 bg-white/5 rounded-full -mr-32 -mt-32"></div>
            <div class="absolute bottom-0 left-0 w-48 h-48 bg-white/5 rounded-full -ml-24 -mb-24"></div>

            <div class="relative z-10">
                <div class="flex flex-col md:flex-row items-center justify-between gap-4">
                    <div class="text-center md:text-left">
                        <h2 class="text-2xl font-bold mb-1">🎯 ศูนย์บัญชาการ Dashboard</h2>
                        <p class="text-green-100 text-sm">การควบคุมที่รวดเร็ว เพื่อประสิทธิภาพสูงสุด</p>
                    </div>

                    <div class="flex flex-wrap gap-3 justify-center">
                        <button onclick="showQuickAddJob()"
                            class="group bg-white/20 hover:bg-white/30 backdrop-blur-sm px-5 py-3 rounded-xl flex items-center gap-2 transition-all hover:scale-105 shadow-lg">
                            <div
                                class="w-8 h-8 bg-white/20 rounded-lg flex items-center justify-center group-hover:rotate-90 transition-transform">
                                <i class="fa-solid fa-plus"></i>
                            </div>
                            <span class="font-bold text-sm">เพิ่มงานใหม่</span>
                        </button>

                        <button onclick="openReportModal()"
                            class="group bg-white/20 hover:bg-white/30 backdrop-blur-sm px-5 py-3 rounded-xl flex items-center gap-2 transition-all hover:scale-105 shadow-lg">
                            <div class="w-8 h-8 bg-white/20 rounded-lg flex items-center justify-center">
                                <i class="fa-solid fa-file-export"></i>
                            </div>
                            <span class="font-bold text-sm">ส่งออกรายงาน</span>
                        </button>

                        <button onclick="toggleRealTimeMode()"
                            class="group bg-white/20 hover:bg-white/30 backdrop-blur-sm px-5 py-3 rounded-xl flex items-center gap-2 transition-all hover:scale-105 shadow-lg">
                            <div class="w-8 h-8 bg-white/20 rounded-lg flex items-center justify-center">
                                <i class="fa-solid fa-satellite-dish animate-pulse"></i>
                            </div>
                            <span class="font-bold text-sm">Real-time Mode</span>
                        </button>

                        <button onclick="showNotificationCenter()"
                            class="group bg-white/20 hover:bg-white/30 backdrop-blur-sm px-5 py-3 rounded-xl flex items-center gap-2 transition-all hover:scale-105 shadow-lg relative">
                            <div class="w-8 h-8 bg-white/20 rounded-lg flex items-center justify-center">
                                <i class="fa-solid fa-bell"></i>
                            </div>
                            <span class="font-bold text-sm">การแจ้งเตือน</span>
                            <span
                                class="absolute -top-1 -right-1 w-5 h-5 bg-red-500 rounded-full text-xs flex items-center justify-center animate-bounce">5</span>
                        </button>
                    </div>
                </div>
            </div>
        </div>

        {{-- 🔴 1. Alert Section --}}
        @if (isset($maintenanceAlerts) && count($maintenanceAlerts) > 0)
            <div x-data="{ open: true }"
                class="bg-white rounded-2xl shadow-lg border border-red-100 overflow-hidden relative">
                <div class="absolute top-0 left-0 w-1 h-full bg-red-500"></div>

                <div class="p-4 flex justify-between items-center bg-gradient-to-r from-red-50 to-white cursor-pointer hover:bg-red-50 transition"
                    @click="open = !open">
                    <div class="flex items-center gap-3">
                        <div class="bg-red-100 p-2.5 rounded-xl text-red-600 animate-pulse shadow-sm relative">
                            <i class="fa-solid fa-triangle-exclamation text-xl"></i>
                            <span class="absolute -top-1 -right-1 w-3 h-3 bg-red-500 rounded-full animate-ping"></span>
                        </div>
                        <div>
                            <h3 class="font-bold text-red-800 text-lg flex items-center gap-2">
                                แจ้งเตือนบำรุงรักษา
                                <span
                                    class="bg-red-600 text-white text-[10px] px-2 py-0.5 rounded-full shadow-sm">{{ count($maintenanceAlerts) }}</span>
                            </h3>
                            <p class="text-sm text-red-600/80 font-medium">เครื่องจักรเหล่านี้ถึงรอบตรวจเช็คแล้ว</p>
                        </div>
                    </div>
                    <i class="fa-solid text-red-400 transition-transform duration-300"
                        :class="open ? 'fa-chevron-up' : 'fa-chevron-down'"></i>
                </div>

                <div x-show="open" x-transition.opacity
                    class="divide-y divide-gray-100 max-h-[300px] overflow-y-auto custom-scrollbar">
                    @foreach ($maintenanceAlerts as $alert)
                        @php
                            $diff = $alert->maintenance_hour_threshold - $alert->current_hours;
                            $isOverdue = $diff <= 0;
                            $statusColor = $isOverdue
                                ? 'bg-red-100 text-red-700 border-red-200'
                                : 'bg-orange-100 text-orange-700 border-orange-200';
                            $statusIcon = $isOverdue ? 'fa-circle-exclamation' : 'fa-clock';
                            $statusText = $isOverdue
                                ? 'เลยกำหนด ' . number_format(abs($diff)) . ' ชม.'
                                : 'อีก ' . number_format($diff) . ' ชม.';
                        @endphp
                        <div
                            class="p-4 hover:bg-gray-50 transition flex flex-col md:flex-row items-center justify-between gap-4 group">
                            <div class="flex items-center gap-4 w-full md:w-auto">
                                <div class="relative">
                                    <img src="{{ $alert->image_path ? asset('storage/' . $alert->image_path) : 'https://ui-avatars.com/api/?name=' . $alert->name }}"
                                        class="w-14 h-14 rounded-xl object-cover shadow-sm border border-gray-200 group-hover:scale-105 transition">
                                    @if ($isOverdue)
                                        <div
                                            class="absolute -top-1 -right-1 w-4 h-4 bg-red-500 border-2 border-white rounded-full animate-bounce">
                                        </div>
                                    @endif
                                </div>
                                <div>
                                    <h4 class="font-bold text-gray-800 text-base flex items-center gap-2">
                                        {{ $alert->name }}
                                        <span
                                            class="text-[10px] font-mono bg-gray-100 text-gray-500 px-1.5 py-0.5 rounded border border-gray-200">{{ $alert->equipment_code }}</span>
                                    </h4>
                                    <div class="flex items-center gap-2 text-sm mt-0.5">
                                        <span class="font-bold {{ $isOverdue ? 'text-red-600' : 'text-orange-500' }}">
                                            <i class="fa-solid fa-gauge-high mr-1"></i>
                                            {{ number_format($alert->current_hours) }} /
                                            {{ number_format($alert->maintenance_hour_threshold) }} ชม.
                                        </span>
                                    </div>
                                </div>
                            </div>

                            <div class="flex items-center gap-3 w-full md:w-auto justify-end">
                                <span
                                    class="hidden md:inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg text-xs font-bold border {{ $statusColor }}">
                                    <i class="fa-solid {{ $statusIcon }}"></i> {{ $statusText }}
                                </span>
                                <a href="{{ route('admin.maintenance.create', ['equipment_id' => $alert->id]) }}"
                                    class="flex-1 md:flex-none px-4 py-2 rounded-xl bg-red-600 text-white text-xs font-bold hover:bg-red-700 shadow-md shadow-red-200 transition flex items-center justify-center gap-2 active:scale-95">
                                    <i class="fa-solid fa-screwdriver-wrench"></i> <span>เปิดใบซ่อม</span>
                                </a>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        @endif

        {{-- 🚜 2. Operational Stats --}}
        <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
            {{-- Active Machines --}}
            <div class="bg-gradient-to-br from-blue-50 to-blue-100 p-4 rounded-2xl shadow-md border-2 border-blue-200 flex flex-col justify-between h-32 relative overflow-hidden group hover:shadow-xl transition-all hover:scale-105 cursor-pointer"
                onclick="filterDashboard('active')">
                <div
                    class="absolute right-0 top-0 p-3 opacity-10 group-hover:scale-125 transition-transform duration-500">
                    <i class="fa-solid fa-tractor text-5xl text-blue-600"></i>
                </div>
                <div class="absolute top-2 right-2 opacity-0 group-hover:opacity-100 transition">
                    <i class="fa-solid fa-arrow-up-right-from-square text-blue-400 text-sm"></i>
                </div>
                <div>
                    <p class="text-xs text-blue-600 font-bold uppercase tracking-wide">กำลังทำงาน</p>
                    <h3 class="text-3xl font-black text-blue-700 mt-1">{{ $activeMachines ?? 0 }} <span
                            class="text-sm font-normal text-blue-400">คัน</span></h3>
                </div>
                <div class="flex items-center justify-between">
                    <div class="flex items-center gap-2">
                        <span
                            class="w-2.5 h-2.5 rounded-full bg-blue-500 animate-pulse shadow-lg shadow-blue-500/50"></span>
                        <span class="text-xs text-blue-600 font-bold">Active Now</span>
                    </div>
                    <div class="text-xs font-bold text-blue-400">+{{ rand(5, 15) }}%</div>
                </div>
            </div>

            {{-- Available Staff --}}
            <div class="bg-gradient-to-br from-purple-50 to-purple-100 p-4 rounded-2xl shadow-md border-2 border-purple-200 flex flex-col justify-between h-32 relative overflow-hidden group hover:shadow-xl transition-all hover:scale-105 cursor-pointer"
                onclick="filterDashboard('staff')">
                <div
                    class="absolute right-0 top-0 p-3 opacity-10 group-hover:scale-125 transition-transform duration-500">
                    <i class="fa-solid fa-users text-5xl text-purple-600"></i>
                </div>
                <div class="absolute top-2 right-2 opacity-0 group-hover:opacity-100 transition">
                    <i class="fa-solid fa-arrow-up-right-from-square text-purple-400 text-sm"></i>
                </div>
                <div>
                    <p class="text-xs text-purple-600 font-bold uppercase tracking-wide">พนักงานพร้อม</p>
                    <h3 class="text-3xl font-black text-purple-700 mt-1">{{ $availableStaff ?? 0 }} <span
                            class="text-sm font-normal text-purple-400">คน</span></h3>
                </div>
                <div class="flex items-center justify-between">
                    <div class="flex items-center gap-2">
                        <span class="w-2.5 h-2.5 rounded-full bg-purple-500 shadow-lg shadow-purple-500/50"></span>
                        <span class="text-xs text-purple-600 font-bold">Standby</span>
                    </div>
                    <div class="text-xs font-bold text-purple-400">100%</div>
                </div>
            </div>

            {{-- Pending Jobs --}}
            <a href="{{ route('admin.jobs.index', ['status' => 'completed_pending_approval']) }}"
                class="bg-gradient-to-br from-orange-50 to-orange-100 p-4 rounded-2xl shadow-md border-2 border-orange-200 flex flex-col justify-between h-32 relative overflow-hidden group hover:shadow-xl transition-all hover:scale-105 cursor-pointer">
                <div
                    class="absolute right-0 top-0 p-3 opacity-10 group-hover:scale-125 transition-transform duration-500">
                    <i class="fa-solid fa-clipboard-check text-5xl text-orange-600"></i>
                </div>
                <div class="absolute top-2 right-2 opacity-0 group-hover:opacity-100 transition">
                    <i class="fa-solid fa-arrow-up-right-from-square text-orange-400 text-sm"></i>
                </div>
                @if (($pendingJobs ?? 0) > 0)
                    <div class="absolute top-2 left-2 animate-bounce">
                        <span class="w-3 h-3 bg-red-500 rounded-full block shadow-lg"></span>
                    </div>
                @endif
                <div>
                    <p class="text-xs text-orange-600 font-bold uppercase tracking-wide">รอตรวจสอบ</p>
                    <h3 class="text-3xl font-black text-orange-700 mt-1">{{ $pendingJobs ?? 0 }} <span
                            class="text-sm font-normal text-orange-400">งาน</span></h3>
                </div>
                <div class="flex items-center justify-between">
                    <div class="flex items-center gap-2">
                        <i class="fa-solid fa-circle-exclamation text-orange-500 animate-pulse"></i>
                        <span class="text-xs text-orange-600 font-bold">Action Required</span>
                    </div>
                </div>
            </a>

            {{-- Completed Jobs --}}
            <div class="bg-gradient-to-br from-green-50 to-green-100 p-4 rounded-2xl shadow-md border-2 border-green-200 flex flex-col justify-between h-32 relative overflow-hidden group hover:shadow-xl transition-all hover:scale-105 cursor-pointer"
                onclick="filterDashboard('completed')">
                <div
                    class="absolute right-0 top-0 p-3 opacity-10 group-hover:scale-125 transition-transform duration-500">
                    <i class="fa-solid fa-flag-checkered text-5xl text-green-600"></i>
                </div>
                <div class="absolute top-2 right-2 opacity-0 group-hover:opacity-100 transition">
                    <i class="fa-solid fa-arrow-up-right-from-square text-green-400 text-sm"></i>
                </div>
                <div>
                    <p class="text-xs text-green-600 font-bold uppercase tracking-wide">งานเสร็จสิ้น</p>
                    <h3 class="text-3xl font-black text-green-700 mt-1">{{ $completedJobs ?? 0 }} <span
                            class="text-sm font-normal text-green-400">งาน</span></h3>
                </div>
                <div class="flex items-center justify-between">
                    <span class="text-xs text-green-500 font-medium">ทั้งหมดในระบบ</span>
                    <div class="text-xs font-bold text-green-400">+{{ rand(10, 25) }}%</div>
                </div>
            </div>
        </div>

        {{-- 💰 3. Financial Analysis (Graph & Filter) --}}
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            {{-- Left Column: Stats Boxes --}}
            <div class="space-y-4">
                {{-- Filter --}}
                <div class="bg-gradient-to-br from-white to-gray-50 p-4 rounded-2xl shadow-md border border-gray-200">
                    <h3 class="font-bold text-gray-800 flex items-center gap-2 mb-3">
                        <div class="w-8 h-8 bg-agri-primary rounded-lg flex items-center justify-center">
                            <i class="fa-solid fa-filter text-white text-sm"></i>
                        </div>
                        <span>กรองช่วงเวลา</span>
                    </h3>
                    <div class="space-y-3">
                        <div class="relative">
                            <label class="text-xs text-gray-500 font-bold mb-1 block">วันที่เริ่มต้น</label>
                            <input type="date" id="startDate"
                                class="w-full bg-white border-2 border-gray-200 text-gray-700 text-sm rounded-xl p-3 focus:border-agri-primary focus:ring-2 focus:ring-agri-primary/20 transition"
                                value="{{ date('Y-m-01') }}">
                        </div>
                        <div class="relative">
                            <label class="text-xs text-gray-500 font-bold mb-1 block">วันที่สิ้นสุด</label>
                            <input type="date" id="endDate"
                                class="w-full bg-white border-2 border-gray-200 text-gray-700 text-sm rounded-xl p-3 focus:border-agri-primary focus:ring-2 focus:ring-agri-primary/20 transition"
                                value="{{ date('Y-m-d') }}">
                        </div>
                        <button onclick="fetchChartData()"
                            class="w-full bg-gradient-to-r from-agri-primary to-green-700 text-white py-3 rounded-xl text-sm font-bold hover:shadow-lg transition-all flex items-center justify-center gap-2 group">
                            <i
                                class="fa-solid fa-rotate-right group-hover:rotate-180 transition-transform duration-500"></i>
                            <span>อัปเดตข้อมูล</span>
                        </button>

                        {{-- Quick Filter Buttons --}}
                        <div class="pt-2 border-t border-gray-200">
                            <p class="text-xs text-gray-500 font-bold mb-2">ช่วงเวลาด่วน</p>
                            <div class="grid grid-cols-2 gap-2">
                                <button onclick="setQuickFilter('today')"
                                    class="text-xs bg-white border border-gray-200 hover:bg-agri-primary hover:text-white hover:border-agri-primary py-2 rounded-lg transition font-bold">วันนี้</button>
                                <button onclick="setQuickFilter('week')"
                                    class="text-xs bg-white border border-gray-200 hover:bg-agri-primary hover:text-white hover:border-agri-primary py-2 rounded-lg transition font-bold">สัปดาห์นี้</button>
                                <button onclick="setQuickFilter('month')"
                                    class="text-xs bg-white border border-gray-200 hover:bg-agri-primary hover:text-white hover:border-agri-primary py-2 rounded-lg transition font-bold">เดือนนี้</button>
                                <button onclick="setQuickFilter('year')"
                                    class="text-xs bg-white border border-gray-200 hover:bg-agri-primary hover:text-white hover:border-agri-primary py-2 rounded-lg transition font-bold">ปีนี้</button>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- 1. รายรับรวม (Income) - สีเขียว --}}
                <div
                    class="bg-gradient-to-br from-green-50 to-emerald-50 p-5 rounded-2xl shadow-md border-2 border-green-200 relative overflow-hidden group hover:shadow-xl transition-all">
                    <div
                        class="absolute right-0 bottom-0 p-4 opacity-5 group-hover:scale-110 group-hover:rotate-12 transition-all duration-500">
                        <i class="fa-solid fa-sack-dollar text-6xl text-agri-primary"></i>
                    </div>
                    <div class="absolute top-3 right-3">
                        <div class="w-2 h-2 bg-green-500 rounded-full animate-pulse"></div>
                    </div>
                    <p class="text-xs text-green-700 font-bold uppercase tracking-wider mb-1 flex items-center gap-2">
                        <i class="fa-solid fa-arrow-trend-up"></i> รายรับรวม (Income)
                    </p>
                    <h3 class="text-4xl font-black text-agri-primary mt-1" id="sumIncome">
                        ฿{{ number_format($totalIncome ?? 0) }}</h3>
                    <div class="mt-3 flex items-center justify-between">
                        <p class="text-xs text-green-600 font-medium">รายได้จากงานบริการ</p>
                        <span
                            class="text-xs font-bold text-green-600 bg-green-100 px-2 py-1 rounded-md">+{{ rand(5, 20) }}%</span>
                    </div>
                </div>

                {{-- 2. ต้นทุนรวม (Total Cost) - สีแดง --}}
                <div
                    class="bg-gradient-to-br from-red-50 to-rose-50 p-5 rounded-2xl shadow-md border-2 border-red-200 relative overflow-hidden group hover:shadow-xl transition-all">
                    <div
                        class="absolute right-0 bottom-0 p-4 opacity-5 group-hover:scale-110 group-hover:rotate-12 transition-all duration-500">
                        <i class="fa-solid fa-money-bill-wave text-6xl text-red-500"></i>
                    </div>
                    <div class="absolute top-3 right-3">
                        <div class="w-2 h-2 bg-red-500 rounded-full animate-pulse"></div>
                    </div>
                    <p class="text-xs text-red-700 font-bold uppercase tracking-wider mb-1 flex items-center gap-2">
                        <i class="fa-solid fa-arrow-trend-down"></i> ต้นทุนรวม (Total Cost)
                    </p>
                    <h3 class="text-4xl font-black text-red-600 mt-1" id="sumCost">
                        ฿{{ number_format($totalExpense ?? 0) }}</h3>
                    <div class="mt-3 flex items-center justify-between">
                        <p class="text-xs text-red-500 font-medium">All Expenses Combined</p>
                        <span
                            class="text-xs font-bold text-red-600 bg-red-100 px-2 py-1 rounded-md">-{{ rand(2, 10) }}%</span>
                    </div>
                </div>

                {{-- 3. ค่าซ่อมบำรุง (Maintenance) - สีชมพู (แยกออกมา) --}}
                <div
                    class="bg-gradient-to-br from-pink-50 to-fuchsia-50 p-5 rounded-2xl shadow-md border-2 border-pink-200 relative overflow-hidden group hover:shadow-xl transition-all">
                    <div
                        class="absolute right-0 bottom-0 p-4 opacity-10 group-hover:scale-110 group-hover:rotate-12 transition-all duration-500">
                        <i class="fa-solid fa-screwdriver-wrench text-6xl text-pink-500"></i>
                    </div>
                    <div class="absolute top-3 right-3">
                        <div class="w-2 h-2 bg-pink-500 rounded-full animate-pulse"></div>
                    </div>
                    <p class="text-xs text-pink-700 font-bold uppercase tracking-wider mb-1 flex items-center gap-2">
                        <i class="fa-solid fa-gears"></i> ค่าซ่อมบำรุง (Maintenance)
                    </p>
                    <h3 class="text-4xl font-black text-pink-600 mt-1" id="sumMaintenance">
                        ฿{{ number_format($maintenanceCost ?? 0) }}</h3>
                    <div class="mt-3 flex items-center justify-between">
                        <p class="text-xs text-pink-500 font-medium">Machine Repair Cost</p>
                        <span class="text-xs font-bold text-pink-600 bg-pink-100 px-2 py-1 rounded-md">
                            <i class="fa-solid fa-wrench mr-1"></i> Fix
                        </span>
                    </div>
                </div>

                {{-- 4. ค่าน้ำมัน (Fuel) - สีส้ม (แยกออกมา) --}}
                <div
                    class="bg-gradient-to-br from-orange-50 to-amber-50 p-5 rounded-2xl shadow-md border-2 border-orange-200 relative overflow-hidden group hover:shadow-xl transition-all">
                    <div
                        class="absolute right-0 bottom-0 p-4 opacity-10 group-hover:scale-110 group-hover:rotate-12 transition-all duration-500">
                        <i class="fa-solid fa-gas-pump text-6xl text-orange-500"></i>
                    </div>
                    <div class="absolute top-3 right-3">
                        <div class="w-2 h-2 bg-orange-500 rounded-full animate-pulse"></div>
                    </div>
                    <p class="text-xs text-orange-700 font-bold uppercase tracking-wider mb-1 flex items-center gap-2">
                        <i class="fa-solid fa-droplet"></i> ค่าน้ำมัน (Fuel)
                    </p>
                    <h3 class="text-4xl font-black text-orange-600 mt-1" id="sumFuel">
                        ฿{{ number_format($fuelCostThisMonth ?? 0, 2) }}
                    </h3>
                    <div class="mt-3 flex items-center justify-between">
                        <p class="text-xs text-orange-600 font-medium">
                            <i class="fa-solid fa-fire-flame-curved"></i> Fuel Usage
                        </p>
                        <span class="text-xs font-bold text-orange-600 bg-orange-100 px-2 py-1 rounded-md">
                            {{ rand(15, 30) }}%
                        </span>
                    </div>
                </div>

                {{-- 5. ชั่วโมงเครื่องจักร - สีฟ้า --}}
                <div
                    class="bg-gradient-to-br from-blue-50 to-cyan-50 p-5 rounded-2xl shadow-md border-2 border-blue-200 relative overflow-hidden group hover:shadow-xl transition-all">
                    <div
                        class="absolute right-0 bottom-0 p-4 opacity-5 group-hover:scale-110 group-hover:rotate-12 transition-all duration-500">
                        <i class="fa-solid fa-clock-rotate-left text-6xl text-blue-500"></i>
                    </div>
                    <div class="absolute top-3 right-3">
                        <div class="w-2 h-2 bg-blue-500 rounded-full animate-pulse"></div>
                    </div>
                    <p class="text-xs text-blue-700 font-bold uppercase tracking-wider mb-1 flex items-center gap-2">
                        <i class="fa-solid fa-gauge-high"></i> ชั่วโมงเครื่องจักร
                    </p>
                    <h3 class="text-4xl font-black text-blue-600 mt-1" id="sumHours">-</h3>
                    <div class="mt-3 flex items-center justify-between">
                        <p class="text-xs text-blue-500 font-medium">Machine Utilization</p>
                        <span
                            class="text-xs font-bold text-blue-600 bg-blue-100 px-2 py-1 rounded-md">{{ rand(70, 95) }}%</span>
                    </div>
                </div>

                {{-- 6. กำไรสุทธิ - สีม่วง --}}
                <div
                    class="bg-gradient-to-br from-purple-50 to-violet-50 p-5 rounded-2xl shadow-md border-2 border-purple-200 relative overflow-hidden group hover:shadow-xl transition-all">
                    <div
                        class="absolute right-0 bottom-0 p-4 opacity-5 group-hover:scale-110 transition-all duration-500">
                        <i class="fa-solid fa-chart-pie text-6xl text-purple-500"></i>
                    </div>
                    <p class="text-xs text-purple-700 font-bold uppercase tracking-wider mb-1">กำไรสุทธิ (Profit)</p>
                    <h3 class="text-4xl font-black text-purple-600 mt-1" id="sumProfit">฿0</h3>
                    <div class="mt-3 flex items-center justify-between">
                        <p class="text-xs text-purple-500 font-medium">Profit Margin</p>
                        <span class="text-xs font-bold text-purple-600 bg-purple-100 px-2 py-1 rounded-md"
                            id="profitMargin">0%</span>
                    </div>
                </div>
            </div>

            {{-- Right Column: Main Chart --}}
            <div class="lg:col-span-2 bg-white p-6 rounded-2xl shadow-md border border-gray-200 relative flex flex-col">
                <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center mb-6 gap-4">
                    <h3 class="font-bold text-gray-800 text-lg flex items-center gap-2">
                        <div
                            class="w-10 h-10 bg-gradient-to-br from-agri-primary to-green-700 rounded-xl flex items-center justify-center shadow-lg">
                            <i class="fa-solid fa-chart-area text-white"></i>
                        </div>
                        <div>
                            <span class="block">แนวโน้มประสิทธิภาพ</span>
                            <span class="text-xs text-gray-500 font-normal">Performance Trend Analysis</span>
                        </div>
                    </h3>
                    <div class="flex flex-wrap gap-3 text-xs">
                        <button onclick="toggleChartType('bar')"
                            class="flex items-center gap-1.5 px-3 py-2 rounded-lg bg-gray-100 hover:bg-agri-primary hover:text-white transition font-bold">
                            <i class="fa-solid fa-chart-column"></i> <span>แท่ง</span>
                        </button>
                        <button onclick="toggleChartType('line')"
                            class="flex items-center gap-1.5 px-3 py-2 rounded-lg bg-gray-100 hover:bg-agri-primary hover:text-white transition font-bold">
                            <i class="fa-solid fa-chart-line"></i> <span>เส้น</span>
                        </button>
                        <button onclick="exportChartData()"
                            class="flex items-center gap-1.5 px-3 py-2 rounded-lg bg-gray-100 hover:bg-agri-primary hover:text-white transition font-bold">
                            <i class="fa-solid fa-download"></i> <span>ส่งออก</span>
                        </button>
                    </div>
                </div>

                {{-- Legend --}}
                <div class="flex flex-wrap gap-4 mb-4 pb-4 border-b border-gray-100">
                    <label class="flex items-center gap-2 cursor-pointer group">
                        <input type="checkbox" checked onchange="toggleDataset(0)"
                            class="w-4 h-4 text-agri-primary rounded focus:ring-2 focus:ring-agri-primary">
                        <div class="w-4 h-4 bg-agri-primary rounded"></div>
                        <span
                            class="text-sm font-bold text-gray-700 group-hover:text-agri-primary transition">รายรับ</span>
                    </label>
                    <label class="flex items-center gap-2 cursor-pointer group">
                        <input type="checkbox" checked onchange="toggleDataset(1)"
                            class="w-4 h-4 text-red-500 rounded focus:ring-2 focus:ring-red-500">
                        <div class="w-4 h-4 bg-red-500 rounded"></div>
                        <span class="text-sm font-bold text-gray-700 group-hover:text-red-500 transition">ต้นทุน</span>
                    </label>
                    <label class="flex items-center gap-2 cursor-pointer group">
                        <input type="checkbox" checked onchange="toggleDataset(2)"
                            class="w-4 h-4 text-blue-400 rounded focus:ring-2 focus:ring-blue-400">
                        <div class="w-4 h-1 bg-blue-400 border-t border-blue-400"></div>
                        <span class="text-sm font-bold text-gray-700 group-hover:text-blue-400 transition">ชม.งาน</span>
                    </label>
                </div>

                <div class="flex-1 w-full relative min-h-[300px]">
                    <canvas id="financeChart"></canvas>
                    <div id="chartLoading"
                        class="absolute inset-0 bg-white/90 backdrop-blur-sm flex items-center justify-center z-10 hidden">
                        <div class="flex flex-col items-center">
                            <div class="relative">
                                <i class="fa-solid fa-circle-notch fa-spin text-agri-primary text-4xl mb-2"></i>
                                <div class="absolute inset-0 animate-ping">
                                    <i class="fa-solid fa-circle-notch text-agri-primary/20 text-4xl"></i>
                                </div>
                            </div>
                            <span class="text-sm text-gray-500 font-bold mt-2">กำลังโหลดข้อมูล...</span>
                            <span class="text-xs text-gray-400 mt-1">กรุณารอสักครู่</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- 🗓️ 4. JOB CALENDAR (Full Details) --}}
        <div class="bg-white rounded-2xl shadow-md border border-gray-200 overflow-hidden relative">
            <div
                class="px-6 py-4 border-b border-gray-100 flex flex-col sm:flex-row items-center justify-between bg-gradient-to-r from-indigo-50 to-purple-50 gap-4">
                <h3 class="font-bold text-gray-800 flex items-center gap-2 text-lg">
                    <div
                        class="w-12 h-12 rounded-xl bg-gradient-to-br from-indigo-500 to-purple-600 text-white flex items-center justify-center shadow-lg">
                        <i class="fa-regular fa-calendar-days text-xl"></i>
                    </div>
                    <div>
                        <span class="block leading-tight">ปฏิทินงาน (Job Schedule)</span>
                        <span class="text-xs text-gray-500 font-normal">ตารางคิวงานรายเดือน • Real-time Updates</span>
                    </div>
                </h3>

                <div class="flex items-center gap-3">
                    <div class="flex items-center gap-3 bg-white p-1 rounded-xl border-2 border-gray-200 shadow-sm">
                        <button @click="prevMonth()"
                            class="w-9 h-9 flex items-center justify-center rounded-lg hover:bg-gradient-to-br hover:from-agri-primary hover:to-green-700 hover:text-white text-gray-600 transition-all">
                            <i class="fa-solid fa-chevron-left"></i>
                        </button>
                        <div class="text-center min-w-[140px]">
                            <span class="text-sm font-bold text-agri-primary block"
                                x-text="monthNames[currentMonth]"></span>
                            <span class="text-xs text-gray-400 font-medium" x-text="currentYear"></span>
                        </div>
                        <button @click="nextMonth()"
                            class="w-9 h-9 flex items-center justify-center rounded-lg hover:bg-gradient-to-br hover:from-agri-primary hover:to-green-700 hover:text-white text-gray-600 transition-all">
                            <i class="fa-solid fa-chevron-right"></i>
                        </button>
                    </div>

                    <button @click="goToToday()"
                        class="px-4 py-2 rounded-xl bg-white border-2 border-gray-200 text-xs font-bold text-gray-700 hover:bg-agri-primary hover:text-white hover:border-agri-primary transition-all shadow-sm">
                        <i class="fa-solid fa-calendar-day mr-1"></i> วันนี้
                    </button>
                </div>
            </div>

            <div class="p-4 md:p-6 bg-white">
                <div class="grid grid-cols-7 mb-3">
                    <template x-for="(day, index) in ['อา', 'จ', 'อ', 'พ', 'พฤ', 'ศ', 'ส']">
                        <div class="text-center py-2" :class="index === 0 || index === 6 ? 'bg-red-50 rounded-t-lg' : ''">
                            <span class="text-xs font-black uppercase tracking-wider"
                                :class="index === 0 || index === 6 ? 'text-red-600' : 'text-gray-500'"
                                x-text="day"></span>
                        </div>
                    </template>
                </div>

                <div class="grid grid-cols-7 gap-2 md:gap-3">
                    <template x-for="blank in blankDays">
                        <div
                            class="h-28 md:h-36 bg-gradient-to-br from-gray-50 to-gray-100 rounded-xl border-2 border-dashed border-gray-200 flex items-center justify-center">
                            <i class="fa-solid fa-ban text-gray-300 text-xl"></i>
                        </div>
                    </template>

                    <template x-for="(date, index) in noOfDays">
                        <div class="relative h-28 md:h-36 border-2 rounded-xl bg-white hover:shadow-lg transition-all duration-200 flex flex-col overflow-hidden group"
                            :class="[
                                isToday(date) ?
                                'ring-4 ring-agri-primary/50 ring-offset-2 border-agri-primary shadow-lg' :
                                'border-gray-200',
                                isWeekend(date) ? 'bg-red-50/30' : ''
                            ]">

                            <div class="p-2 flex justify-between items-start">
                                <span
                                    class="text-sm font-bold w-8 h-8 flex items-center justify-center rounded-full transition-all"
                                    :class="isToday(date) ?
                                        'bg-gradient-to-br from-agri-primary to-green-700 text-white shadow-md scale-110' :
                                        'text-gray-700 group-hover:bg-gray-100'"
                                    x-text="date"></span>

                                <div class="flex flex-col items-end gap-1">
                                    <div x-show="getEvents(date).length > 0"
                                        class="text-[10px] font-black px-2 py-1 rounded-lg shadow-sm"
                                        :class="getEvents(date).length > 3 ? 'bg-red-100 text-red-600 animate-pulse' :
                                            'bg-blue-100 text-blue-600'">
                                        <i class="fa-solid fa-briefcase mr-0.5"></i>
                                        <span x-text="getEvents(date).length"></span>
                                    </div>
                                </div>
                            </div>

                            <div class="flex-1 overflow-y-auto space-y-1 px-1 pb-1 custom-scrollbar">
                                <template x-for="event in getEvents(date)">
                                    <button @click="openEventModal(event)"
                                        class="w-full text-left p-1.5 rounded-lg border-2 text-[10px] md:text-xs transition-all hover:scale-[1.02] shadow-sm flex flex-col gap-0.5 group/event"
                                        :class="event.status.color">

                                        <div class="flex items-center justify-between w-full">
                                            <span
                                                class="font-extrabold truncate group-hover/event:text-agri-primary transition"
                                                x-text="event.job_number"></span>
                                            <i :class="'fa-solid ' + event.status.icon"
                                                class="opacity-70 text-[9px] group-hover/event:scale-125 transition"></i>
                                        </div>

                                        <div class="flex items-center gap-1 opacity-90 truncate">
                                            <i class="fa-regular fa-user text-[9px]"></i>
                                            <span class="truncate" x-text="event.title"></span>
                                        </div>

                                        <div class="flex items-center gap-1 text-[9px] opacity-70">
                                            <i class="fa-regular fa-clock"></i>
                                            <span x-text="event.time_range"></span>
                                        </div>
                                    </button>
                                </template>
                            </div>
                        </div>
                    </template>
                </div>
            </div>
        </div>

        {{-- 🔥 5. MODAL รายละเอียดงาน --}}
        <div x-show="showModal" style="display: none;"
            class="fixed inset-0 z-[60] flex items-center justify-center p-4 bg-black/70 backdrop-blur-md"
            x-transition.opacity>

            <div @click.away="showModal = false"
                class="bg-white rounded-3xl shadow-2xl w-full max-w-lg overflow-hidden transform transition-all scale-100"
                x-transition:enter="ease-out duration-300" x-transition:enter-start="opacity-0 scale-95"
                x-transition:enter-end="opacity-100 scale-100">

                <div
                    class="px-6 py-5 flex justify-between items-center border-b border-gray-100 bg-gradient-to-r from-gray-50 to-gray-100 relative overflow-hidden">
                    <div class="absolute inset-0 opacity-10">
                        <div class="absolute top-0 left-0 w-32 h-32 bg-agri-primary rounded-full -ml-16 -mt-16"></div>
                        <div class="absolute bottom-0 right-0 w-24 h-24 bg-blue-500 rounded-full -mr-12 -mb-12"></div>
                    </div>
                    <div class="flex items-center gap-3 relative z-10">
                        <div class="w-12 h-12 rounded-xl flex items-center justify-center shadow-lg animate-pulse"
                            :class="selectedEvent?.status.color">
                            <i :class="'fa-solid text-xl ' + selectedEvent?.status.icon"></i>
                        </div>
                        <div>
                            <h4 class="font-black text-gray-800 text-xl" x-text="selectedEvent?.job_number"></h4>
                            <span class="text-xs font-bold px-3 py-1 rounded-full bg-white shadow-inner border-2"
                                :class="selectedEvent?.status.color.replace('bg-', 'border-').replace('-100', '-200')"
                                x-text="selectedEvent?.status.label"></span>
                        </div>
                    </div>
                    <button @click="showModal = false"
                        class="text-gray-400 hover:text-gray-600 transition w-10 h-10 flex items-center justify-center rounded-full hover:bg-white shadow-sm relative z-10">
                        <i class="fa-solid fa-xmark text-xl"></i>
                    </button>
                </div>

                <div class="p-6 space-y-5 max-h-[70vh] overflow-y-auto custom-scrollbar">
                    <div class="grid grid-cols-2 gap-4">
                        <div
                            class="bg-gradient-to-br from-blue-50 to-cyan-50 p-4 rounded-2xl border-2 border-blue-200 relative overflow-hidden group">
                            <div class="absolute top-2 right-2 opacity-10 group-hover:scale-110 transition">
                                <i class="fa-solid fa-clock text-3xl text-blue-500"></i>
                            </div>
                            <p class="text-[10px] text-blue-600 uppercase font-black tracking-wider mb-2">เวลานัดหมาย</p>
                            <div class="flex items-center gap-2 text-gray-700 font-bold">
                                <i class="fa-regular fa-clock text-agri-primary"></i>
                                <span x-text="selectedEvent?.time_range"></span>
                            </div>
                            <div class="text-xs text-gray-500 ml-6 mt-1 font-medium" x-text="selectedEvent?.start_date">
                            </div>
                        </div>
                        <div
                            class="bg-gradient-to-br from-orange-50 to-amber-50 p-4 rounded-2xl border-2 border-orange-200 relative overflow-hidden group">
                            <div class="absolute top-2 right-2 opacity-10 group-hover:scale-110 transition">
                                <i class="fa-solid fa-tractor text-3xl text-orange-500"></i>
                            </div>
                            <p class="text-[10px] text-orange-600 uppercase font-black tracking-wider mb-2">เครื่องจักร</p>
                            <div class="flex items-center gap-2 text-gray-700 font-bold text-sm">
                                <i class="fa-solid fa-tractor text-orange-500"></i>
                                <span x-text="selectedEvent?.equipment" class="truncate"></span>
                            </div>
                        </div>
                    </div>

                    <div class="border-t border-b border-gray-100 py-5 space-y-5">
                        <div
                            class="bg-gradient-to-br from-purple-50 to-pink-50 p-4 rounded-2xl border-2 border-purple-200">
                            <p
                                class="text-xs text-purple-600 uppercase font-black tracking-wider mb-3 flex items-center gap-2">
                                <i class="fa-solid fa-user-circle"></i> ข้อมูลลูกค้า
                            </p>
                            <div class="flex items-start gap-3">
                                <div
                                    class="w-12 h-12 rounded-full bg-gradient-to-br from-blue-400 to-blue-600 text-white flex items-center justify-center shrink-0 shadow-lg">
                                    <i class="fa-solid fa-user text-lg"></i>
                                </div>
                                <div class="flex-1">
                                    <p class="font-black text-gray-800 text-base" x-text="selectedEvent?.title"></p>
                                    <div class="space-y-1 mt-2">
                                        <p class="text-sm text-gray-600 flex items-center gap-2">
                                            <i class="fa-solid fa-phone text-xs text-green-500"></i>
                                            <span x-text="selectedEvent?.phone"></span>
                                        </p>
                                        <p class="text-sm text-gray-600 flex items-center gap-2">
                                            <i class="fa-solid fa-location-dot text-xs text-red-500"></i>
                                            <span x-text="selectedEvent?.location"></span>
                                        </p>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div
                            class="bg-gradient-to-br from-green-50 to-emerald-50 p-4 rounded-2xl border-2 border-green-200">
                            <p
                                class="text-xs text-green-600 uppercase font-black tracking-wider mb-3 flex items-center gap-2">
                                <i class="fa-solid fa-user-tie"></i> พนักงานรับผิดชอบ
                            </p>
                            <div class="flex items-center gap-3">
                                <template x-if="selectedEvent?.staff_avatar">
                                    <img :src="selectedEvent?.staff_avatar"
                                        class="w-12 h-12 rounded-full border-2 border-green-300 shadow-lg">
                                </template>
                                <div>
                                    <span class="text-sm font-black text-gray-800 block"
                                        x-text="selectedEvent?.staff"></span>
                                    <span class="text-xs text-green-600 font-bold">Assigned Staff</span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div
                        class="flex justify-between items-center pt-2 bg-gradient-to-r from-gray-50 to-gray-100 p-4 rounded-2xl border-2 border-gray-200">
                        <div>
                            <p class="text-xs text-gray-500 font-bold mb-1">มูลค่างาน</p>
                            <p
                                class="text-3xl font-black bg-gradient-to-r from-agri-primary to-green-700 bg-clip-text text-transparent">
                                ฿<span x-text="selectedEvent?.price"></span>
                            </p>
                        </div>
                        <a :href="selectedEvent?.url"
                            class="px-6 py-3 bg-gradient-to-r from-gray-900 to-gray-700 text-white text-sm font-black rounded-xl hover:shadow-xl transition-all flex items-center gap-2 group">
                            <span>ดูรายละเอียด</span>
                            <i class="fa-solid fa-arrow-right group-hover:translate-x-1 transition"></i>
                        </a>
                    </div>
                </div>
            </div>
        </div>

    </div>

    {{-- 🎨 NEW: Quick Add Job Modal --}}
    <div id="quickAddJobModal"
        class="hidden fixed inset-0 z-[70] flex items-center justify-center p-4 bg-black/70 backdrop-blur-md">
        <div class="bg-white rounded-3xl shadow-2xl w-full max-w-md">
            <div
                class="p-6 border-b border-gray-100 bg-gradient-to-r from-agri-primary to-green-700 text-white rounded-t-3xl">
                <h3 class="font-black text-xl flex items-center gap-2">
                    <i class="fa-solid fa-plus-circle"></i> เพิ่มงานใหม่อย่างรวดเร็ว
                </h3>
            </div>
            <div class="p-6 space-y-4">
                <div>
                    <label class="text-xs font-bold text-gray-600 mb-2 block">ลูกค้า</label>
                    <input type="text"
                        class="w-full border-2 border-gray-200 rounded-xl p-3 focus:border-agri-primary focus:ring-2 focus:ring-agri-primary/20"
                        placeholder="ชื่อลูกค้า">
                </div>
                <div>
                    <label class="text-xs font-bold text-gray-600 mb-2 block">วันที่นัดหมาย</label>
                    <input type="date"
                        class="w-full border-2 border-gray-200 rounded-xl p-3 focus:border-agri-primary focus:ring-2 focus:ring-agri-primary/20">
                </div>
                <div class="flex gap-3 pt-4">
                    <button onclick="closeQuickAddJob()"
                        class="flex-1 py-3 bg-gray-100 text-gray-700 rounded-xl font-bold hover:bg-gray-200 transition">
                        ยกเลิก
                    </button>
                    <button onclick="submitQuickJob()"
                        class="flex-1 py-3 bg-gradient-to-r from-agri-primary to-green-700 text-white rounded-xl font-bold hover:shadow-lg transition">
                        บันทึก
                    </button>
                </div>
            </div>
        </div>
    </div>

    {{-- 🎨 NEW: Report Export Modal --}}
    <div id="reportModal"
        class="hidden fixed inset-0 z-[70] flex items-center justify-center p-4 bg-black/70 backdrop-blur-md">
        <div class="bg-white rounded-3xl shadow-2xl w-full max-w-md">
            <div class="p-6 border-b border-gray-100">
                <h3 class="font-black text-xl flex items-center gap-2">
                    <i class="fa-solid fa-file-export text-agri-primary"></i> ส่งออกรายงาน
                </h3>
            </div>
            <div class="p-6 space-y-3">
                <button onclick="exportReport('pdf')"
                    class="w-full p-4 bg-red-50 border-2 border-red-200 rounded-xl hover:bg-red-100 transition flex items-center justify-between group">
                    <div class="flex items-center gap-3">
                        <i class="fa-solid fa-file-pdf text-2xl text-red-600"></i>
                        <div class="text-left">
                            <p class="font-bold text-gray-800">PDF Report</p>
                            <p class="text-xs text-gray-500">รายงานฉบับสมบูรณ์</p>
                        </div>
                    </div>
                    <i class="fa-solid fa-download text-red-600 group-hover:translate-y-1 transition"></i>
                </button>

                <button onclick="exportReport('excel')"
                    class="w-full p-4 bg-green-50 border-2 border-green-200 rounded-xl hover:bg-green-100 transition flex items-center justify-between group">
                    <div class="flex items-center gap-3">
                        <i class="fa-solid fa-file-excel text-2xl text-green-600"></i>
                        <div class="text-left">
                            <p class="font-bold text-gray-800">Excel Spreadsheet</p>
                            <p class="text-xs text-gray-500">ข้อมูลวิเคราะห์</p>
                        </div>
                    </div>
                    <i class="fa-solid fa-download text-green-600 group-hover:translate-y-1 transition"></i>
                </button>

                <button onclick="exportReport('csv')"
                    class="w-full p-4 bg-blue-50 border-2 border-blue-200 rounded-xl hover:bg-blue-100 transition flex items-center justify-between group">
                    <div class="flex items-center gap-3">
                        <i class="fa-solid fa-file-csv text-2xl text-blue-600"></i>
                        <div class="text-left">
                            <p class="font-bold text-gray-800">CSV Data</p>
                            <p class="text-xs text-gray-500">ข้อมูลดิบ</p>
                        </div>
                    </div>
                    <i class="fa-solid fa-download text-blue-600 group-hover:translate-y-1 transition"></i>
                </button>

                <button onclick="closeReportModal()"
                    class="w-full mt-4 py-3 bg-gray-100 text-gray-700 rounded-xl font-bold hover:bg-gray-200 transition">
                    ปิด
                </button>
            </div>
        </div>
    </div>

    {{-- 🔔 NEW: Notification Center --}}
    <div id="notificationCenter"
        class="hidden fixed right-4 top-20 z-[70] w-96 bg-white rounded-2xl shadow-2xl border-2 border-gray-200">
        <div class="p-4 border-b border-gray-100 bg-gradient-to-r from-blue-50 to-purple-50">
            <div class="flex items-center justify-between">
                <h3 class="font-black text-lg flex items-center gap-2">
                    <i class="fa-solid fa-bell text-blue-600"></i> การแจ้งเตือน
                </h3>
                <button onclick="closeNotificationCenter()" class="text-gray-400 hover:text-gray-600">
                    <i class="fa-solid fa-xmark"></i>
                </button>
            </div>
        </div>
        <div class="max-h-96 overflow-y-auto custom-scrollbar">
            <div class="p-4 space-y-3">
                <div class="p-3 bg-red-50 border-l-4 border-red-500 rounded-lg">
                    <p class="text-sm font-bold text-red-800">เครื่องจักร T-001 ต้องบำรุงรักษา</p>
                    <p class="text-xs text-red-600 mt-1">5 นาทีที่แล้ว</p>
                </div>
                <div class="p-3 bg-blue-50 border-l-4 border-blue-500 rounded-lg">
                    <p class="text-sm font-bold text-blue-800">งาน #JOB-2024-001 เสร็จสิ้น</p>
                    <p class="text-xs text-blue-600 mt-1">15 นาทีที่แล้ว</p>
                </div>
                <div class="p-3 bg-green-50 border-l-4 border-green-500 rounded-lg">
                    <p class="text-sm font-bold text-green-800">ลูกค้าใหม่ลงทะเบียน</p>
                    <p class="text-xs text-green-600 mt-1">1 ชั่วโมงที่แล้ว</p>
                </div>
            </div>
        </div>
    </div>

@endsection

@push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        // --- Chart Logic - ENHANCED ---
        let financeChartInstance = null;
        let currentChartType = 'bar';
        document.addEventListener('DOMContentLoaded', function() {
            fetchChartData();
        });

        function fetchChartData() {
            const startDate = document.getElementById('startDate').value;
            const endDate = document.getElementById('endDate').value;
            const loader = document.getElementById('chartLoading');
            if (loader) loader.classList.remove('hidden');

            fetch(`{{ route('admin.dashboard.financial') }}?start_date=${startDate}&end_date=${endDate}`)
                .then(response => response.json())
                .then(data => {
                    updateDashboard(data);
                    if (loader) loader.classList.add('hidden');
                })
                .catch(error => {
                    console.error('Error:', error);
                    if (loader) loader.classList.add('hidden');
                });
        }

        function updateDashboard(data) {
            // Update Summary Cards
            if (document.getElementById('sumIncome')) document.getElementById('sumIncome').innerText = '฿' + new Intl
                .NumberFormat().format(data.summary.total_income);
            if (document.getElementById('sumCost')) document.getElementById('sumCost').innerText = '฿' + new Intl
                .NumberFormat().format(data.summary.total_cost);
            if (document.getElementById('sumHours')) document.getElementById('sumHours').innerText = new Intl.NumberFormat()
                .format(data.summary.total_hours) + ' ชม.';

            // ✅ Update Maintenance & Fuel Cards (New!)
            if (document.getElementById('sumMaintenance')) {
                document.getElementById('sumMaintenance').innerText = '฿' + new Intl.NumberFormat().format(data.summary.total_maintenance);
            }
            if (document.getElementById('sumFuel')) {
                document.getElementById('sumFuel').innerText = '฿' + new Intl.NumberFormat().format(data.summary.total_fuel);
            }

            // Calculate and display profit
            const profit = data.summary.total_income - data.summary.total_cost;
            const profitMargin = data.summary.total_income > 0 ? ((profit / data.summary.total_income) * 100).toFixed(1) :
                0;
            if (document.getElementById('sumProfit')) document.getElementById('sumProfit').innerText = '฿' + new Intl
                .NumberFormat().format(profit);
            if (document.getElementById('profitMargin')) document.getElementById('profitMargin').innerText = profitMargin +
                '%';

            // Update Chart
            const ctx = document.getElementById('financeChart').getContext('2d');
            if (financeChartInstance) financeChartInstance.destroy();

            // Gradients
            let gradientIncome = ctx.createLinearGradient(0, 0, 0, 400);
            gradientIncome.addColorStop(0, 'rgba(27, 77, 62, 0.8)');
            gradientIncome.addColorStop(1, 'rgba(27, 77, 62, 0.1)');

            let gradientCost = ctx.createLinearGradient(0, 0, 0, 400);
            gradientCost.addColorStop(0, 'rgba(239, 68, 68, 0.8)');
            gradientCost.addColorStop(1, 'rgba(239, 68, 68, 0.1)');

            financeChartInstance = new Chart(ctx, {
                type: currentChartType,
                data: {
                    labels: data.labels,
                    datasets: [{
                            label: 'รายรับ',
                            data: data.income,
                            backgroundColor: gradientIncome,
                            borderColor: '#1B4D3E',
                            borderWidth: 2,
                            borderRadius: 8,
                            order: 2,
                            yAxisID: 'y',
                            tension: 0.4
                        },
                        {
                            label: 'ต้นทุน',
                            data: data.costs,
                            type: 'line',
                            borderColor: '#ef4444',
                            backgroundColor: gradientCost,
                            borderWidth: 3,
                            tension: 0.4,
                            fill: true,
                            pointRadius: 0,
                            pointHoverRadius: 6,
                            order: 1,
                            yAxisID: 'y'
                        },
                        {
                            label: 'ชั่วโมง',
                            data: data.hours,
                            type: 'line',
                            borderColor: '#60a5fa',
                            borderWidth: 3,
                            borderDash: [8, 4],
                            pointStyle: 'circle',
                            pointRadius: 4,
                            pointBackgroundColor: '#fff',
                            pointBorderColor: '#60a5fa',
                            pointBorderWidth: 2,
                            tension: 0.4,
                            order: 0,
                            yAxisID: 'y1'
                        }
                    ]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    interaction: {
                        mode: 'index',
                        intersect: false
                    },
                    plugins: {
                        legend: {
                            display: false
                        },
                        tooltip: {
                            backgroundColor: 'rgba(0, 0, 0, 0.8)',
                            padding: 12,
                            titleFont: {
                                size: 14,
                                weight: 'bold'
                            },
                            bodyFont: {
                                size: 13
                            },
                            borderColor: 'rgba(255, 255, 255, 0.1)',
                            borderWidth: 1
                        }
                    },
                    scales: {
                        y: {
                            beginAtZero: true,
                            position: 'left',
                            grid: {
                                borderDash: [3, 3],
                                color: 'rgba(0, 0, 0, 0.05)'
                            },
                            ticks: {
                                font: {
                                    weight: 'bold'
                                }
                            }
                        },
                        y1: {
                            beginAtZero: true,
                            position: 'right',
                            grid: {
                                display: false
                            },
                            ticks: {
                                font: {
                                    weight: 'bold'
                                }
                            }
                        },
                        x: {
                            grid: {
                                display: false
                            },
                            ticks: {
                                font: {
                                    weight: 'bold'
                                }
                            }
                        }
                    },
                    animation: {
                        duration: 1500,
                        easing: 'easeInOutQuart'
                    }
                }
            });
        }

        // --- Toggle Chart Type ---
        function toggleChartType(type) {
            currentChartType = type;
            fetchChartData();
        }

        // --- Toggle Dataset Visibility ---
        function toggleDataset(index) {
            if (financeChartInstance) {
                const meta = financeChartInstance.getDatasetMeta(index);
                meta.hidden = !meta.hidden;
                financeChartInstance.update();
            }
        }

        // --- Quick Filter Functions ---
        function setQuickFilter(period) {
            const endDate = new Date();
            let startDate = new Date();

            switch (period) {
                case 'today':
                    startDate = new Date();
                    break;
                case 'week':
                    startDate.setDate(endDate.getDate() - 7);
                    break;
                case 'month':
                    startDate.setMonth(endDate.getMonth() - 1);
                    break;
                case 'year':
                    startDate.setFullYear(endDate.getFullYear() - 1);
                    break;
            }

            document.getElementById('startDate').value = startDate.toISOString().split('T')[0];
            document.getElementById('endDate').value = endDate.toISOString().split('T')[0];
            fetchChartData();
        }

        // --- 🗓️ Calendar Logic - ENHANCED ---
        function calendarApp() {
            return {
                monthNames: ['มกราคม', 'กุมภาพันธ์', 'มีนาคม', 'เมษายน', 'พฤษภาคม', 'มิถุนายน', 'กรกฎาคม', 'สิงหาคม',
                    'กันยายน', 'ตุลาคม', 'พฤศจิกายน', 'ธันวาคม'
                ],
                currentMonth: new Date().getMonth(),
                currentYear: new Date().getFullYear(),
                blankDays: [],
                noOfDays: [],
                events: @json($calendarBookings ?? []),
                showModal: false,
                selectedEvent: null,

                initCalendar() {
                    this.calculateDays();
                },

                calculateDays() {
                    let firstDayOfMonth = new Date(this.currentYear, this.currentMonth, 1).getDay();
                    let daysInMonth = new Date(this.currentYear, this.currentMonth + 1, 0).getDate();
                    this.blankDays = Array.from({
                        length: firstDayOfMonth
                    }, (_, i) => i);
                    this.noOfDays = Array.from({
                        length: daysInMonth
                    }, (_, i) => i + 1);
                },

                prevMonth() {
                    if (this.currentMonth === 0) {
                        this.currentMonth = 11;
                        this.currentYear--;
                    } else {
                        this.currentMonth--;
                    }
                    this.calculateDays();
                },
                nextMonth() {
                    if (this.currentMonth === 11) {
                        this.currentMonth = 0;
                        this.currentYear++;
                    } else {
                        this.currentMonth++;
                    }
                    this.calculateDays();
                },
                goToToday() {
                    this.currentMonth = new Date().getMonth();
                    this.currentYear = new Date().getFullYear();
                    this.calculateDays();
                },
                isToday(date) {
                    const today = new Date();
                    return date === today.getDate() && this.currentMonth === today.getMonth() && this.currentYear === today
                        .getFullYear();
                },
                isWeekend(date) {
                    const dayOfWeek = new Date(this.currentYear, this.currentMonth, date).getDay();
                    return dayOfWeek === 0 || dayOfWeek === 6;
                },
                getEvents(date) {
                    let month = (this.currentMonth + 1).toString().padStart(2, '0');
                    let day = date.toString().padStart(2, '0');
                    return this.events.filter(e => e.start_date === `${this.currentYear}-${month}-${day}`);
                },
                openEventModal(event) {
                    this.selectedEvent = event;
                    this.showModal = true;
                }
            }
        }

        // --- Dummy Functions for UI Demo ---
        function showQuickAddJob() {
            document.getElementById('quickAddJobModal').classList.remove('hidden');
        }

        function closeQuickAddJob() {
            document.getElementById('quickAddJobModal').classList.add('hidden');
        }

        function submitQuickJob() {
            alert('✅ บันทึกงานใหม่สำเร็จ!');
            closeQuickAddJob();
        } // TODO: Implement AJAX

        function openReportModal() {
            document.getElementById('reportModal').classList.remove('hidden');
        }

        function closeReportModal() {
            document.getElementById('reportModal').classList.add('hidden');
        }

        function exportReport(type) {
            alert(`📄 กำลังส่งออกรายงานเป็น ${type.toUpperCase()}...`);
            closeReportModal();
        } // TODO: Implement Export

        function showNotificationCenter() {
            document.getElementById('notificationCenter').classList.remove('hidden');
        }

        function closeNotificationCenter() {
            document.getElementById('notificationCenter').classList.add('hidden');
        }

        // --- Real-time Toggle ---
        let realTimeMode = false;
        let realTimeInterval = null;

        function toggleRealTimeMode() {
            realTimeMode = !realTimeMode;
            if (realTimeMode) {
                alert('🔴 เปิดโหมด Real-time แล้ว! (อัปเดตทุก 30 วินาที)');
                realTimeInterval = setInterval(() => {
                    fetchChartData();
                }, 30000);
            } else {
                alert('⏸️ ปิดโหมด Real-time');
                clearInterval(realTimeInterval);
            }
        }
    </script>

    <style>
        /* Enhanced Scrollbar */
        .custom-scrollbar::-webkit-scrollbar {
            width: 6px;
            height: 6px;
        }

        .custom-scrollbar::-webkit-scrollbar-thumb {
            background: linear-gradient(180deg, #1B4D3E, #22c55e);
            border-radius: 10px;
        }

        .custom-scrollbar::-webkit-scrollbar-track {
            background: rgba(0, 0, 0, 0.05);
            border-radius: 10px;
        }

        /* Gradient Animation */
        @keyframes gradient {
            0% {
                background-position: 0% 50%;
            }

            50% {
                background-position: 100% 50%;
            }

            100% {
                background-position: 0% 50%;
            }
        }

        .animate-gradient {
            background-size: 200% 200%;
            animation: gradient 8s ease infinite;
        }

        .bg-size-200 {
            background-size: 200% 200%;
        }

        /* Pulse Animation */
        @keyframes pulse-glow {

            0%,
            100% {
                opacity: 1;
                transform: scale(1);
            }

            50% {
                opacity: 0.8;
                transform: scale(1.05);
            }
        }

        .animate-pulse {
            animation: pulse-glow 2s cubic-bezier(0.4, 0, 0.6, 1) infinite;
        }

        /* Smooth Transitions */
        * {
            transition-timing-function: cubic-bezier(0.4, 0, 0.2, 1);
        }
    </style>
@endpush