@extends('layouts.staff')

@section('content')
<div class="container mx-auto px-4 py-6">
    <div class="flex justify-between items-center mb-6">
        <h2 class="text-2xl font-bold text-gray-800">
            📜 ประวัติงานที่ทำเสร็จแล้ว
        </h2>
        <a href="{{ route('staff.jobs.index') }}" class="text-gray-600 hover:text-gray-900 text-sm underline">
            &larr; กลับหน้างานปัจจุบัน
        </a>
    </div>

    <div class="bg-white rounded-lg shadow-lg overflow-hidden border border-gray-100">
        <div class="overflow-x-auto">
            <table class="min-w-full leading-normal">
                <thead class="bg-gray-50 border-b border-gray-200">
                    <tr>
                        <th class="px-5 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">
                            วันที่ / เลข Job
                        </th>
                        <th class="px-5 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">
                            ลูกค้า / รถที่ใช้
                        </th>
                        <th class="px-5 py-3 text-right text-xs font-semibold text-gray-600 uppercase tracking-wider">
                            รายละเอียดบิล (บาท)
                        </th>
                        <th class="px-5 py-3 text-center text-xs font-semibold text-gray-600 uppercase tracking-wider">
                            สถานะงาน
                        </th>
                        <th class="px-5 py-3 text-center text-xs font-semibold text-gray-600 uppercase tracking-wider">
                            จัดการ
                        </th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200">
                    @forelse($historyJobs as $job)
                    @php
                        // คำนวณยอดเงิน
                        $total = $job->total_price;
                        $deposit = $job->deposit_amount;
                        $balance = $total - $deposit;
                    @endphp
                    <tr class="hover:bg-gray-50 transition duration-150">
                        <td class="px-5 py-4 bg-white text-sm">
                            <div class="flex flex-col">
                                <span class="font-bold text-indigo-600 text-md">
                                    {{ $job->job_number }}
                                </span>
                                <span class="text-gray-500 text-xs">
                                    {{ \Carbon\Carbon::parse($job->actual_end)->format('d/m/Y H:i') }}
                                </span>
                            </div>
                        </td>

                        <td class="px-5 py-4 bg-white text-sm">
                            <div class="flex items-center">
                                <div class="ml-3">
                                    <p class="text-gray-900 whitespace-no-wrap font-medium">
                                        👤 {{ $job->customer->name ?? '-' }}
                                    </p>
                                    <p class="text-gray-500 text-xs mt-1">
                                        🚜 {{ $job->equipment->name ?? '-' }}
                                    </p>
                                </div>
                            </div>
                        </td>

                        <td class="px-5 py-4 bg-white text-sm text-right">
                            <div class="flex flex-col space-y-1">
                                <span class="text-gray-800 font-semibold">
                                    ยอดรวม: {{ number_format($total, 2) }}
                                </span>
                                <div class="text-xs text-gray-500">
                                    <span class="text-gray-500">มัดจำ: -{{ number_format($deposit, 2) }}</span>
                                    
                                    @if($balance > 0)
                                        <span class="text-red-500 ml-1">คงเหลือ: {{ number_format($balance, 2) }}</span>
                                    @else
                                        <span class="text-gray-500 ml-1">(ชำระครบ)</span>
                                    @endif
                                </div>
                                
                                <div class="mt-1">
                                    @if($job->payment_status == 'paid')
                                        <span class="inline-flex px-2 text-xs font-semibold leading-5 text-blue-800 bg-blue-100 rounded-full">
                                            ✅ จ่ายแล้ว
                                        </span>
                                    @elseif($job->payment_status == 'unpaid')
                                        <span class="inline-flex px-2 text-xs font-semibold leading-5 text-red-800 bg-red-100 rounded-full">
                                            ❌ ยังไม่จ่าย
                                        </span>
                                    @else
                                        <span class="inline-flex px-2 text-xs font-semibold leading-5 text-yellow-800 bg-yellow-100 rounded-full">
                                            {{ $job->payment_status }}
                                        </span>
                                    @endif
                                </div>
                            </div>
                        </td>

                        <td class="px-5 py-4 bg-white text-sm text-center">
                            @if($job->status == 'completed')
                                <span class="relative inline-block px-3 py-1 font-semibold text-gray-700 leading-tight">
                                    <span aria-hidden class="absolute inset-0 bg-gray-200 opacity-50 rounded-full"></span>
                                    <span class="relative">เสร็จสมบูรณ์</span>
                                </span>
                            @elseif($job->status == 'completed_pending_approval')
                                <span class="relative inline-block px-3 py-1 font-semibold text-orange-900 leading-tight">
                                    <span aria-hidden class="absolute inset-0 bg-orange-200 opacity-50 rounded-full"></span>
                                    <span class="relative">รอตรวจสอบ</span>
                                </span>
                            @else
                                <span class="text-gray-500">{{ $job->status }}</span>
                            @endif
                        </td>

                        <td class="px-5 py-4 bg-white text-sm text-center">
                            <a href="{{ route('staff.jobs.show', $job->id) }}" 
                               class="inline-flex items-center px-3 py-1.5 border border-indigo-500 text-indigo-500 rounded-md hover:bg-indigo-50 transition">
                                🔍 ดูรายละเอียด
                            </a>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5" class="px-5 py-10 text-center text-gray-500">
                            <p class="text-lg">ยังไม่มีประวัติงานที่ทำเสร็จแล้ว</p>
                            <p class="text-sm">สู้ๆ นะครับ! ✌️</p>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        
        @if($historyJobs->hasPages())
        <div class="px-5 py-5 bg-white border-t flex flex-col xs:flex-row items-center xs:justify-between">
            {{ $historyJobs->links() }} 
        </div>
        @endif
    </div>
</div>
@endsection