@extends('layouts.admin')

@section('title', 'ระบบจัดการซ่อมบำรุง')
@section('header', 'ศูนย์ซ่อมบำรุงเครื่องจักร (Maintenance)')

@section('content')
    <div class="max-w-7xl mx-auto space-y-6" x-data="{ finishModal: false, logId: '', equipName: '' }">

        {{-- 🌟 Header & Action --}}
        <div class="flex justify-between items-center bg-white p-4 rounded-2xl shadow-sm border border-gray-100">
            <div class="flex items-center gap-3">
                <div class="w-12 h-12 bg-red-100 text-red-600 rounded-xl flex items-center justify-center text-xl">
                    <i class="fa-solid fa-screwdriver-wrench"></i>
                </div>
                <div>
                    <h2 class="font-bold text-gray-800 text-lg">ภาพรวมการซ่อมบำรุง</h2>
                    <p class="text-xs text-gray-500">จัดการแจ้งซ่อมจากพนักงานและรอบเช็คระยะ</p>
                </div>
            </div>
            <a href="{{ route('admin.maintenance.create') }}"
                class="bg-agri-primary hover:bg-green-700 text-white px-5 py-2.5 rounded-xl font-bold text-sm shadow-md transition flex items-center gap-2">
                <i class="fa-solid fa-plus"></i> เปิดบิลซ่อมเอง
            </a>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

            {{-- 🔴 คอลัมน์ซ้าย: แจ้งซ่อม & ถึงระยะ --}}
            <div class="space-y-6 lg:col-span-1">

                {{-- 1. รอรับเรื่องจากพนักงาน --}}
                <div class="bg-white rounded-2xl shadow-sm border border-red-200 overflow-hidden">
                    <div class="bg-red-50 p-4 border-b border-red-100 flex justify-between items-center">
                        <h3 class="font-bold text-red-800 flex items-center gap-2">
                            <i class="fa-solid fa-bell text-red-500 animate-ring"></i> พนักงานแจ้งรถเสีย
                        </h3>
                        <span
                            class="bg-red-600 text-white px-2 py-0.5 rounded-full text-xs font-bold">{{ $reportedIssues->count() }}</span>
                    </div>
                    <div class="p-4 space-y-3">
                        @forelse($reportedIssues as $issue)
                            <div class="border border-red-100 bg-white p-3 rounded-xl shadow-sm">
                                <div class="flex justify-between items-start mb-2">
                                    <span class="font-bold text-gray-800">{{ $issue->equipment->name }}</span>
                                    <span
                                        class="text-[10px] text-gray-400">{{ \Carbon\Carbon::parse($issue->created_at)->diffForHumans() }}</span>
                                </div>
                                <p class="text-xs text-gray-600 mb-3 line-clamp-2">"{{ $issue->description }}"</p>
                                <a href="{{ route('admin.maintenance.show_accept', $issue->id) }}"
                                    class="block w-full text-center bg-red-100 hover:bg-red-200 text-red-700 text-xs font-bold py-2 rounded-lg transition">
                                    ตรวจสอบและรับเรื่อง
                                </a>
                            </div>
                        @empty
                            <p class="text-sm text-gray-400 text-center py-4">ไม่มีรายการแจ้งซ่อมใหม่</p>
                        @endforelse
                    </div>
                </div>

                {{-- 2. ถึงรอบเช็คระยะ --}}
                <div class="bg-white rounded-2xl shadow-sm border border-yellow-200 overflow-hidden">
                    <div class="bg-yellow-50 p-4 border-b border-yellow-100 flex justify-between items-center">
                        <h3 class="font-bold text-yellow-800 flex items-center gap-2">
                            <i class="fa-solid fa-clock-rotate-left text-yellow-600"></i> ถึงรอบเช็คระยะ
                        </h3>
                        <span
                            class="bg-yellow-500 text-white px-2 py-0.5 rounded-full text-xs font-bold">{{ $needMaintenance->count() }}</span>
                    </div>
                    <div class="p-4 space-y-3">
                        @forelse($needMaintenance as $eq)
                            <div class="border border-yellow-100 bg-white p-3 rounded-xl flex justify-between items-center">
                                <div>
                                    <p class="font-bold text-gray-800 text-sm">{{ $eq->name }}</p>
                                    <p class="text-[10px] text-yellow-600 font-bold mt-0.5">ใช้งานไปแล้ว
                                        {{ number_format($eq->current_hours) }} /
                                        {{ number_format($eq->maintenance_hour_threshold) }} ชม.</p>
                                </div>
                                <form action="{{ route('admin.maintenance.start', $eq->id) }}" method="POST">
                                    @csrf
                                    <button
                                        class="bg-yellow-100 hover:bg-yellow-200 text-yellow-700 w-8 h-8 rounded-lg flex items-center justify-center transition"
                                        title="ส่งเข้าเช็คระยะ">
                                        <i class="fa-solid fa-arrow-right"></i>
                                    </button>
                                </form>
                            </div>
                        @empty
                            <p class="text-sm text-gray-400 text-center py-4">ไม่มีรถถึงรอบเช็คระยะ</p>
                        @endforelse
                    </div>
                </div>

            </div>

            {{-- 🔵 คอลัมน์ขวา: กำลังซ่อม & ประวัติ --}}
            <div class="space-y-6 lg:col-span-2">

                {{-- 3. รถที่กำลังซ่อมอยู่ --}}
                <div class="bg-white rounded-2xl shadow-sm border border-blue-200 overflow-hidden">
                    <div class="bg-blue-50 p-4 border-b border-blue-100 flex justify-between items-center">
                        <h3 class="font-bold text-blue-800 flex items-center gap-2">
                            <i class="fa-solid fa-gear text-blue-500 animate-spin-slow"></i> รถที่กำลังซ่อมอยู่ตอนนี้
                        </h3>
                        <span
                            class="bg-blue-600 text-white px-2 py-0.5 rounded-full text-xs font-bold">{{ $inMaintenance->count() }}</span>
                    </div>
                    <div class="p-4 grid grid-cols-1 md:grid-cols-2 gap-4">
                        @forelse($inMaintenance as $log)
                            <div
                                class="border border-blue-100 bg-white p-4 rounded-xl shadow-sm relative overflow-hidden group">
                                <div class="absolute right-0 top-0 opacity-5 group-hover:scale-110 transition p-2">
                                    <i class="fa-solid fa-wrench text-6xl text-blue-600"></i>
                                </div>
                                <div class="relative z-10">
                                    <h4 class="font-bold text-gray-800 text-lg">{{ $log->equipment->name }}</h4>
                                    <p class="text-xs text-blue-600 font-bold mb-2">รหัส:
                                        {{ $log->equipment->equipment_code }}</p>
                                    <p class="text-xs text-gray-500 line-clamp-2 mb-4 h-8">{{ $log->description }}</p>

                                    <button
                                        @click="finishModal = true; logId = {{ $log->id }}; equipName = '{{ $log->equipment->name }}'"
                                        class="w-full bg-blue-600 hover:bg-blue-700 text-white text-sm font-bold py-2.5 rounded-lg shadow-sm transition flex items-center justify-center gap-2">
                                        <i class="fa-solid fa-check-double"></i> บันทึกซ่อมเสร็จ
                                    </button>
                                </div>
                            </div>
                        @empty
                            <div class="col-span-1 md:col-span-2 text-center py-8 text-gray-400">
                                <i class="fa-solid fa-mug-hot text-4xl mb-2 opacity-50"></i>
                                <p class="text-sm font-bold">ไม่มีรถกำลังซ่อม</p>
                            </div>
                        @endforelse
                    </div>
                </div>

                {{-- 4. ประวัติการซ่อมล่าสุด --}}
                <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
                    <div class="bg-gray-50 p-4 border-b border-gray-100">
                        <h3 class="font-bold text-gray-800 flex items-center gap-2">
                            <i class="fa-solid fa-clock-rotate-left text-gray-500"></i> ประวัติงานซ่อมล่าสุด
                        </h3>
                    </div>
                    <div class="p-0 overflow-x-auto">
                        <table class="w-full text-left text-sm whitespace-nowrap">
                            <thead class="bg-white border-b text-gray-500">
                                <tr>
                                    <th class="px-4 py-3">วันที่ซ่อมเสร็จ</th>
                                    <th class="px-4 py-3">เครื่องจักร</th>
                                    <th class="px-4 py-3">รายละเอียด</th>
                                    <th class="px-4 py-3">อู่/ช่าง</th>
                                    <th class="px-4 py-3 text-right">ค่าใช้จ่าย</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-50">
                                @foreach ($history as $log)
                                    <tr class="hover:bg-gray-50 transition">
                                        <td class="px-4 py-3 text-xs">
                                            {{ \Carbon\Carbon::parse($log->completion_date)->format('d/m/Y') }}</td>
                                        <td class="px-4 py-3 font-bold text-gray-700">{{ $log->equipment->name }}</td>
                                        <td class="px-4 py-3 text-xs text-gray-500 max-w-[200px] truncate"
                                            title="{{ $log->description }}">{{ $log->description }}</td>
                                        <td class="px-4 py-3 text-xs">{{ $log->service_provider ?? '-' }}</td>
                                        {{-- 🟢 อัปเดตส่วนปุ่มแสดงรูปใบเสร็จตรงนี้ --}}
                                        <td class="px-4 py-3 text-right font-bold text-red-500">
                                            <div class="flex items-center justify-end gap-2">
                                                @if($log->receipt_image)
                                                    <a href="{{ asset('storage/' . $log->receipt_image) }}" target="_blank" class="text-blue-500 hover:text-blue-700 bg-blue-50 px-2 py-1 rounded-md text-xs transition border border-blue-200" title="ดูรูปใบเสร็จ">
                                                        <i class="fa-solid fa-file-invoice"></i> บิล
                                                    </a>
                                                @endif
                                                <span>{{ number_format($log->total_cost) }} ฿</span>
                                            </div>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>

            </div>
        </div>

        {{-- 🔥 MODAL: จบงานซ่อม --}}
        <div x-show="finishModal" class="fixed inset-0 z-50 overflow-y-auto" style="display: none;">
            <div class="flex items-center justify-center min-h-screen px-4 pt-4 pb-20 text-center sm:p-0">
                <div class="fixed inset-0 transition-opacity bg-gray-900/75 backdrop-blur-sm" @click="finishModal = false">
                </div>

                <div
                    class="relative inline-block w-full max-w-md p-6 overflow-hidden text-left align-middle transition-all transform bg-white shadow-xl rounded-2xl">
                    <div class="flex justify-between items-center mb-4 pb-3 border-b border-gray-100">
                        <h3 class="text-lg font-bold text-gray-900"><i class="fa-solid fa-check-double text-blue-500"></i>
                            บันทึกซ่อมเสร็จ</h3>
                        <button @click="finishModal = false" class="text-gray-400 hover:text-gray-600"><i
                                class="fa-solid fa-times"></i></button>
                    </div>

                    <p class="text-sm text-gray-500 mb-4">เครื่องจักร: <span class="font-bold text-blue-600"
                            x-text="equipName"></span></p>

                    <form :action="'/admin/maintenance/log/' + logId + '/finish'" method="POST" enctype="multipart/form-data">
                        @csrf
                        <div class="space-y-4">
                            <div>
                                <label class="block text-sm font-bold text-gray-700 mb-1">ค่าใช้จ่ายในการซ่อม (บาท)
                                    *</label>
                                <input type="number" name="total_cost" required min="0"
                                    class="w-full rounded-xl border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 bg-blue-50/50">
                            </div>
                            <div>
                                <label class="block text-sm font-bold text-gray-700 mb-1">อู่ / ศูนย์บริการ / ช่างที่ซ่อม
                                    (ถ้ามี)</label>
                                <input type="text" name="service_provider"
                                    class="w-full rounded-xl border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"
                                    placeholder="เช่น ศูนย์คูโบต้า หรือ เปลี่ยนเอง">
                            </div>

                            {{-- 🟢 เพิ่มช่องแนบใบเสร็จตรงนี้ --}}
                            <div class="bg-gray-50 p-3 rounded-xl border border-gray-200 border-dashed">
                                <label class="block text-sm font-bold text-gray-700 mb-2"><i
                                        class="fa-solid fa-receipt text-gray-400"></i> ถ่ายรูปใบเสร็จ/บิลค่าซ่อม
                                    (ถ้ามี)</label>
                                <input type="file" name="receipt_image" accept="image/*" capture="environment"
                                    class="block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-sm file:font-semibold file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100 cursor-pointer">
                            </div>

                            <div>
                                <label class="block text-sm font-bold text-gray-700 mb-1">รายละเอียดเพิ่มเติม /
                                    อะไหล่ที่เปลี่ยน</label>
                                <textarea name="note" rows="2"
                                    class="w-full rounded-xl border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"></textarea>
                            </div>
                            <div class="bg-yellow-50 p-3 rounded-xl border border-yellow-200">
                                <label class="flex items-start gap-3 cursor-pointer">
                                    <input type="checkbox" name="reset_hours" value="1"
                                        class="mt-1 w-5 h-5 text-yellow-600 rounded border-gray-300 focus:ring-yellow-500">
                                    <div>
                                        <span class="block text-sm font-bold text-yellow-800">รีเซ็ตชั่วโมงการทำงานเป็น
                                            0</span>
                                        <span
                                            class="block text-xs text-yellow-700 mt-0.5">เลือกเมื่อเป็นการถ่ายน้ำมันเครื่องรอบใหญ่
                                            เพื่อเริ่มนับชั่วโมงใหม่</span>
                                    </div>
                                </label>
                            </div>
                        </div>
                        <div class="mt-6 flex justify-end gap-3">
                            <button type="button" @click="finishModal = false"
                                class="px-4 py-2 bg-white border border-gray-300 rounded-xl font-bold text-gray-700 hover:bg-gray-50">ยกเลิก</button>
                            <button type="submit"
                                class="px-6 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-xl font-bold shadow-md flex items-center gap-2"><i
                                    class="fa-solid fa-save"></i> ยืนยันซ่อมเสร็จ</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection