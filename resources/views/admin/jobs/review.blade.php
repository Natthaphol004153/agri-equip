@extends('layouts.admin')

@section('title', 'ตรวจสอบงาน')
@section('header', 'ตรวจสอบความถูกต้อง')

@section('content')
<div class="max-w-5xl mx-auto">
    
    <div class="flex items-center justify-between mb-6">
        <a href="{{ route('admin.jobs.index') }}" class="text-gray-500 hover:text-gray-700 text-sm flex items-center gap-1 transition">
            <i class="fa-solid fa-arrow-left"></i> กลับหน้ารายการ
        </a>
        <div class="px-3 py-1 bg-orange-100 text-orange-700 rounded-full text-xs font-bold flex items-center gap-2">
            <span class="w-2 h-2 rounded-full bg-orange-500 animate-pulse"></span>
            สถานะ: รอตรวจสอบ (Pending Approval)
        </div>
    </div>

    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
        {{-- Header --}}
        <div class="bg-gray-50/50 px-6 py-4 border-b border-gray-100 flex justify-between items-center">
            <h3 class="font-bold text-lg text-gray-800">
                <i class="fa-solid fa-file-contract text-agri-primary mr-2"></i> งานเลขที่ #{{ $job->job_number }}
            </h3>
            <span class="text-sm text-gray-500">{{ \Carbon\Carbon::parse($job->created_at)->format('d M Y') }}</span>
        </div>

        <div class="p-6 grid grid-cols-1 md:grid-cols-2 gap-8">
            {{-- 👈 ฝั่งซ้าย: ข้อมูลสรุป --}}
            <div class="space-y-6">
                {{-- Customer & Staff --}}
                <div class="bg-blue-50/50 p-4 rounded-xl border border-blue-100">
                    <div class="flex justify-between mb-2">
                        <span class="text-xs text-gray-500 uppercase font-bold">ลูกค้า</span>
                        <span class="text-sm font-bold text-gray-800">{{ $job->customer->name }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-xs text-gray-500 uppercase font-bold">ผู้ปฏิบัติงาน</span>
                        <span class="text-sm font-bold text-gray-800">{{ $job->assignedStaff->name ?? '-' }}</span>
                    </div>
                </div>

                {{-- Time & Machine --}}
                <div>
                    <h4 class="font-bold text-gray-800 mb-3 border-l-4 border-agri-primary pl-3">รายละเอียดการทำงาน</h4>
                    <ul class="space-y-3 text-sm">
                        <li class="flex justify-between border-b border-gray-100 pb-2">
                            <span class="text-gray-500">เครื่องจักร</span>
                            <span class="font-medium">{{ $job->equipment->name }}</span>
                        </li>
                        <li class="flex justify-between border-b border-gray-100 pb-2">
                            <span class="text-gray-500">เวลาเริ่ม-จบจริง</span>
                            <span class="font-medium text-blue-600">
                                {{ \Carbon\Carbon::parse($job->actual_start)->format('H:i') }} - 
                                {{ \Carbon\Carbon::parse($job->actual_end)->format('H:i') }}
                            </span>
                        </li>
                        <li class="flex justify-between border-b border-gray-100 pb-2">
                            <span class="text-gray-500">รวมระยะเวลา</span>
                            <span class="font-bold bg-gray-100 px-2 rounded">{{ $durationText ?? '-' }}</span>
                        </li>
                    </ul>
                </div>

                {{-- Payment Info --}}
                <div>
                    <h4 class="font-bold text-gray-800 mb-3 border-l-4 border-green-500 pl-3">สรุปยอดเงิน</h4>
                    <div class="bg-gray-50 p-4 rounded-xl space-y-2">
                        <div class="flex justify-between text-sm">
                            <span class="text-gray-600">ยอดรวมทั้งหมด</span>
                            <span class="font-bold">{{ number_format($job->total_price, 2) }} ฿</span>
                        </div>
                        <div class="flex justify-between text-sm text-red-500">
                            <span>หักมัดจำ</span>
                            <span>-{{ number_format($job->deposit_amount, 2) }} ฿</span>
                        </div>
                        <div class="border-t border-gray-200 my-2 pt-2 flex justify-between text-lg font-bold text-agri-primary">
                            <span>ยอดที่ต้องโอน</span>
                            <span>{{ number_format($job->total_price - $job->deposit_amount, 2) }} ฿</span>
                        </div>
                        <div class="flex justify-between text-sm text-gray-600">
                            <span>วิธีชำระเงิน</span>
                            @php
                                $paymentMethodLabel = match ($job->payment_method) {
                                    'cash' => 'เงินสด',
                                    'transfer' => 'โอนเงิน',
                                    default => '-'
                                };
                            @endphp
                            <span class="font-bold">{{ $paymentMethodLabel }}</span>
                        </div>
                    </div>
                </div>

                @if($job->note)
                    <div class="bg-yellow-50 p-3 rounded-lg border border-yellow-100 text-sm text-yellow-800">
                        <strong>💬 หมายเหตุ:</strong> {{ $job->note }}
                    </div>
                @endif
            </div>

            {{-- 👉 ฝั่งขวา: หลักฐาน (รูปภาพ) --}}
            <div class="space-y-6">
                <div>
                    <h4 class="font-bold text-gray-800 mb-3 flex items-center gap-2">
                        <i class="fa-solid fa-camera text-gray-400"></i> รูปหน้างาน
                    </h4>
                    @if($job->image_path)
                        <div class="relative group rounded-xl overflow-hidden border border-gray-200 shadow-sm">
                            <img src="{{ asset('storage/' . $job->image_path) }}" class="w-full h-64 object-cover">
                            <div class="absolute inset-0 bg-black/50 opacity-0 group-hover:opacity-100 transition flex items-center justify-center">
                                <a href="{{ asset('storage/' . $job->image_path) }}" target="_blank" class="text-white border border-white px-4 py-2 rounded-lg hover:bg-white hover:text-black transition">
                                    <i class="fa-solid fa-magnifying-glass"></i> ดูรูปขยาย
                                </a>
                            </div>
                        </div>
                    @else
                        <div class="h-40 bg-gray-50 rounded-xl border-2 border-dashed border-gray-200 flex flex-col items-center justify-center text-gray-400">
                            <i class="fa-solid fa-image-slash text-3xl mb-2"></i>
                            <span class="text-sm">ไม่ได้แนบรูปหน้างาน</span>
                        </div>
                    @endif
                </div>

                <div>
                    <h4 class="font-bold text-gray-800 mb-3 flex items-center gap-2">
                        <i class="fa-solid fa-money-bill-transfer text-gray-400"></i> หลักฐานการชำระเงิน
                    </h4>
                    @if($job->payment_method === 'cash')
                        <div class="bg-blue-50 border border-blue-200 rounded-xl p-4 text-center">
                            <i class="fa-solid fa-hand-holding-dollar text-blue-500 text-3xl mb-2"></i>
                            <p class="text-blue-700 font-bold">ชำระเงินสด</p>
                            <p class="text-xs text-blue-600">ไม่ต้องมีสลิปโอน</p>
                        </div>
                    @elseif($job->payment_proof)
                        <div class="relative group rounded-xl overflow-hidden border border-gray-200 shadow-sm">
                            <img src="{{ asset('storage/' . $job->payment_proof) }}" class="w-full h-64 object-cover">
                            <div class="absolute inset-0 bg-black/50 opacity-0 group-hover:opacity-100 transition flex items-center justify-center">
                                <a href="{{ asset('storage/' . $job->payment_proof) }}" target="_blank" class="bg-green-600 text-white px-4 py-2 rounded-lg shadow-lg hover:bg-green-700 transition">
                                    <i class="fa-solid fa-check"></i> ตรวจสอบสลิป
                                </a>
                            </div>
                        </div>
                    @elseif(($job->total_price - $job->deposit_amount) <= 0)
                        <div class="bg-green-50 border border-green-200 rounded-xl p-4 text-center">
                            <i class="fa-solid fa-circle-check text-green-500 text-3xl mb-2"></i>
                            <p class="text-green-700 font-bold">ชำระครบถ้วนแล้ว</p>
                            <p class="text-xs text-green-600">(หักจากมัดจำพอดี)</p>
                        </div>
                    @else
                        <div class="bg-red-50 border border-red-200 rounded-xl p-4 text-center">
                            <i class="fa-solid fa-circle-xmark text-red-400 text-3xl mb-2"></i>
                            <p class="text-red-700 font-bold">ยังไม่แนบสลิป</p>
                            <p class="text-xs text-red-500">กรุณาทวงถามลูกค้าหรือพนักงาน</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        {{-- Footer Actions --}}
        <div class="bg-gray-50 px-6 py-4 border-t border-gray-100 flex justify-end gap-3">
            <form action="{{ route('admin.jobs.approve', $job->id) }}" method="POST" onsubmit="return confirm('ยืนยันอนุมัติงานนี้? สถานะจะเปลี่ยนเป็นเสร็จสมบูรณ์');">
                @csrf
                <button type="submit" class="bg-green-600 text-white px-6 py-3 rounded-xl shadow-lg shadow-green-600/30 hover:bg-green-700 hover:-translate-y-0.5 transition font-bold text-lg flex items-center gap-2">
                    <i class="fa-solid fa-circle-check"></i> อนุมัติงาน (Approve)
                </button>
            </form>
        </div>
    </div>
</div>
@endsection