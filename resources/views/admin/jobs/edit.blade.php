@extends('layouts.admin')

@section('title', 'แก้ไขงาน #' . $job->job_number)
@section('header', 'แก้ไขรายละเอียดงาน')

@section('content')
<div class="max-w-5xl mx-auto">

    <div class="flex items-center justify-between mb-6">
        <a href="{{ route('admin.jobs.index') }}" class="text-gray-500 hover:text-gray-700 text-sm flex items-center gap-1 transition">
            <i class="fa-solid fa-arrow-left"></i> กลับหน้ารายการ
        </a>
    </div>

    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
        
        <div class="bg-orange-50 px-6 py-4 border-b border-orange-100 flex justify-between items-center">
            <h3 class="font-bold text-lg text-orange-800 flex items-center gap-2">
                <i class="fa-solid fa-pen-to-square"></i> แก้ไขงาน #{{ $job->job_number }}
            </h3>
            <span class="px-3 py-1 bg-white text-orange-600 rounded-full text-xs font-bold border border-orange-200 shadow-sm">
                {{ $job->status }}
            </span>
        </div>

        <div class="p-6 md:p-8">
            {{-- 🔥 Form Start พร้อมตัวแปร Alpine.js คำนวณไร่ --}}
            <form action="{{ route('admin.jobs.update', $job->id) }}" method="POST" enctype="multipart/form-data" 
                  x-data="{ 
                      rate: {{ $job->price_per_rai_at_booking ?? 0 }},
                      actualArea: {{ $job->actual_area ?? ($job->estimated_area ?? 0) }},
                      total: {{ $job->total_price ?? 0 }}, 
                      deposit: {{ $job->deposit_amount ?? 0 }},
                      paymentMethod: '{{ $job->payment_method ?? 'transfer' }}' 
                  }">
                @csrf
                @method('PUT')

                {{-- 🟢 SECTION 1: ข้อมูลทั่วไป (Read Only) --}}
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-8">
                    <div class="space-y-4">
                        <div>
                            <label class="block text-xs font-bold text-gray-400 uppercase tracking-wider mb-1">ลูกค้า</label>
                            <div class="bg-gray-50 text-gray-700 px-4 py-3 rounded-xl border border-gray-200 flex items-center gap-3">
                                <div class="w-10 h-10 rounded-full bg-white flex items-center justify-center border border-gray-100 text-gray-400 shadow-sm">
                                    <i class="fa-solid fa-user"></i>
                                </div>
                                <div>
                                    <p class="font-bold text-sm text-gray-800">{{ $job->customer->name }}</p>
                                    <p class="text-xs text-gray-500"><i class="fa-solid fa-phone mr-1"></i> {{ $job->customer->phone }}</p>
                                </div>
                            </div>
                        </div>
                        <div class="grid grid-cols-2 gap-3">
                            <div>
                                <label class="block text-xs font-bold text-gray-400 uppercase tracking-wider mb-1">เครื่องจักร</label>
                                <div class="bg-gray-50 text-gray-700 px-4 py-3 rounded-xl border border-gray-200 flex items-center gap-3">
                                    <div class="w-8 h-8 rounded-full bg-white flex items-center justify-center border border-gray-100 text-gray-400 shadow-sm shrink-0">
                                        <i class="fa-solid fa-tractor"></i>
                                    </div>
                                    <span class="font-medium text-sm truncate">{{ $job->equipment->name }}</span>
                                </div>
                            </div>
                            <div>
                                <label class="block text-xs font-bold text-gray-400 uppercase tracking-wider mb-1">พื้นที่ประเมิน (ไร่)</label>
                                <div class="bg-gray-50 text-gray-700 px-4 py-3 rounded-xl border border-gray-200 flex items-center gap-3">
                                    <div class="w-8 h-8 rounded-full bg-white flex items-center justify-center border border-gray-100 text-green-500 shadow-sm shrink-0">
                                        <i class="fa-solid fa-map"></i>
                                    </div>
                                    <span class="font-bold text-sm">{{ number_format($job->estimated_area ?? 0, 1) }}</span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="bg-blue-50/50 rounded-xl p-5 border border-blue-100 flex flex-col justify-center">
                        <h4 class="text-blue-800 font-bold text-sm mb-4 flex items-center gap-2 border-b border-blue-100 pb-2">
                            <i class="fa-regular fa-calendar-days"></i> วันเวลาที่จอง
                        </h4>
                        <div class="space-y-3">
                            <div class="flex justify-between text-sm">
                                <span class="text-gray-500">เริ่มงาน</span>
                                <span class="font-bold text-gray-700 bg-white px-2 py-1 rounded border border-gray-200">
                                    {{ \Carbon\Carbon::parse($job->scheduled_start)->format('d/m/Y H:i') }}
                                </span>
                            </div>
                            <div class="flex justify-between text-sm">
                                <span class="text-gray-500">สิ้นสุด</span>
                                <span class="font-bold text-gray-700 bg-white px-2 py-1 rounded border border-gray-200">
                                    {{ \Carbon\Carbon::parse($job->scheduled_end)->format('d/m/Y H:i') }}
                                </span>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="relative flex py-5 items-center">
                    <div class="flex-grow border-t border-gray-200"></div>
                    <span class="flex-shrink-0 mx-4 text-gray-400 text-xs uppercase font-bold tracking-wider">ส่วนแก้ไขข้อมูลและปิดงาน</span>
                    <div class="flex-grow border-t border-gray-200"></div>
                </div>

                {{-- 🟡 SECTION 2: พื้นที่, การเงิน และปิดงาน --}}
                <div class="bg-yellow-50/50 rounded-2xl p-6 border border-yellow-100 mb-8">
                    <h4 class="font-bold text-yellow-800 mb-4 flex items-center gap-2 text-lg">
                        <i class="fa-solid fa-file-invoice-dollar"></i> สรุปพื้นที่และยอดเงิน
                    </h4>

                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-6">
                        {{-- 1. พื้นที่ทำจริง --}}
                        <div>
                            <label class="block text-sm font-bold text-gray-700 mb-2">พื้นที่ทำจริง (ไร่) <span class="text-red-500">*</span></label>
                            <div class="relative">
                                <input type="number" name="actual_area" x-model="actualArea" @input="total = (actualArea * rate).toFixed(2)" step="0.1" min="0" required
                                       class="w-full pl-4 pr-10 py-3 rounded-xl border border-gray-300 focus:ring-2 focus:ring-green-500/50 shadow-sm font-bold text-green-700 bg-white">
                                <span class="absolute inset-y-0 right-0 pr-4 flex items-center pointer-events-none text-gray-400 font-medium">ไร่</span>
                            </div>
                            <p class="text-[11px] text-gray-500 mt-1">เรท: <span x-text="rate"></span> บ./ไร่</p>
                        </div>

                        {{-- 2. ราคารวม --}}
                        <div>
                            <label class="block text-sm font-bold text-gray-700 mb-2">ราคารวม (บาท)</label>
                            <div class="relative">
                                <span class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-gray-500 font-bold">฿</span>
                                <input type="number" name="total_price" x-model="total" step="0.01" min="0" required
                                       class="w-full pl-8 pr-3 py-3 rounded-xl border border-gray-300 focus:ring-2 focus:ring-yellow-400/50 shadow-sm font-bold text-gray-800">
                            </div>
                            <p class="text-[11px] text-gray-500 mt-1">สามารถพิมพ์แก้ยอดเงินเองได้</p>
                        </div>

                        {{-- 3. มัดจำ --}}
                        <div>
                            <label class="block text-sm font-bold text-gray-700 mb-2">หักมัดจำ</label>
                            <div class="relative">
                                <span class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-red-400 font-bold">฿</span>
                                <input type="number" name="deposit_amount" x-model="deposit" step="0.01" min="0" readonly
                                       class="w-full pl-8 pr-3 py-3 rounded-xl border border-red-100 bg-red-50 text-red-500 shadow-sm cursor-not-allowed font-medium">
                            </div>
                        </div>

                        {{-- 4. ยอดคงเหลือ --}}
                        <div>
                            <label class="block text-sm font-bold text-gray-700 mb-2">ยอดเก็บสุทธิ</label>
                            <div class="relative">
                                <div class="w-full px-4 py-3 rounded-xl border border-agri-primary/20 bg-green-50 text-agri-primary font-black text-lg flex justify-between items-center shadow-inner">
                                    <span>฿</span>
                                    <span x-text="Math.max(0, total - deposit).toLocaleString('th-TH', {minimumFractionDigits: 2})"></span>
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- ส่วนเลือกวิธีชำระเงิน --}}
                    <div class="bg-white p-5 rounded-xl border border-gray-200 shadow-sm">
                        <label class="block text-sm font-bold text-gray-700 mb-3">วิธีชำระเงินปิดยอด <span class="text-red-500">*</span></label>
                        
                        <div class="flex flex-wrap gap-4 mb-4">
                            <label class="cursor-pointer">
                                <input type="radio" name="payment_method" value="transfer" class="peer sr-only" x-model="paymentMethod">
                                <div class="px-4 py-2 rounded-lg border border-gray-300 peer-checked:bg-blue-50 peer-checked:border-blue-500 peer-checked:text-blue-700 flex items-center gap-2 transition hover:bg-gray-50">
                                    <i class="fa-solid fa-mobile-screen-button"></i> โอนเงิน (Transfer)
                                </div>
                            </label>
                            <label class="cursor-pointer">
                                <input type="radio" name="payment_method" value="cash" class="peer sr-only" x-model="paymentMethod">
                                <div class="px-4 py-2 rounded-lg border border-gray-300 peer-checked:bg-green-50 peer-checked:border-green-500 peer-checked:text-green-700 flex items-center gap-2 transition hover:bg-gray-50">
                                    <i class="fa-solid fa-money-bill-wave"></i> เงินสด (Cash)
                                </div>
                            </label>
                        </div>

                        {{-- ช่องแนบสลิป (แสดงเฉพาะเลือกโอน) --}}
                        <div x-show="paymentMethod === 'transfer'" x-transition class="space-y-3">
                            <label class="block text-sm font-medium text-gray-600">แนบหลักฐานการโอน (Slip)</label>
                            <input type="file" name="payment_proof" accept="image/*" 
                                   class="block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-sm file:font-semibold file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100 transition">
                            @if($job->payment_proof)
                                <p class="text-xs text-green-600 mt-1"><i class="fa-solid fa-check-circle"></i> มีหลักฐานเดิมแล้ว (แนบไฟล์ใหม่หากต้องการเปลี่ยน)</p>
                            @endif
                        </div>

                        {{-- ข้อความแจ้งเตือนเงินสด (แสดงเฉพาะเลือกเงินสด) --}}
                        <div x-show="paymentMethod === 'cash'" x-transition class="bg-green-50 text-green-700 px-4 py-3 rounded-lg text-sm border border-green-200 flex items-start gap-2">
                            <i class="fa-solid fa-circle-info mt-0.5"></i>
                            <div>
                                <p class="font-bold">รับชำระด้วยเงินสด</p>
                                <p class="text-xs">ระบบจะบันทึกการเงินว่าชำระครบถ้วนเมื่อกดบันทึก</p>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- 🟢 SECTION 3: สถานะและคนขับ --}}
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-8">
                    <div>
                        <label class="block text-sm font-bold text-gray-700 mb-2">อัปเดตสถานะงาน <span class="text-red-500">*</span></label>
                        <select name="status" class="w-full pl-4 pr-10 py-3 rounded-xl border border-gray-300 focus:ring-2 focus:ring-green-500/20 focus:border-green-500 bg-white shadow-sm cursor-pointer font-medium">
                            <option value="pending" {{ $job->status == 'pending' ? 'selected' : '' }}>⏳ รออนุมัติ</option>
                            <option value="scheduled" {{ $job->status == 'scheduled' ? 'selected' : '' }}>✅ อนุมัติ / รอเริ่มงาน</option>
                            <option value="in_progress" {{ $job->status == 'in_progress' ? 'selected' : '' }}>🚜 กำลังดำเนินการ</option>
                            <option value="completed_pending_approval" {{ $job->status == 'completed_pending_approval' ? 'selected' : '' }}>🧐 รอตรวจสอบเงิน</option>
                            <option value="completed" {{ $job->status == 'completed' ? 'selected' : '' }}>🎉 เสร็จสิ้น (ปิดงานสมบูรณ์)</option>
                            <option value="cancelled" {{ $job->status == 'cancelled' ? 'selected' : '' }}>❌ ยกเลิก</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-bold text-gray-700 mb-2">พนักงานขับรถ</label>
                        <select name="assigned_staff_id" class="w-full pl-4 pr-10 py-3 rounded-xl border border-gray-300 bg-white shadow-sm cursor-pointer">
                            <option value="">-- ยังไม่ระบุ --</option>
                            @foreach($staffs as $staff)
                                <option value="{{ $staff->id }}" {{ $job->assigned_staff_id == $staff->id ? 'selected' : '' }}>
                                    {{ $staff->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <div class="mb-8">
                    <label class="block text-sm font-bold text-gray-700 mb-2">หมายเหตุเพิ่มเติม</label>
                    <textarea name="note" rows="2" class="w-full px-4 py-3 rounded-xl border border-gray-300 focus:ring-2 focus:ring-green-500/20 shadow-sm" placeholder="ระบุหมายเหตุ...">{{ old('note', $job->note) }}</textarea>
                </div>

                {{-- Action Buttons --}}
                <div class="flex items-center justify-between pt-6 border-t border-gray-100 bg-gray-50 -mx-6 -mb-6 px-6 py-4 md:px-8 md:rounded-b-2xl mt-6">
                    <button type="button" onclick="if(confirm('⚠️ ยืนยันที่จะยกเลิกงานนี้?')) document.getElementById('cancelForm').submit();" 
                            class="text-red-500 hover:text-red-700 text-sm font-bold px-4 py-2 hover:bg-red-50 rounded-lg transition">
                        <i class="fa-solid fa-trash-can"></i> ยกเลิกงาน
                    </button>

                    <div class="flex gap-3">
                        <a href="{{ route('admin.jobs.index') }}" class="px-6 py-2.5 rounded-xl text-sm font-bold text-gray-500 hover:bg-gray-200 transition">
                            ย้อนกลับ
                        </a>
                        <button type="submit" class="bg-green-600 text-white px-8 py-2.5 rounded-xl shadow-lg shadow-green-600/30 hover:bg-green-700 hover:-translate-y-0.5 transition font-bold flex items-center gap-2">
                            <i class="fa-solid fa-save"></i> บันทึกข้อมูล
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>
    
    <form id="cancelForm" action="{{ route('admin.jobs.cancel', $job->id) }}" method="POST" class="hidden">@csrf</form>

</div>
@endsection