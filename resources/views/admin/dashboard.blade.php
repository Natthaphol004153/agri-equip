@extends('layouts.admin')

@section('title', 'Admin Dashboard')
@section('header', 'ภาพรวมระบบปฏิบัติการ')

@section('content')
    <div class="max-w-7xl mx-auto space-y-6 pb-12" x-data="calendarApp()" x-init="initCalendar()">

        {{-- 🎯 1. Quick Action Bar --}}
        <div class="bg-gradient-to-r from-agri-primary via-green-700 to-agri-primary bg-size-200 animate-gradient rounded-2xl shadow-xl p-6 text-white relative overflow-hidden">
            <div class="absolute top-0 right-0 w-64 h-64 bg-white/5 rounded-full -mr-32 -mt-32 pointer-events-none"></div>
            <div class="absolute bottom-0 left-0 w-48 h-48 bg-white/5 rounded-full -ml-24 -mb-24 pointer-events-none"></div>

            <div class="relative z-10 flex flex-col md:flex-row items-center justify-between gap-4">
                <div class="text-center md:text-left">
                    <h2 class="text-2xl font-bold mb-1 flex items-center gap-2">
                        <i class="fa-solid fa-bolt text-yellow-300"></i> ศูนย์ปฏิบัติการ
                    </h2>
                    <p class="text-green-100 text-sm">ยินดีต้อนรับ! นี่คือสรุปงานที่คุณต้องจัดการในวันนี้</p>
                </div>

                <div class="flex flex-wrap gap-3 justify-center">
                    <a href="{{ route('admin.jobs.create') }}" class="group bg-white/20 hover:bg-white/30 backdrop-blur-sm px-5 py-3 rounded-xl flex items-center gap-2 transition-all hover:scale-105 shadow-lg">
                        <div class="w-8 h-8 bg-white/20 rounded-lg flex items-center justify-center group-hover:rotate-90 transition-transform">
                            <i class="fa-solid fa-plus"></i>
                        </div>
                        <span class="font-bold text-sm">สร้างการจองใหม่</span>
                    </a>
                </div>
            </div>
        </div>

        {{-- 🔴 2. Alert Section (แจ้งเตือนรถเสีย / ซ่อมบำรุง) --}}
        @if (isset($maintenanceAlerts) && count($maintenanceAlerts) > 0)
            <div class="bg-red-50 rounded-2xl shadow-sm border border-red-200 overflow-hidden relative p-4 flex flex-col sm:flex-row sm:items-center justify-between gap-4">
                <div class="flex items-center gap-4">
                    <div class="bg-red-100 w-12 h-12 rounded-full flex items-center justify-center text-red-600 animate-pulse shrink-0">
                        <i class="fa-solid fa-triangle-exclamation text-2xl"></i>
                    </div>
                    <div>
                        <h3 class="font-bold text-red-800 text-lg">แจ้งเตือนด่วน: เครื่องจักรถึงรอบซ่อมบำรุง ({{ count($maintenanceAlerts) }} คัน)</h3>
                        <p class="text-sm text-red-600">กรุณาตรวจสอบและเปิดใบแจ้งซ่อม เพื่อป้องกันความเสียหายระหว่างปฏิบัติงาน</p>
                    </div>
                </div>
                <a href="{{ route('admin.maintenance.index') }}" class="shrink-0 px-4 py-2 bg-red-600 hover:bg-red-700 text-white text-sm font-bold rounded-xl shadow-md transition w-full sm:w-auto text-center">
                    จัดการซ่อมบำรุง <i class="fa-solid fa-arrow-right ml-1"></i>
                </a>
            </div>
        @endif

        {{-- 🚜 3. Operational Stats (ตัวเลขภาพรวมปัจจุบัน) --}}
        <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
            {{-- Pending Jobs (สำคัญที่สุด ต้องจัดการก่อน) --}}
            <a href="{{ route('admin.jobs.index', ['status' => 'pending_approval']) }}" class="bg-orange-50 p-5 rounded-2xl shadow-sm border border-orange-100 flex flex-col justify-between h-32 relative overflow-hidden group hover:shadow-md transition-all hover:-translate-y-1">
                <div class="absolute right-0 top-0 p-3 opacity-10 group-hover:scale-125 transition-transform duration-500">
                    <i class="fa-solid fa-clock text-6xl text-orange-600"></i>
                </div>
                <div>
                    <p class="text-sm text-orange-800 font-bold flex items-center gap-1.5"><i class="fa-solid fa-circle-exclamation animate-pulse text-orange-500"></i> รอตรวจสอบ/อนุมัติ</p>
                    <h3 class="text-3xl font-black text-orange-600 mt-1">{{ $pendingJobsCount ?? 0 }} <span class="text-sm font-normal text-orange-400">งาน</span></h3>
                </div>
            </a>

            {{-- Active Machines --}}
            <div class="bg-blue-50 p-5 rounded-2xl shadow-sm border border-blue-100 flex flex-col justify-between h-32 relative overflow-hidden group hover:shadow-md transition-all">
                <div class="absolute right-0 top-0 p-3 opacity-10 group-hover:scale-125 transition-transform duration-500">
                    <i class="fa-solid fa-tractor text-6xl text-blue-600"></i>
                </div>
                <div>
                    <p class="text-sm text-blue-800 font-bold flex items-center gap-1.5"><i class="fa-solid fa-circle text-[8px] text-blue-500 animate-ping"></i> กำลังทำงานตอนนี้</p>
                    <h3 class="text-3xl font-black text-blue-600 mt-1">{{ $activeMachines ?? 0 }} <span class="text-sm font-normal text-blue-400">คัน</span></h3>
                </div>
            </div>

            {{-- Available Staff --}}
            <div class="bg-purple-50 p-5 rounded-2xl shadow-sm border border-purple-100 flex flex-col justify-between h-32 relative overflow-hidden group hover:shadow-md transition-all">
                <div class="absolute right-0 top-0 p-3 opacity-10 group-hover:scale-125 transition-transform duration-500">
                    <i class="fa-solid fa-users text-6xl text-purple-600"></i>
                </div>
                <div>
                    <p class="text-sm text-purple-800 font-bold">พนักงานในระบบ</p>
                    <h3 class="text-3xl font-black text-purple-600 mt-1">{{ $availableStaff ?? 0 }} <span class="text-sm font-normal text-purple-400">คน</span></h3>
                </div>
            </div>

            {{-- Completed Jobs (Today) --}}
            <div class="bg-green-50 p-5 rounded-2xl shadow-sm border border-green-100 flex flex-col justify-between h-32 relative overflow-hidden group hover:shadow-md transition-all">
                <div class="absolute right-0 top-0 p-3 opacity-10 group-hover:scale-125 transition-transform duration-500">
                    <i class="fa-solid fa-check-double text-6xl text-green-600"></i>
                </div>
                <div>
                    <p class="text-sm text-green-800 font-bold">งานเสร็จสิ้น (เดือนนี้)</p>
                    <h3 class="text-3xl font-black text-green-600 mt-1">{{ $completedJobs ?? 0 }} <span class="text-sm font-normal text-green-400">งาน</span></h3>
                </div>
            </div>
        </div>

        {{-- 📋 4. Today's Action & Schedule (แบ่ง 2 คอลัมน์) --}}
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
            
            {{-- ฝั่งซ้าย: งานวันนี้ --}}
            <div class="bg-white rounded-2xl shadow-sm border border-gray-200 overflow-hidden flex flex-col h-[400px]">
                <div class="p-4 border-b border-gray-100 bg-gray-50 flex justify-between items-center">
                    <h3 class="font-bold text-gray-800 flex items-center gap-2">
                        <i class="fa-solid fa-calendar-day text-agri-primary"></i> คิวงานวันนี้ (Today's Jobs)
                    </h3>
                    <span class="text-xs bg-agri-primary text-white px-2 py-1 rounded-lg font-bold">{{ date('d M Y') }}</span>
                </div>
                <div class="p-4 flex-1 overflow-y-auto custom-scrollbar space-y-3">
                    @forelse ($todayJobs ?? [] as $job)
                        @php
                            // แปลงสถานะเป็นรูปแบบและสีที่เข้าใจง่าย
                            $statusConfig = match ($job->status ?? 'pending') {
                                'pending' => ['label' => 'รออนุมัติ', 'color' => 'text-gray-600 bg-gray-100 border-gray-200', 'icon' => 'fa-clock'],
                                'pending_approval' => ['label' => 'รออนุมัติ', 'color' => 'text-gray-600 bg-gray-100 border-gray-200', 'icon' => 'fa-clock'],
                                'scheduled' => ['label' => 'รอเริ่มงาน', 'color' => 'text-yellow-700 bg-yellow-50 border-yellow-200', 'icon' => 'fa-calendar-check'],
                                'in_progress' => ['label' => 'กำลังทำงาน', 'color' => 'text-blue-700 bg-blue-50 border-blue-200 animate-pulse', 'icon' => 'fa-spinner fa-spin'],
                                'completed_pending_approval' => ['label' => 'รอตรวจเงิน', 'color' => 'text-orange-700 bg-orange-50 border-orange-200', 'icon' => 'fa-file-invoice-dollar'],
                                'completed' => ['label' => 'เสร็จสิ้น', 'color' => 'text-green-700 bg-green-50 border-green-200', 'icon' => 'fa-check-circle'],
                                'cancelled' => ['label' => 'ยกเลิก', 'color' => 'text-red-700 bg-red-50 border-red-200', 'icon' => 'fa-ban'],
                                default => ['label' => $job->status ?? '-', 'color' => 'text-gray-600 bg-gray-100', 'icon' => 'fa-circle'],
                            };
                        @endphp
                        <div class="border border-gray-100 p-3 rounded-xl hover:border-agri-primary/50 transition bg-white shadow-sm flex flex-col justify-between">
                            <div class="flex justify-between items-start mb-2">
                                <div class="flex items-center gap-2">
                                    <span class="font-bold text-gray-800">{{ $job->job_number }}</span>
                                    {{-- 🟢 ป้ายสถานะ --}}
                                    <span class="text-[10px] px-2 py-0.5 rounded-md border font-bold flex items-center {{ $statusConfig['color'] }}">
                                        <i class="fa-solid {{ $statusConfig['icon'] }} mr-1"></i> {{ $statusConfig['label'] }}
                                    </span>
                                </div>
                                <span class="text-[10px] bg-gray-50 text-gray-600 px-2 py-1 rounded-lg font-bold border border-gray-100 whitespace-nowrap">
                                    <i class="fa-regular fa-clock mr-1"></i>{{ $job->time_range }}
                                </span>
                            </div>
                            
                            <div class="flex justify-between items-end">
                                <div>
                                    <p class="text-sm text-gray-600 mb-1"><i class="fa-solid fa-user text-gray-400 text-xs w-4"></i> {{ $job->customer_name }}</p>
                                    <p class="text-xs text-gray-500"><i class="fa-solid fa-tractor text-orange-400 text-xs w-4"></i> {{ $job->equipment_name }}</p>
                                </div>
                                {{-- 🟢 ปุ่มดูรายละเอียด --}}
                                @if(isset($job->id))
                                    <a href="{{ route('admin.jobs.show', $job->id) }}" class="text-[10px] bg-white border border-gray-200 text-gray-600 px-2 py-1 rounded-md hover:bg-gray-50 transition shadow-sm font-medium">
                                        จัดการ <i class="fa-solid fa-chevron-right ml-0.5"></i>
                                    </a>
                                @endif
                            </div>
                        </div>
                    @empty
                        <div class="flex flex-col items-center justify-center h-full text-gray-400 opacity-50">
                            <i class="fa-solid fa-mug-hot text-5xl mb-3"></i>
                            <p class="font-bold">ไม่มีคิวงานสำหรับวันนี้</p>
                            <p class="text-xs">พักผ่อนให้เต็มที่ครับ</p>
                        </div>
                    @endforelse
                </div>
            </div>

            {{-- ฝั่งขวา: รายการรอดำเนินการ (Action Required) แบบแยกประเภท --}}
            <div class="bg-white rounded-2xl shadow-sm border border-orange-200 overflow-hidden flex flex-col h-[400px]">
                <div class="p-4 border-b border-orange-100 bg-orange-50 flex justify-between items-center">
                    <h3 class="font-bold text-orange-800 flex items-center gap-2">
                        <i class="fa-solid fa-clipboard-list text-orange-600"></i> ต้องจัดการด่วน (Action Required)
                    </h3>
                </div>
                <div class="p-4 flex-1 overflow-y-auto custom-scrollbar space-y-3">
                    
                    {{-- 🚨 ส่วนที่ 1: งานเลยกำหนด / ล่าช้า (Overdue) --}}
                    @foreach ($overdueJobs ?? [] as $job)
                        <div class="flex items-center justify-between border-l-4 border-red-500 bg-red-50 p-3 rounded-r-xl">
                            <div>
                                <p class="text-sm font-bold text-red-800">{{ $job->job_number }}</p>
                                <p class="text-xs text-red-600 mt-0.5">
                                    <i class="fa-solid fa-triangle-exclamation"></i> พนักงานไม่เริ่มงานตามกำหนด
                                </p>
                            </div>
                            <a href="{{ route('admin.jobs.show', $job->id) }}" class="shrink-0 px-3 py-1.5 bg-red-600 text-white hover:bg-red-700 text-xs font-bold rounded-lg transition shadow-sm ml-2">
                                ติดตามงาน
                            </a>
                        </div>
                    @endforeach

                    {{-- 🚨 ส่วนที่ 2: งานรออนุมัติแต่เลยวันแล้ว (Expired) --}}
                    @foreach ($expiredPendingJobs ?? [] as $job)
                        <div class="flex items-center justify-between border-l-4 border-gray-500 bg-gray-100 p-3 rounded-r-xl">
                            <div>
                                <p class="text-sm font-bold text-gray-700">{{ $job->job_number }}</p>
                                <p class="text-xs text-gray-500 mt-0.5">
                                    <i class="fa-solid fa-clock-rotate-left"></i> หมดอายุ (เลยวันนัดแล้ว)
                                </p>
                            </div>
                            <a href="{{ route('admin.jobs.show', $job->id) }}" class="shrink-0 px-3 py-1.5 bg-white border border-gray-300 text-gray-700 hover:bg-gray-200 text-xs font-bold rounded-lg transition ml-2">
                                จัดการ
                            </a>
                        </div>
                    @endforeach

                    {{-- 🟡 ส่วนที่ 3: งานรออนุมัติปกติ (คิวคือวันนี้ หรือ อนาคต) --}}
                    @forelse ($pendingJobs ?? [] as $job)
                        <div class="flex items-center justify-between border-l-4 border-orange-400 bg-orange-50/50 p-3 rounded-r-xl">
                            <div>
                                <p class="text-sm font-bold text-gray-800">{{ $job->job_number }}</p>
                                <p class="text-xs text-gray-500 mt-0.5">รออนุมัติ / นัด: {{ \Carbon\Carbon::parse($job->scheduled_start)->format('d/m/Y') }}</p>
                            </div>
                            <a href="{{ route('admin.jobs.show', $job->id) }}" class="shrink-0 px-3 py-1.5 bg-white border border-orange-200 text-orange-600 hover:bg-orange-600 hover:text-white text-xs font-bold rounded-lg transition ml-2">
                                พิจารณา
                            </a>
                        </div>
                    @empty
                        {{-- ถ้าไม่มีงานอะไรค้างเลย --}}
                        @if(count($overdueJobs ?? []) == 0 && count($expiredPendingJobs ?? []) == 0)
                            <div class="flex flex-col items-center justify-center h-full text-green-500/50 py-10">
                                <i class="fa-solid fa-check-circle text-5xl mb-3"></i>
                                <p class="font-bold">เคลียร์งานหมดแล้ว!</p>
                                <p class="text-xs text-gray-400">ไม่มีรายการค้างในระบบ</p>
                            </div>
                        @endif
                    @endforelse

                </div>
            </div>

        </div>

        {{-- 🗓️ 5. JOB CALENDAR (ภาพรวมรายเดือน) --}}
        <div class="bg-white rounded-2xl shadow-sm border border-gray-200 overflow-hidden relative">
            <div class="px-6 py-4 border-b border-gray-100 flex flex-col sm:flex-row items-center justify-between bg-gray-50 gap-4">
                <h3 class="font-bold text-gray-800 flex items-center gap-2 text-lg">
                    <i class="fa-regular fa-calendar-days text-agri-primary"></i>
                    ปฏิทินงาน (Monthly Schedule)
                </h3>
                
                {{-- Calendar Controls --}}
                <div class="flex items-center gap-3">
                    <div class="flex items-center gap-3 bg-white p-1 rounded-xl border border-gray-200 shadow-sm">
                        <button @click="prevMonth()" class="w-8 h-8 flex items-center justify-center rounded-lg hover:bg-gray-100 text-gray-600 transition">
                            <i class="fa-solid fa-chevron-left"></i>
                        </button>
                        <div class="text-center min-w-[120px]">
                            <span class="text-sm font-bold text-gray-800" x-text="monthNames[currentMonth]"></span>
                            <span class="text-sm font-bold text-gray-800" x-text="currentYear"></span>
                        </div>
                        <button @click="nextMonth()" class="w-8 h-8 flex items-center justify-center rounded-lg hover:bg-gray-100 text-gray-600 transition">
                            <i class="fa-solid fa-chevron-right"></i>
                        </button>
                    </div>
                    <button @click="goToToday()" class="px-4 py-2 rounded-xl bg-white border border-gray-200 text-xs font-bold text-gray-700 hover:bg-gray-50 transition">
                        วันนี้
                    </button>
                </div>
            </div>

            <div class="p-4 md:p-6 bg-white">
                <div class="grid grid-cols-7 mb-2">
                    <template x-for="(day, index) in ['อา', 'จ', 'อ', 'พ', 'พฤ', 'ศ', 'ส']">
                        <div class="text-center py-2" :class="index === 0 || index === 6 ? 'text-red-500' : 'text-gray-500'">
                            <span class="text-xs font-bold uppercase" x-text="day"></span>
                        </div>
                    </template>
                </div>

                <div class="grid grid-cols-7 gap-1 md:gap-2">
                    <template x-for="blank in blankDays">
                        <div class="h-24 md:h-32 bg-gray-50 rounded-lg border border-transparent"></div>
                    </template>

                    <template x-for="(date, index) in noOfDays">
                        <div class="relative h-24 md:h-32 border border-gray-200 rounded-lg p-1 hover:border-agri-primary transition flex flex-col overflow-hidden"
                             :class="{ 'ring-2 ring-agri-primary/50 bg-green-50/20': isToday(date) }">
                            
                            <div class="flex justify-between items-start mb-1">
                                <span class="text-xs font-bold w-6 h-6 flex items-center justify-center rounded-full"
                                      :class="isToday(date) ? 'bg-agri-primary text-white shadow-sm' : 'text-gray-700'"
                                      x-text="date"></span>
                            </div>

                            <div class="flex-1 overflow-y-auto space-y-1 custom-scrollbar">
                                <template x-for="event in getEvents(date)">
                                    <div class="w-full text-left px-1.5 py-1 rounded text-[10px] truncate border" :class="event.status.color">
                                        <span class="font-bold" x-text="event.job_number"></span>
                                        <span x-text="event.title"></span>
                                    </div>
                                </template>
                            </div>
                        </div>
                    </template>
                </div>
            </div>
        </div>

    </div>
@endsection

@push('scripts')
    <script>
        // --- 🗓️ Calendar Logic ---
        function calendarApp() {
            return {
                monthNames: ['มกราคม', 'กุมภาพันธ์', 'มีนาคม', 'เมษายน', 'พฤษภาคม', 'มิถุนายน', 'กรกฎาคม', 'สิงหาคม', 'กันยายน', 'ตุลาคม', 'พฤศจิกายน', 'ธันวาคม'],
                currentMonth: new Date().getMonth(),
                currentYear: new Date().getFullYear(),
                blankDays: [],
                noOfDays: [],
                events: @json($calendarBookings ?? []),

                initCalendar() {
                    this.calculateDays();
                },

                calculateDays() {
                    let firstDayOfMonth = new Date(this.currentYear, this.currentMonth, 1).getDay();
                    let daysInMonth = new Date(this.currentYear, this.currentMonth + 1, 0).getDate();
                    this.blankDays = Array.from({ length: firstDayOfMonth }, (_, i) => i);
                    this.noOfDays = Array.from({ length: daysInMonth }, (_, i) => i + 1);
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
                    return date === today.getDate() && this.currentMonth === today.getMonth() && this.currentYear === today.getFullYear();
                },
                
                getEvents(date) {
                    let month = (this.currentMonth + 1).toString().padStart(2, '0');
                    let day = date.toString().padStart(2, '0');
                    let targetDate = `${this.currentYear}-${month}-${day}`;
                    return this.events.filter(e => e.start_date === targetDate);
                }
            }
        }
    </script>

    <style>
        .custom-scrollbar::-webkit-scrollbar { width: 4px; }
        .custom-scrollbar::-webkit-scrollbar-thumb { background: #cbd5e1; border-radius: 4px; }
        .custom-scrollbar::-webkit-scrollbar-track { background: transparent; }
        
        @keyframes gradient {
            0% { background-position: 0% 50%; }
            50% { background-position: 100% 50%; }
            100% { background-position: 0% 50%; }
        }
        .animate-gradient { background-size: 200% 200%; animation: gradient 8s ease infinite; }
    </style>
@endpush