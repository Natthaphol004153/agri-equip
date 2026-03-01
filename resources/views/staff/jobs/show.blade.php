@extends('layouts.staff')

@section('title', 'รายละเอียดงาน')
@section('header', 'Job #' . $job->job_number)

@section('content')
    <div class="max-w-2xl mx-auto space-y-4 pb-24" x-data="{ reportModal: false, finishModal: false, isSubmitting: false }">

        {{-- 1. Status Card --}}
        <div class="bg-white rounded-2xl p-5 shadow-sm border border-gray-100 relative overflow-hidden">
            <div class="absolute top-0 right-0 p-4 opacity-10">
                <i class="fa-solid fa-clipboard-list text-6xl text-gray-400"></i>
            </div>
            <div class="flex items-center justify-between mb-2">
                <h3 class="text-lg font-bold text-gray-800">สถานะงานปัจจุบัน</h3>
                @if ($job->status == 'scheduled')
                    <span class="bg-gray-100 text-gray-600 px-3 py-1 rounded-lg text-xs font-bold border border-gray-200"><i
                            class="fa-regular fa-clock"></i> รอเริ่มงาน</span>
                @elseif($job->status == 'in_progress')
                    <span
                        class="bg-blue-100 text-blue-600 px-3 py-1 rounded-lg text-xs font-bold border border-blue-200 animate-pulse"><i
                            class="fa-solid fa-spinner fa-spin"></i> กำลังดำเนินการ</span>
                @elseif(in_array($job->status, ['completed', 'completed_pending_approval']))
                    <span
                        class="bg-green-100 text-green-700 px-3 py-1 rounded-lg text-xs font-bold border border-green-200"><i
                            class="fa-solid fa-check-circle"></i> เสร็จสิ้น</span>
                @elseif($job->status == 'cancelled')
                    <span class="bg-red-50 text-red-600 px-3 py-1 rounded-lg text-xs font-bold border border-red-100"><i
                            class="fa-solid fa-ban"></i> ยกเลิก</span>
                @endif
            </div>
            <p class="text-sm text-gray-500 flex items-center gap-2"><i
                    class="fa-regular fa-calendar text-agri-primary"></i> นัดหมาย: <span
                    class="font-medium text-gray-700">{{ \Carbon\Carbon::parse($job->scheduled_start)->format('d/m/Y H:i') }}
                    น.</span></p>
        </div>

        {{-- 2. Customer Info --}}
        <div class="bg-white rounded-2xl p-5 shadow-sm border border-gray-100">
            <h4 class="text-sm font-bold text-gray-400 uppercase tracking-wider mb-4 flex items-center gap-2"><i
                    class="fa-solid fa-user"></i> ข้อมูลลูกค้า</h4>
            <div class="flex items-start gap-4 mb-4">
                <div
                    class="w-12 h-12 rounded-full bg-agri-bg flex items-center justify-center text-agri-primary text-xl flex-shrink-0">
                    <i class="fa-solid fa-user-tag"></i>
                </div>
                <div>
                    <h5 class="font-bold text-gray-800 text-lg">{{ $job->customer->name }}</h5>
                    <p class="text-sm text-gray-500 mt-1 leading-relaxed"><i
                            class="fa-solid fa-location-dot text-red-500 mr-1"></i>
                        {{ $job->customer->address ?? 'ไม่ระบุที่อยู่' }}</p>
                </div>
            </div>
            <div class="grid grid-cols-2 gap-3">
                <a href="tel:{{ $job->customer->phone }}"
                    class="flex items-center justify-center gap-2 py-2.5 rounded-xl border border-green-200 bg-green-50 text-green-700 font-bold text-sm hover:bg-green-100 transition active:scale-95"><i
                        class="fa-solid fa-phone"></i> โทรหาลูกค้า</a>
                @php
                    $mapLink = isset($job->customer->latitude)
                        ? "https://maps.google.com/maps?q={$job->customer->latitude},{$job->customer->longitude}"
                        : 'https://maps.google.com/maps?q=' . urlencode($job->customer->address ?? '');
                @endphp
                <a href="{{ $mapLink }}" target="_blank"
                    class="flex items-center justify-center gap-2 py-2.5 rounded-xl border border-blue-200 bg-blue-50 text-blue-700 font-bold text-sm hover:bg-blue-100 transition active:scale-95"><i
                        class="fa-solid fa-map-location-dot"></i> นำทาง</a>
            </div>
        </div>

        {{-- 3. Machine Info --}}
        <div class="bg-white rounded-2xl p-5 shadow-sm border border-gray-100">
            <h4 class="text-sm font-bold text-gray-400 uppercase tracking-wider mb-4 flex items-center gap-2"><i
                    class="fa-solid fa-tractor"></i> ข้อมูลหน้างาน</h4>
            <div class="flex items-center gap-4 bg-gray-50 p-3 rounded-xl border border-gray-100 mb-3">
                <div class="w-14 h-14 rounded-lg bg-white border border-gray-200 flex items-center justify-center"><i
                        class="fa-solid fa-wrench text-2xl text-gray-400"></i></div>
                <div>
                    <h6 class="font-bold text-gray-800">{{ $job->equipment->name }}</h6>
                    <div class="flex gap-2 mt-1">
                        <span
                            class="text-xs bg-white border border-gray-200 px-2 py-0.5 rounded text-gray-500 font-mono">{{ $job->equipment->equipment_code }}</span>
                    </div>
                </div>
            </div>

            {{-- ✅ แสดงพื้นที่ประเมินให้พนักงานเห็น --}}
            <div class="bg-green-50 p-3 rounded-xl border border-green-100 flex justify-between items-center">
                <span class="text-sm font-bold text-green-800"><i class="fa-solid fa-map"></i> พื้นที่ประเมิน:</span>
                <span class="text-sm font-black text-green-700">{{ number_format($job->estimated_area ?? 0, 1) }} ไร่</span>
            </div>
        </div>

        {{-- Bottom Action Bar --}}
        <div
            class="fixed bottom-0 left-0 right-0 z-40 lg:hidden p-4 bg-white border-t border-gray-100 shadow-[0_-4px_20px_rgba(0,0,0,0.05)]">
            @if ($job->status == 'scheduled')
                <form action="{{ route('staff.jobs.start', $job->id) }}" method="POST" class="w-full">
                    @csrf
                    <button type="submit"
                        class="w-full bg-agri-primary text-white font-bold py-3.5 rounded-xl shadow-lg shadow-agri-primary/30 active:scale-95 transition flex items-center justify-center gap-2"
                        onclick="return confirm('ยืนยันการเริ่มงาน?');"><i class="fa-solid fa-play"></i> เริ่มงาน
                        (Check-in)</button>
                </form>
            @elseif($job->status == 'in_progress')
                @if ($job->equipment->current_status == 'breakdown' || $job->equipment->current_status == 'maintenance')
                    <div
                        class="w-full bg-red-50 text-red-600 font-bold py-3 rounded-xl border border-red-100 text-center flex flex-col items-center justify-center">
                        <div class="flex items-center gap-2"><i class="fa-solid fa-triangle-exclamation"></i> แจ้งซ่อมแล้ว
                        </div>
                        <span class="text-xs font-normal opacity-80">กรุณารอตรวจสอบ</span>
                    </div>
                @else
                    <div class="grid grid-cols-3 gap-3">
                        <button @click="reportModal = true"
                            class="col-span-1 bg-white border-2 border-red-100 text-red-500 font-bold py-3 rounded-xl hover:bg-red-50 active:scale-95 transition flex flex-col items-center justify-center leading-tight">
                            <i class="fa-solid fa-triangle-exclamation mb-1"></i> <span class="text-xs">แจ้งปัญหา</span>
                        </button>
                        <button @click="finishModal = true"
                            class="col-span-2 bg-green-600 text-white font-bold py-3 rounded-xl shadow-lg shadow-green-200 active:scale-95 transition flex items-center justify-center gap-2">
                            <i class="fa-solid fa-flag-checkered"></i> จบงาน (Finish)
                        </button>
                    </div>
                @endif
            @endif
        </div>

        <div class="hidden lg:block"></div>

        {{-- MODAL: แจ้งปัญหา --}}
        <div x-show="reportModal" class="relative z-50" style="display: none;">
            <div class="fixed inset-0 bg-gray-900/75 transition-opacity backdrop-blur-sm"></div>
            <div class="fixed inset-0 z-10 w-screen overflow-y-auto">
                <div class="flex min-h-full items-end justify-center p-4 text-center sm:items-center sm:p-0">
                    <div class="relative transform overflow-hidden rounded-2xl bg-white text-left shadow-xl transition-all sm:my-8 sm:w-full sm:max-w-lg w-full"
                        @click.away="reportModal = false">
                        <div class="bg-red-600 px-4 py-4 flex justify-between items-center">
                            <h3 class="text-lg font-bold text-white flex items-center gap-2"><i
                                    class="fa-solid fa-triangle-exclamation"></i> แจ้งเหตุขัดข้อง</h3><button
                                @click="reportModal = false" class="text-red-100 hover:text-white"><i
                                    class="fa-solid fa-times text-xl"></i></button>
                        </div>
                        <form action="{{ route('staff.jobs.report_issue', $job->id) }}" method="POST"
                            enctype="multipart/form-data" class="p-6">
                            @csrf
                            <div class="bg-yellow-50 border-l-4 border-yellow-400 p-4 rounded-r-lg mb-4">
                                <div class="flex">
                                    <div class="flex-shrink-0"><i
                                            class="fa-solid fa-circle-exclamation text-yellow-600"></i></div>
                                    <div class="ml-3">
                                        <p class="text-sm text-yellow-700">เมื่อแจ้งแล้ว สถานะรถจะเปลี่ยนเป็น <strong>"เสีย
                                                (Breakdown)"</strong> ทันที</p>
                                    </div>
                                </div>
                            </div>
                            <div class="space-y-4">
                                <div><label class="block text-sm font-bold text-gray-700 mb-1">สาเหตุ/อาการ *</label>
                                    <textarea name="description" rows="3" required
                                        class="w-full rounded-xl border-gray-300 shadow-sm focus:border-red-500 focus:ring-red-500 p-3"
                                        placeholder="ระบุอาการ..."></textarea>
                                </div>
                                <div><label class="block text-sm font-bold text-gray-700 mb-1">รูปถ่าย (ถ้ามี)</label><input
                                        type="file" name="image" accept="image/*" capture="environment"
                                        class="block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-sm file:font-semibold file:bg-red-50 file:text-red-700 hover:file:bg-red-100">
                                </div>
                            </div>
                            <div class="mt-6 flex gap-3"><button type="button" @click="reportModal = false"
                                    class="w-1/3 py-2.5 rounded-xl border border-gray-300 text-gray-700 font-bold hover:bg-gray-50">ยกเลิก</button><button
                                    type="submit"
                                    class="w-2/3 bg-red-600 text-white font-bold py-2.5 rounded-xl hover:bg-red-700 shadow-lg shadow-red-200">ยืนยันแจ้งเหตุ</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>

        {{-- 🔥 MODAL: จบงาน (Smart Payment & Area Logic) --}}
        <div x-show="finishModal" class="relative z-50" style="display: none;">
            <div class="fixed inset-0 bg-gray-900/75 transition-opacity backdrop-blur-sm"></div>
            <div class="fixed inset-0 z-10 w-screen overflow-y-auto">
                <div class="flex min-h-full items-end justify-center p-4 text-center sm:items-center sm:p-0">

                    @php
                        $isAlreadyPaid =
                            in_array($job->payment_status, ['paid', 'pending_approval']) ||
                            ($job->payment_status == 'deposit_paid' && $balance <= 0);
                        $pricePerRai = $job->price_per_rai_at_booking ?? 0;
                    @endphp

                    <div class="relative transform overflow-hidden rounded-2xl bg-white text-left shadow-xl transition-all sm:my-8 sm:w-full sm:max-w-lg w-full"
                        @click.away="finishModal = false" x-data="{
                            paymentMethod: 'transfer',
                            isPaid: {{ $isAlreadyPaid ? 'true' : 'false' }},
                            rate: {{ $pricePerRai }},
                            actualArea: {{ $job->estimated_area ?? 0 }},
                            deposit: {{ $job->deposit_amount ?? 0 }},
                            get total() { return (this.actualArea * this.rate).toFixed(2); },
                            get balanceAmount() { return Math.max(0, this.total - this.deposit).toFixed(2); }
                        }">

                        <div class="bg-green-600 px-4 py-4 flex justify-between items-center text-white">
                            <h3 class="text-lg font-bold flex items-center gap-2"><i
                                    class="fa-solid fa-flag-checkered"></i> สรุปจบงาน</h3>
                            <button @click="finishModal = false" class="text-green-100 hover:text-white"><i
                                    class="fa-solid fa-times text-xl"></i></button>
                        </div>

                        <form action="{{ route('staff.jobs.finish', $job->id) }}" method="POST"
                            enctype="multipart/form-data" class="p-6" @submit="isSubmitting = true">
                            @csrf

                            {{-- ✅ 1. ให้พนักงานกรอกพื้นที่ทำจริง --}}
                            {{-- 🌾 ส่วนแสดงพื้นที่ (ล็อคค่าตามใบจอง) --}}
                            <div class="mb-5 border-b border-gray-100 pb-5">
                                <label class="block text-sm font-bold text-gray-700 mb-2">จำนวนพื้นที่
                                    (ตกลงไว้ตามใบจอง)</label>
                                <div class="relative">
                                    <input type="number" name="actual_area" x-model.number="actualArea" readonly
                                        class="w-full pl-4 pr-12 py-3 rounded-xl border border-gray-200 bg-gray-100 text-lg font-bold text-gray-500 cursor-not-allowed focus:ring-0">
                                    <span
                                        class="absolute inset-y-0 right-0 pr-4 flex items-center pointer-events-none text-gray-400 font-bold">ไร่</span>
                                </div>
                                <p class="text-xs text-red-500/80 mt-1.5 font-medium"><i class="fa-solid fa-lock"></i>
                                    ค่าพื้นที่และยอดเงินถูกล็อคตามที่แอดมินระบุไว้</p>
                            </div>

                            {{-- 2. ยอดเงินคงเหลือ (คำนวณสด) --}}
                            <div class="bg-gray-50 rounded-xl p-4 border border-gray-200 mb-5">
                                <div class="flex justify-between text-sm mb-1 text-gray-500">
                                    <span>ยอดรวม (<span x-text="actualArea"></span> x <span
                                            x-text="rate"></span>):</span>
                                    <span x-text="total + ' ฿'"></span>
                                </div>
                                <div class="flex justify-between text-sm mb-1 text-red-500">
                                    <span>หักมัดจำ:</span>
                                    <span>-<span x-text="deposit"></span> ฿</span>
                                </div>
                                <div
                                    class="flex justify-between text-lg font-bold text-green-700 border-t border-gray-200 pt-2 mt-2">
                                    <span>ต้องชำระเพิ่ม:</span> <span x-text="balanceAmount + ' ฿'"></span>
                                </div>
                            </div>

                            {{-- 3. กรณีจ่ายแล้ว --}}
                            <template x-if="isPaid || balanceAmount <= 0">
                                <div class="bg-green-50 border-l-4 border-green-500 p-4 rounded-r-lg mb-5 flex gap-3">
                                    <div class="text-green-500 text-xl"><i class="fa-solid fa-circle-check"></i></div>
                                    <div>
                                        <h4 class="font-bold text-green-800 text-sm">ไม่ต้องเก็บเงินเพิ่ม</h4>
                                        <p class="text-xs text-green-600 mt-1">ยอดเงินเคลียร์แล้ว สามารถบันทึกจบงานได้เลย
                                        </p>
                                    </div>
                                </div>
                            </template>

                            {{-- 4. กรณีต้องเก็บเงินหน้างาน --}}
                            <template x-if="!isPaid && balanceAmount > 0">
                                <div class="space-y-5 mb-5">
                                    <div>
                                        <label
                                            class="block text-sm font-bold text-gray-700 mb-2">วิธีชำระเงินที่ลูกค้าเลือก</label>
                                        <div class="grid grid-cols-2 gap-3">
                                            <label class="cursor-pointer">
                                                <input type="radio" name="payment_method" value="transfer"
                                                    class="peer sr-only" x-model="paymentMethod">
                                                <div
                                                    class="border-2 border-gray-200 rounded-xl p-3 text-center peer-checked:border-green-500 peer-checked:bg-green-50 transition hover:bg-gray-50">
                                                    <i
                                                        class="fa-solid fa-qrcode text-xl mb-1 text-gray-500 peer-checked:text-green-600"></i>
                                                    <div
                                                        class="text-sm font-bold text-gray-600 peer-checked:text-green-700">
                                                        โอนจ่าย</div>
                                                </div>
                                            </label>
                                            <label class="cursor-pointer">
                                                <input type="radio" name="payment_method" value="cash"
                                                    class="peer sr-only" x-model="paymentMethod">
                                                <div
                                                    class="border-2 border-gray-200 rounded-xl p-3 text-center peer-checked:border-blue-500 peer-checked:bg-blue-50 transition hover:bg-gray-50">
                                                    <i
                                                        class="fa-solid fa-money-bill-wave text-xl mb-1 text-gray-500 peer-checked:text-blue-600"></i>
                                                    <div
                                                        class="text-sm font-bold text-gray-600 peer-checked:text-blue-700">
                                                        เงินสด</div>
                                                </div>
                                            </label>
                                        </div>
                                    </div>

                                    {{-- โอนจ่าย --}}
                                    <div x-show="paymentMethod === 'transfer'" class="animate-fade-in-up">
                                        @if ($qrData)
                                            <div class="text-center mb-4">
                                                <img src="https://chart.googleapis.com/chart?chs=200x200&cht=qr&chl={{ $qrData }}&choe=UTF-8"
                                                    class="mx-auto border p-1 rounded-lg w-32 h-32">
                                                <p class="text-xs text-gray-500 mt-1">ให้ลูกค้าสแกนเพื่อโอนเงิน</p>
                                            </div>
                                        @endif
                                        <div>
                                            <label class="block text-sm font-bold text-gray-700 mb-1">💸 แนบสลิปโอนเงิน
                                                *</label>
                                            <input type="file" name="payment_proof" accept="image/*"
                                                class="block w-full text-sm text-gray-500 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-sm file:font-semibold file:bg-green-50 file:text-green-700 hover:file:bg-green-100 cursor-pointer">
                                        </div>
                                    </div>

                                    {{-- เงินสด --}}
                                    <div x-show="paymentMethod === 'cash'"
                                        class="bg-blue-50 p-4 rounded-xl border border-blue-100 text-center animate-fade-in-up">
                                        <div class="text-blue-600 text-2xl mb-2"><i
                                                class="fa-solid fa-hand-holding-dollar"></i></div>
                                        <p class="text-sm font-bold text-blue-800">กรุณาเก็บเงินสดจากลูกค้า</p>
                                        <p class="text-xl font-bold text-blue-900 mt-1"><span
                                                x-text="balanceAmount"></span> บาท</p>
                                    </div>
                                </div>
                            </template>

                            {{-- 5. รูปผลงาน (บังคับ) --}}
                            <div class="space-y-3 pt-4 border-t border-gray-100">
                                <div>
                                    <label class="block text-sm font-bold text-gray-700 mb-1">📸 รูปผลงานจบงาน (บังคับ)
                                        *</label>
                                    <input type="file" name="job_image" required accept="image/*"
                                        capture="environment"
                                        class="block w-full text-sm text-gray-500 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-sm file:font-semibold file:bg-gray-100 file:text-gray-700 hover:file:bg-gray-200 cursor-pointer">
                                </div>
                                <div>
                                    <textarea name="note" rows="2"
                                        class="w-full rounded-xl border-gray-300 p-3 shadow-sm focus:border-green-500 focus:ring-green-500"
                                        placeholder="หมายเหตุเพิ่มเติม (ถ้ามี)..."></textarea>
                                </div>
                            </div>

                            <div class="mt-6">
                                <button type="submit"
                                    class="w-full bg-green-600 text-white font-bold py-3.5 rounded-xl hover:bg-green-700 shadow-lg shadow-green-200 disabled:opacity-50 disabled:cursor-not-allowed transition-all active:scale-95"
                                    :disabled="isSubmitting">
                                    <span x-show="!isSubmitting">ยืนยันจบงาน</span>
                                    <span x-show="isSubmitting" class="flex items-center justify-center gap-2"><i
                                            class="fa-solid fa-spinner fa-spin"></i> กำลังบันทึก...</span>
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>

    </div>
@endsection
