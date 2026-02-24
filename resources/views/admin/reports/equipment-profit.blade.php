@extends('layouts.admin')

@section('title', 'รายงานกำไร/ขาดทุนรายตัวรถ')
@section('header', 'รายงานกำไร/ขาดทุนรายตัวรถ')

@section('content')
<div class="max-w-6xl mx-auto space-y-6">
    <form action="{{ route('admin.reports.equipment_profit') }}" method="GET"
        class="bg-white p-4 rounded-2xl shadow-sm border border-gray-100 flex flex-col lg:flex-row gap-4 lg:items-end">
        <div class="flex-1">
            <label class="block text-xs text-gray-500 font-bold mb-2">เริ่มวันที่</label>
            <input type="date" name="start_date" value="{{ $startDate }}"
                class="w-full rounded-xl border-gray-200 focus:ring-2 focus:ring-agri-accent/40 focus:border-agri-primary px-3 py-2">
        </div>
        <div class="flex-1">
            <label class="block text-xs text-gray-500 font-bold mb-2">ถึงวันที่</label>
            <input type="date" name="end_date" value="{{ $endDate }}"
                class="w-full rounded-xl border-gray-200 focus:ring-2 focus:ring-agri-accent/40 focus:border-agri-primary px-3 py-2">
        </div>
        <div>
            <button type="submit"
                class="w-full lg:w-auto bg-agri-primary text-white px-6 py-2.5 rounded-xl font-bold shadow-lg shadow-agri-primary/30 hover:bg-agri-hover transition">
                ดูรายงาน
            </button>
        </div>
    </form>

    <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
        <div class="bg-white p-4 rounded-2xl border border-gray-100 shadow-sm">
            <p class="text-xs text-gray-500 font-bold">รายได้รวม</p>
            <p class="text-2xl font-bold text-gray-800">฿{{ number_format($totals->revenue, 2) }}</p>
        </div>
        <div class="bg-white p-4 rounded-2xl border border-gray-100 shadow-sm">
            <p class="text-xs text-gray-500 font-bold">ค่าน้ำมันรวม</p>
            <p class="text-2xl font-bold text-red-500">-฿{{ number_format($totals->fuel_cost, 2) }}</p>
        </div>
        <div class="bg-white p-4 rounded-2xl border border-gray-100 shadow-sm">
            <p class="text-xs text-gray-500 font-bold">ค่าซ่อมรวม</p>
            <p class="text-2xl font-bold text-orange-500">-฿{{ number_format($totals->maintenance_cost, 2) }}</p>
        </div>
        <div class="bg-white p-4 rounded-2xl border border-gray-100 shadow-sm">
            <p class="text-xs text-gray-500 font-bold">กำไรสุทธิ</p>
            <p class="text-2xl font-bold {{ $totals->profit >= 0 ? 'text-green-600' : 'text-red-600' }}">
                ฿{{ number_format($totals->profit, 2) }}
            </p>
        </div>
    </div>

    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-50 flex items-center justify-between bg-gray-50/50">
            <h3 class="font-bold text-gray-800">สรุปกำไร/ขาดทุนรายตัวรถ</h3>
            <span class="text-xs text-gray-500">ช่วง {{ $startDate }} ถึง {{ $endDate }}</span>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead class="bg-gray-50 text-gray-500 font-medium">
                    <tr>
                        <th class="px-6 py-3 text-left">เครื่องจักร</th>
                        <th class="px-6 py-3 text-right">รายได้</th>
                        <th class="px-6 py-3 text-right">ค่าน้ำมัน</th>
                        <th class="px-6 py-3 text-right">ค่าซ่อม</th>
                        <th class="px-6 py-3 text-right">กำไร/ขาดทุน</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse ($rows as $row)
                        <tr class="hover:bg-gray-50">
                            <td class="px-6 py-3">
                                <div class="font-bold text-gray-800">{{ $row->equipment->name }}</div>
                                <div class="text-xs text-gray-400">{{ $row->equipment->equipment_code }}</div>
                            </td>
                            <td class="px-6 py-3 text-right text-gray-700">฿{{ number_format($row->revenue, 2) }}</td>
                            <td class="px-6 py-3 text-right text-red-500">-฿{{ number_format($row->fuel_cost, 2) }}</td>
                            <td class="px-6 py-3 text-right text-orange-500">-฿{{ number_format($row->maintenance_cost, 2) }}</td>
                            <td class="px-6 py-3 text-right font-bold {{ $row->profit >= 0 ? 'text-green-600' : 'text-red-600' }}">
                                ฿{{ number_format($row->profit, 2) }}
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-6 py-6 text-center text-gray-400">ไม่มีข้อมูลในช่วงเวลานี้</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
