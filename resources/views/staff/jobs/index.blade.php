@extends('layouts.staff')

@section('title', 'งานของฉัน')
@section('header', 'รายการงานวันนี้')

@section('content')
    <div class="max-w-2xl mx-auto pb-24 relative">

        {{-- 🔥 [เพิ่มใหม่] ส่วนแสดงแจ้งเตือน Error/Success --}}
        @if(session('success'))
            <div class="mb-4 p-4 bg-green-100 border-l-4 border-green-500 text-green-700 rounded-r shadow-sm">
                <p class="font-bold"><i class="fa-solid fa-check-circle"></i> สำเร็จ!</p>
                <p>{{ session('success') }}</p>
            </div>
        @endif

        @if(session('error'))
            <div class="mb-4 p-4 bg-red-100 border-l-4 border-red-500 text-red-700 rounded-r shadow-sm">
                <p class="font-bold"><i class="fa-solid fa-circle-exclamation"></i> ผิดพลาด!</p>
                <p>{{ session('error') }}</p>
            </div>
        @endif

        @if ($errors->any())
            <div class="mb-4 p-4 bg-red-50 border border-red-200 rounded-lg">
                <p class="font-bold text-red-600 mb-1"><i class="fa-solid fa-triangle-exclamation"></i> กรุณาตรวจสอบข้อมูล:</p>
                <ul class="list-disc list-inside text-sm text-red-600">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif
        {{-- 🔥 จบส่วนแจ้งเตือน --}}

        {{-- 1. ส่วนหัวข้อและสรุปยอด --}}
        <div class="grid grid-cols-2 gap-3 mb-6">
            {{-- ... (โค้ดเดิมส่วน Card สถิติ) ... --}}
            <div class="bg-blue-500 rounded-2xl p-4 text-white shadow-lg shadow-blue-200 relative overflow-hidden group">
                <div class="absolute right-0 top-0 opacity-10 transform translate-x-2 -translate-y-2 group-hover:scale-110 transition">
                    <i class="fa-solid fa-person-digging text-6xl"></i>
                </div>
                <p class="text-blue-100 text-xs font-medium mb-1">กำลังดำเนินการ</p>
                <h3 class="text-3xl font-bold">{{ $myJobs->where('status', 'in_progress')->count() }} <span class="text-sm font-normal opacity-80">งาน</span></h3>
            </div>
            <div class="bg-white rounded-2xl p-4 border border-gray-100 shadow-sm relative overflow-hidden group">
                <div class="absolute right-0 top-0 opacity-5 transform translate-x-2 -translate-y-2">
                    <i class="fa-regular fa-clock text-6xl text-gray-800"></i>
                </div>
                <p class="text-gray-500 text-xs font-medium mb-1">รอดำเนินการ</p>
                <h3 class="text-3xl font-bold text-gray-800">{{ $myJobs->where('status', 'scheduled')->count() }} <span class="text-sm font-normal text-gray-400">งาน</span></h3>
            </div>
        </div>

        {{-- ... (โค้ดส่วนอื่นคงเดิม จนถึง Script) ... --}}
        {{-- ปุ่มลัด --}}
        <div class="flex gap-3 mb-6 overflow-x-auto pb-2 scrollbar-hide">
            <a href="{{ route('staff.fuel.create') }}" class="flex-shrink-0 flex items-center gap-2 bg-white border border-gray-200 px-4 py-2.5 rounded-full text-sm font-bold text-gray-700 shadow-sm active:scale-95 transition">
                <div class="w-6 h-6 rounded-full bg-orange-100 text-orange-600 flex items-center justify-center">
                    <i class="fa-solid fa-gas-pump text-xs"></i>
                </div>
                บันทึกเติมน้ำมัน
            </a>
        </div>

        {{-- 2. รายการงาน --}}
        <div class="space-y-4">
            @forelse($myJobs as $job)
                <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden relative group transition hover:shadow-md">
                    <div class="h-1.5 w-full {{ $job->status == 'in_progress' ? 'bg-blue-500 animate-pulse' : 'bg-yellow-400' }}"></div>
                    <div class="p-5">
                        <div class="flex justify-between items-start mb-3">
                            <div>
                                <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-lg text-xs font-bold mb-2 {{ $job->status == 'in_progress' ? 'bg-blue-50 text-blue-600' : 'bg-yellow-50 text-yellow-700' }}">
                                    @if ($job->status == 'in_progress')
                                        <span class="w-2 h-2 rounded-full bg-blue-500 animate-pulse"></span> กำลังทำงาน
                                    @else
                                        <i class="fa-regular fa-clock"></i> รอดำเนินการ
                                    @endif
                                </span>
                                <h3 class="text-lg font-bold text-gray-800">#{{ $job->job_number }}</h3>
                            </div>
                            <div class="text-right">
                                <p class="text-xs text-gray-400 mb-0.5">เวลานัดหมาย</p>
                                <p class="text-sm font-bold text-agri-primary bg-green-50 px-2 py-0.5 rounded-md">{{ $job->scheduled_start->format('H:i') }} น.</p>
                                <p class="text-[10px] text-gray-400 mt-0.5">{{ $job->scheduled_start->format('d M Y') }}</p>
                            </div>
                        </div>
                        <div class="space-y-2 mb-4">
                            <div class="flex items-start gap-3">
                                <div class="w-8 h-8 rounded-full bg-gray-50 flex items-center justify-center flex-shrink-0 text-gray-400"><i class="fa-solid fa-user"></i></div>
                                <div>
                                    <p class="text-sm font-bold text-gray-700">{{ $job->customer->name }}</p>
                                    <a href="tel:{{ $job->customer->phone }}" class="text-xs text-blue-500 hover:underline flex items-center gap-1"><i class="fa-solid fa-phone"></i> {{ $job->customer->phone }}</a>
                                </div>
                            </div>
                            <div class="flex items-center gap-3">
                                <div class="w-8 h-8 rounded-full bg-gray-50 flex items-center justify-center flex-shrink-0 text-gray-400"><i class="fa-solid fa-tractor"></i></div>
                                <p class="text-sm text-gray-600">{{ $job->equipment->name }} <span class="text-xs text-gray-400">({{ $job->equipment->equipment_code }})</span></p>
                            </div>
                        </div>
                        @if ($job->deposit_amount > 0)
                            <div class="bg-yellow-50 border border-yellow-100 rounded-lg p-2 mb-4 flex items-center gap-2">
                                <div class="bg-yellow-200 text-yellow-700 w-6 h-6 rounded-full flex items-center justify-center text-xs"><i class="fa-solid fa-coins"></i></div>
                                <span class="text-xs font-bold text-yellow-800">มัดจำแล้ว: {{ number_format($job->deposit_amount) }} บ.</span>
                            </div>
                        @endif
                        <div class="grid grid-cols-2 gap-3 pt-3 border-t border-gray-50">
                            <a href="{{ route('staff.jobs.show', $job->id) }}" class="flex items-center justify-center gap-2 py-2.5 rounded-xl text-sm font-bold text-gray-600 bg-gray-50 hover:bg-gray-100 transition active:scale-95"><i class="fa-solid fa-eye"></i> ดูรายละเอียด</a>
                            @if ($job->status == 'scheduled')
                                <form action="{{ route('staff.jobs.start', $job->id) }}" method="POST" class="block">
                                    @csrf
                                    <button type="submit" class="w-full flex items-center justify-center gap-2 py-2.5 rounded-xl text-sm font-bold text-white bg-green-500 hover:bg-green-600 shadow-md shadow-green-200 transition active:scale-95"><i class="fa-solid fa-play"></i> เริ่มงาน</button>
                                </form>
                            @elseif($job->status == 'in_progress')
                                <button type="button" onclick="openFinishModal('{{ $job->id }}', '{{ $job->job_number }}', {{ $job->total_price }}, {{ $job->deposit_amount }}, {{ isset($qrCodes[$job->id]) ? json_encode($qrCodes[$job->id]) : 'null' }})" class="w-full flex items-center justify-center gap-2 py-2.5 rounded-xl text-sm font-bold text-white bg-blue-500 hover:bg-blue-600 shadow-md shadow-blue-200 transition active:scale-95"><i class="fa-solid fa-check-circle"></i> จบงาน</button>
                            @endif
                        </div>
                    </div>
                </div>
            @empty
                <div class="text-center py-16 bg-white rounded-3xl border border-gray-100 border-dashed">
                    <div class="w-20 h-20 bg-green-50 rounded-full flex items-center justify-center mx-auto mb-4 animate-bounce"><i class="fa-solid fa-mug-hot text-green-400 text-3xl"></i></div>
                    <h3 class="text-lg font-bold text-gray-700">ไม่มีงานค้าง</h3>
                    <p class="text-sm text-gray-400">พักผ่อนได้เต็มที่ครับ! 💤</p>
                </div>
            @endforelse
        </div>

        @if ($historyJobs->count() > 0)
            <div class="mt-8">
                <h3 class="text-sm font-bold text-gray-500 uppercase mb-3 pl-2">ประวัติงานล่าสุด</h3>
                <div class="bg-white rounded-2xl shadow-sm border border-gray-100 divide-y divide-gray-100">
                    @foreach ($historyJobs as $hJob)
                        <div class="p-4 flex justify-between items-center hover:bg-gray-50 transition">
                            <div class="flex items-center gap-3">
                                <div class="w-10 h-10 rounded-full bg-gray-100 flex items-center justify-center text-gray-400"><i class="fa-solid fa-check"></i></div>
                                <div><p class="text-sm font-bold text-gray-800">#{{ $hJob->job_number }}</p><p class="text-xs text-gray-500">{{ $hJob->customer->name }}</p></div>
                            </div>
                            <div class="text-right"><span class="inline-block px-2 py-0.5 rounded-md bg-green-100 text-green-700 text-[10px] font-bold mb-1">เสร็จสิ้น</span><p class="text-[10px] text-gray-400">{{ \Carbon\Carbon::parse($hJob->actual_end)->format('d/m H:i') }}</p></div>
                        </div>
                    @endforeach
                </div>
            </div>
        @endif
    </div>

    {{-- MODAL: แจ้งซ่อม (คงเดิม) --}}
    <div id="generalReportModal" class="fixed inset-0 z-50 hidden" aria-labelledby="modal-title" role="dialog" aria-modal="true">
        <div class="fixed inset-0 bg-gray-900 bg-opacity-75 transition-opacity backdrop-blur-sm" onclick="document.getElementById('generalReportModal').classList.add('hidden')"></div>
        <div class="fixed inset-0 z-10 overflow-y-auto">
            <div class="flex min-h-full items-center justify-center p-4 text-center sm:p-0">
                <div class="relative transform overflow-hidden rounded-2xl bg-white text-left shadow-xl transition-all sm:my-8 sm:w-full sm:max-w-lg w-full">
                    <div class="bg-red-600 px-4 py-4 sm:px-6 flex justify-between items-center"><h3 class="text-lg font-bold leading-6 text-white flex items-center gap-2"><i class="fa-solid fa-screwdriver-wrench"></i> แจ้งซ่อม/แจ้งรถเสีย</h3><button type="button" class="text-red-100 hover:text-white" onclick="document.getElementById('generalReportModal').classList.add('hidden')"><i class="fa-solid fa-times text-xl"></i></button></div>
                    <form action="{{ route('staff.report_general') }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        <div class="px-4 py-5 sm:p-6 space-y-4">
                            <div><label class="block text-sm font-bold text-gray-700 mb-1">เลือกเครื่องจักร *</label><select name="equipment_id" required class="block w-full rounded-xl border-gray-300 shadow-sm focus:border-red-500 focus:ring-red-500 py-3"><option value="">-- แตะเพื่อเลือก --</option>@foreach ($equipments as $eq)<option value="{{ $eq->id }}">{{ $eq->name }} ({{ $eq->equipment_code }})</option>@endforeach</select></div>
                            <div><label class="block text-sm font-bold text-gray-700 mb-1">อาการเสีย *</label><textarea name="description" rows="3" required class="block w-full rounded-xl border-gray-300 shadow-sm focus:border-red-500 focus:ring-red-500 p-3" placeholder="ระบุอาการให้ชัดเจน..."></textarea></div>
                            <div><label class="block text-sm font-bold text-gray-700 mb-1">รูปถ่าย (ถ้ามี)</label><input type="file" name="image" accept="image/*" capture="environment" class="block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-sm file:font-semibold file:bg-red-50 file:text-red-700 hover:file:bg-red-100" /></div>
                        </div>
                        <div class="bg-gray-50 px-4 py-3 sm:flex sm:flex-row-reverse sm:px-6"><button type="submit" class="inline-flex w-full justify-center rounded-xl bg-red-600 px-3 py-3 text-sm font-bold text-white shadow-sm hover:bg-red-500 sm:ml-3 sm:w-auto">ยืนยันแจ้งซ่อม</button><button type="button" class="mt-3 inline-flex w-full justify-center rounded-xl bg-white px-3 py-3 text-sm font-semibold text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 hover:bg-gray-50 sm:mt-0 sm:w-auto" onclick="document.getElementById('generalReportModal').classList.add('hidden')">ยกเลิก</button></div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    {{-- MODAL: จบงาน (Smart Offline) --}}
    <div id="finishJobModal" class="fixed inset-0 z-50 hidden" aria-labelledby="modal-title" role="dialog" aria-modal="true" x-data="finishJobData()">
        <div class="fixed inset-0 bg-gray-900 bg-opacity-75 transition-opacity backdrop-blur-sm"></div>
        <div class="fixed inset-0 z-10 overflow-y-auto">
            <div class="flex min-h-full items-center justify-center p-4 text-center sm:p-0">
                <div class="relative transform overflow-hidden rounded-2xl bg-white text-left shadow-xl transition-all sm:my-8 sm:w-full sm:max-w-lg w-full">
                    <div class="bg-blue-600 px-4 py-4 flex justify-between items-center text-white"><h3 class="text-lg font-bold flex items-center gap-2"><i class="fa-solid fa-flag-checkered"></i> สรุปจบงาน <span x-text="'#' + jobNumber"></span></h3><button @click="closeModal()" class="text-blue-100 hover:text-white"><i class="fa-solid fa-times text-xl"></i></button></div>
                    <form :action="'/staff/jobs/' + jobId + '/finish'" method="POST" enctype="multipart/form-data" @submit="isSubmitting = true">
                        @csrf
                        <div class="p-6 space-y-5">
                            <div class="bg-gray-50 rounded-xl p-4 border border-gray-200">
                                <div class="flex justify-between text-sm mb-1"><span class="text-gray-500">ราคารวม:</span><span class="font-bold" x-text="formatMoney(totalPrice)"></span></div>
                                <div class="flex justify-between text-sm mb-1 text-red-500"><span>หัก มัดจำ:</span><span x-text="'-' + formatMoney(depositAmount)"></span></div>
                                <div class="flex justify-between text-lg font-bold text-blue-600 border-t border-gray-200 pt-2 mt-2"><span>ยอดต้องชำระ:</span><span x-text="formatMoney(balance)"></span></div>
                            </div>
                            <div x-show="balance > 0 && qrPayload" class="text-center">
                                <p class="text-sm font-bold text-gray-700 mb-2">สแกนจ่ายผ่าน QR Code</p>
                                <div class="bg-white p-2 inline-block border rounded-lg shadow-sm"><img :src="'https://api.qrserver.com/v1/create-qr-code/?size=200x200&data=' + qrPayload" class="w-40 h-40 mx-auto"></div>
                            </div>
                            <div x-show="balance > 0" class="bg-blue-50 border-l-4 border-blue-400 p-3 rounded-r-lg flex gap-3">
                                <div class="flex-shrink-0 text-blue-500 py-1"><i class="fa-solid fa-robot"></i></div>
                                <div class="text-sm text-blue-800"><strong>EasySlip AI:</strong> ระบบจะตรวจสอบยอดเงินและความถูกต้องของสลิปอัตโนมัติ กรุณารอสักครู่หลังจากกดส่ง</div>
                            </div>
                            <div class="space-y-3">
                                <div><label class="block text-sm font-bold text-gray-700 mb-1">📸 รูปหน้างาน (บังคับ) *</label><input type="file" name="job_image" required accept="image/*" capture="environment" class="block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-sm file:font-semibold file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100" /></div>
                                <div x-show="balance > 0"><label class="block text-sm font-bold text-gray-700 mb-1">💸 สลิปโอนเงิน (บังคับ) *</label><input type="file" name="payment_proof" :required="balance > 0" accept="image/*" class="block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-sm file:font-semibold file:bg-green-50 file:text-green-700 hover:file:bg-green-100" /></div>
                                <div><label class="block text-sm font-bold text-gray-700 mb-1">📝 หมายเหตุ</label><textarea name="note" rows="2" class="block w-full rounded-xl border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 p-3"></textarea></div>
                            </div>
                        </div>
                        <div class="bg-gray-50 px-4 py-3 sm:flex sm:flex-row-reverse sm:px-6"><button type="submit" class="inline-flex w-full justify-center rounded-xl bg-blue-600 px-3 py-3 text-sm font-bold text-white shadow-sm hover:bg-blue-500 sm:ml-3 sm:w-auto disabled:opacity-50 disabled:cursor-not-allowed" :disabled="isSubmitting"><span x-show="!isSubmitting">ยืนยันจบงาน</span><span x-show="isSubmitting"><i class="fa-solid fa-spinner fa-spin"></i> กำลังบันทึก...</span></button></div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <script>
        function finishJobData() {
            return {
                jobId: '', jobNumber: '', totalPrice: 0, depositAmount: 0, balance: 0, qrPayload: '', isSubmitting: false,
                formatMoney(amount) { return new Intl.NumberFormat('th-TH', { style: 'currency', currency: 'THB' }).format(amount); },
                closeModal() { document.getElementById('finishJobModal').classList.add('hidden'); this.isSubmitting = false; }
            }
        }
        function openFinishModal(id, number, total, deposit, qr) {
            const modal = document.getElementById('finishJobModal');
            const xData = Alpine.$data(modal);
            xData.jobId = id; xData.jobNumber = number; xData.totalPrice = total; xData.depositAmount = deposit; xData.balance = total - deposit; xData.qrPayload = qr; xData.isSubmitting = false;
            modal.classList.remove('hidden');
        }
    </script>
@endsection