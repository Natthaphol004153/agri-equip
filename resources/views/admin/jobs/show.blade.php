@extends('layouts.admin')

@section('title', 'รายละเอียดงาน #' . $job->job_number)
@section('header', 'รายละเอียดงาน (Job Details)')

@section('content')
<div class="max-w-6xl mx-auto">

    @php
        $showDateDiff = false;
        $isOverdue = false;
        $diffDays = 0;
        $dayMessage = '';
        $dayColor = '';
        $dayIcon = '';
        $showRuntimeSummary = false;
        $runtimeMessage = '';
        $runtimeColor = '';
        $runtimeIcon = '';
        if (in_array($job->status, ['pending', 'scheduled']) && $job->scheduled_start) {
            $showDateDiff = true;
            $nowDay = \Carbon\Carbon::now()->startOfDay();
            $schedDay = \Carbon\Carbon::parse($job->scheduled_start)->startOfDay();
            $diffDays = $nowDay->diffInDays($schedDay, false);
            
            if ($diffDays < 0) {
                $isOverdue = true;
                $dayMessage = 'เลยกำหนดมาแล้ว ' . abs((int)$diffDays) . ' วัน';
                $dayColor = 'red';
                $dayIcon = 'fa-triangle-exclamation';
            } elseif ($diffDays > 0) {
                $dayMessage = 'เหลืออีก ' . (int)$diffDays . ' วัน';
                $dayColor = 'blue';
                $dayIcon = 'fa-hourglass-half';
            } else {
                $dayMessage = 'ถึงกำหนดทำงานวันนี้';
                $dayColor = 'orange';
                $dayIcon = 'fa-calendar-day';
            }
        }

        if ($job->status === 'in_progress' && $job->actual_start) {
            $showRuntimeSummary = true;
            $elapsedMinutes = max(0, \Carbon\Carbon::parse($job->actual_start)->diffInMinutes(\Carbon\Carbon::now()));
            $elapsedHours = intdiv($elapsedMinutes, 60);
            $remainingMinutes = $elapsedMinutes % 60;

            if ($elapsedHours > 0) {
                $runtimeMessage = 'กำลังดำเนินงานมาแล้ว ' . $elapsedHours . ' ชม. ' . $remainingMinutes . ' นาที';
            } else {
                $runtimeMessage = 'กำลังดำเนินงานมาแล้ว ' . $remainingMinutes . ' นาที';
            }

            $runtimeColor = 'purple';
            $runtimeIcon = 'fa-stopwatch';
        }

        if (in_array($job->status, ['completed', 'completed_pending_approval']) && $job->actual_start && $job->actual_end) {
            $showRuntimeSummary = true;
            $actualMinutes = max(0, \Carbon\Carbon::parse($job->actual_start)->diffInMinutes(\Carbon\Carbon::parse($job->actual_end)));
            $actualHours = intdiv($actualMinutes, 60);
            $actualRemainMinutes = $actualMinutes % 60;
            $durationText = $actualHours > 0
                ? ($actualHours . ' ชม. ' . $actualRemainMinutes . ' นาที')
                : ($actualRemainMinutes . ' นาที');

            $runtimeMessage = 'จบงานใช้เวลา ' . $durationText;
            $runtimeColor = 'green';
            $runtimeIcon = 'fa-flag-checkered';

            if ($job->scheduled_start && $job->scheduled_end) {
                $scheduledMinutes = max(0, \Carbon\Carbon::parse($job->scheduled_start)->diffInMinutes(\Carbon\Carbon::parse($job->scheduled_end)));
                $varianceMinutes = $actualMinutes - $scheduledMinutes;

                if ($varianceMinutes > 0) {
                    $runtimeMessage .= ' (ช้ากว่ากำหนด ' . $varianceMinutes . ' นาที)';
                    $runtimeColor = 'red';
                    $runtimeIcon = 'fa-triangle-exclamation';
                } elseif ($varianceMinutes < 0) {
                    $runtimeMessage .= ' (เร็วกว่ากำหนด ' . abs($varianceMinutes) . ' นาที)';
                } else {
                    $runtimeMessage .= ' (ตรงตามเวลาที่นัด)';
                }
            }
        }
    @endphp

    {{-- Top Action Bar --}}
    <div class="flex flex-col md:flex-row justify-between items-center mb-6 gap-4">
        <a href="{{ route('admin.jobs.index') }}" class="text-gray-500 hover:text-gray-700 font-medium flex items-center gap-2 transition">
            <i class="fa-solid fa-arrow-left"></i> กลับหน้ารายการ
        </a>

        <div class="flex gap-2">
            @if ($isOverdue)
                <button type="button" onclick="if(confirm('⚠️ งานนี้ล่าช้าเลยกำหนดมาแล้ว ยืนยันที่จะยกเลิกงานนี้ใช่หรือไม่?')) document.getElementById('cancelForm').submit();"
                    class="bg-red-50 border border-red-200 text-red-600 px-4 py-2 rounded-xl hover:bg-red-100 hover:text-red-700 transition shadow-sm font-medium flex items-center gap-2">
                    <i class="fa-solid fa-ban"></i> ยกเลิกงาน
                </button>
            @endif

            @if (in_array($job->status, ['scheduled', 'in_progress', 'completed_pending_approval']))
                <a href="{{ route('admin.jobs.edit', $job->id) }}" class="bg-white border border-gray-200 text-gray-600 px-4 py-2 rounded-xl hover:bg-gray-50 hover:text-orange-500 transition shadow-sm font-medium">
                    <i class="fa-solid fa-pen"></i> แก้ไขงาน
                </a>
            @endif

            @if ($job->status == 'completed')
                <a href="{{ route('admin.jobs.receipt', $job->id) }}" target="_blank" class="bg-gray-800 text-white px-5 py-2 rounded-xl hover:bg-gray-900 transition shadow-lg shadow-gray-500/30 font-medium flex items-center gap-2">
                    <i class="fa-solid fa-print"></i> พิมพ์ใบเสร็จ
                </a>
            @endif
        </div>
    </div>

    {{-- Bottom Action Bar (Mobile Only) --}}
    <div class="fixed bottom-0 left-0 right-0 z-40 lg:hidden p-4 bg-white border-t border-gray-100 shadow-up">
        @if ($job->status == 'scheduled')
            <button type="button" onclick="startJobDetail({{ $job->id }})" class="w-full bg-agri-primary text-white font-bold py-3.5 rounded-xl shadow-lg active:scale-95 transition flex items-center justify-center gap-2">
                <i class="fa-solid fa-play"></i> เริ่มงาน (Check-in)
            </button>
        @elseif($job->status == 'in_progress')
            <div class="grid grid-cols-3 gap-3">
                <button @click="reportModal = true" class="col-span-1 bg-white border-2 border-red-100 text-red-500 font-bold py-3 rounded-xl hover:bg-red-50 active:scale-95">
                    <i class="fa-solid fa-triangle-exclamation mb-1"></i> <span class="text-xs">แจ้งปัญหา</span>
                </button>
                <button @click="finishModal = true" class="col-span-2 bg-green-600 text-white font-bold py-3 rounded-xl shadow-lg active:scale-95">
                    <i class="fa-solid fa-flag-checkered"></i> จบงาน
                </button>
            </div>
        @endif
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

        {{-- 👈 LEFT COLUMN: ข้อมูลหลัก --}}
        <div class="lg:col-span-2 space-y-6">

            {{-- 1. Status Banner --}}
            <div class="bg-white p-6 rounded-2xl shadow-sm border border-gray-100 relative overflow-hidden">
                <div class="flex justify-between items-start relative z-10">
                    <div>
                        <p class="text-gray-500 text-xs uppercase tracking-wide font-bold">หมายเลขงาน (Job No.)</p>
                        <h2 class="text-3xl font-bold text-agri-primary mt-1">#{{ $job->job_number }}</h2>
                        <p class="text-sm text-gray-400 mt-1">สร้างเมื่อ: {{ \Carbon\Carbon::parse($job->created_at)->format('d M Y H:i') }}</p>
                    </div>

                    @php
                        $statusConfig = match ($job->status) {
                            'pending' => ['color' => 'gray', 'label' => 'รออนุมัติ', 'icon' => 'fa-clock'],
                            'scheduled' => ['color' => 'blue', 'label' => 'นัดหมายแล้ว', 'icon' => 'fa-calendar-check'],
                            'in_progress' => ['color' => 'purple', 'label' => 'กำลังดำเนินการ', 'icon' => 'fa-spinner fa-spin'],
                            'completed_pending_approval' => ['color' => 'orange', 'label' => 'รอตรวจสอบเงิน', 'icon' => 'fa-clipboard-check'],
                            'completed' => ['color' => 'green', 'label' => 'เสร็จสมบูรณ์', 'icon' => 'fa-circle-check'],
                            'cancelled' => ['color' => 'red', 'label' => 'ยกเลิก', 'icon' => 'fa-ban'],
                            default => ['color' => 'gray', 'label' => $job->status, 'icon' => 'fa-circle'],
                        };
                        $c = $statusConfig['color'];
                    @endphp

                    <div class="text-right">
                        <span class="inline-flex items-center gap-2 px-4 py-2 rounded-full text-sm font-bold bg-{{ $c }}-100 text-{{ $c }}-700 border border-{{ $c }}-200">
                            <i class="fa-solid {{ $statusConfig['icon'] }}"></i> {{ $statusConfig['label'] }}
                        </span>

                        @if($showDateDiff)
                            <div class="mt-3 inline-flex items-center gap-2 px-3 py-1.5 rounded-lg border bg-{{ $dayColor }}-50 text-{{ $dayColor }}-600 border-{{ $dayColor }}-200 shadow-sm relative z-20">
                                <i class="fa-solid {{ $dayIcon }}"></i>
                                <span class="text-sm font-bold">{{ $dayMessage }}</span>
                            </div>
                        @endif

                        @if($showRuntimeSummary)
                            <div class="mt-3 inline-flex items-center gap-2 px-3 py-1.5 rounded-lg border bg-{{ $runtimeColor }}-50 text-{{ $runtimeColor }}-600 border-{{ $runtimeColor }}-200 shadow-sm relative z-20">
                                <i class="fa-solid {{ $runtimeIcon }}"></i>
                                <span class="text-sm font-bold">{{ $runtimeMessage }}</span>
                            </div>
                        @endif
                    </div>
                </div>
                {{-- Decoration --}}
                <div class="absolute -right-6 -bottom-6 text-gray-50 transform rotate-12">
                    <i class="fa-solid fa-file-invoice text-9xl"></i>
                </div>
            </div>

            {{-- 2. Customer & Machine --}}
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                {{-- ลูกค้า --}}
                <div class="bg-white p-5 rounded-2xl shadow-sm border border-gray-100">
                    <h3 class="font-bold text-gray-800 mb-4 flex items-center gap-2 border-b border-gray-100 pb-2">
                        <i class="fa-solid fa-user text-blue-500"></i> ข้อมูลลูกค้า
                    </h3>
                    <div class="space-y-3">
                        <div>
                            <p class="text-xs text-gray-400">ชื่อลูกค้า</p>
                            <p class="font-medium text-gray-800">{{ $job->customer->name }}</p>
                        </div>
                        <div>
                            <p class="text-xs text-gray-400">เบอร์โทรศัพท์</p>
                            <a href="tel:{{ $job->customer->phone }}" class="font-medium text-blue-600 hover:underline">{{ $job->customer->phone }}</a>
                        </div>
                        <div>
                            <p class="text-xs text-gray-400">ที่อยู่ลูกค้า</p>
                            <p class="font-medium text-gray-800 text-sm leading-relaxed">{{ $job->customer->address ?? '-' }}</p>
                        </div>
                        <div>
                            <p class="text-xs text-gray-400">สถานที่ปฏิบัติงาน</p>
                            <p class="font-medium text-gray-800 text-sm leading-relaxed">{{ $job->customer->work_location_address ?? $job->customer->address ?? '-' }}</p>
                        </div>
                        @php
                            $manualWorkMapUrl = $job->customer->work_map_url ?? null;
                            $workLat = $job->customer->work_latitude ?? null;
                            $workLng = $job->customer->work_longitude ?? null;
                            $workAddress = $job->customer->work_location_address ?? null;

                            $adminMapLink = !empty($manualWorkMapUrl)
                                ? $manualWorkMapUrl
                                : ((!empty($workLat) && !empty($workLng))
                                    ? "https://maps.google.com/maps?q={$workLat},{$workLng}"
                                    : (isset($job->customer->latitude)
                                        ? "https://maps.google.com/maps?q={$job->customer->latitude},{$job->customer->longitude}"
                                        : "https://maps.google.com/maps?q=" . urlencode($workAddress ?: ($job->customer->address ?? ''))));
                        @endphp
                        <div>
                            <a href="{{ $adminMapLink }}" target="_blank"
                                class="inline-flex items-center gap-2 text-sm font-medium text-blue-600 hover:underline">
                                <i class="fa-solid fa-map-location-dot"></i> เปิดแผนที่
                            </a>
                        </div>
                    </div>
                </div>

                {{-- เครื่องจักร & พนักงาน --}}
                <div class="bg-white p-5 rounded-2xl shadow-sm border border-gray-100">
                    <h3 class="font-bold text-gray-800 mb-4 flex items-center gap-2 border-b border-gray-100 pb-2">
                        <i class="fa-solid fa-tractor text-orange-500"></i> การปฏิบัติงาน
                    </h3>
                    <div class="space-y-3">
                        <div>
                            <p class="text-xs text-gray-400">เครื่องจักร</p>
                            <p class="font-medium text-gray-800">{{ $job->equipment->name }}</p>
                            <span class="text-xs bg-gray-100 text-gray-500 px-2 py-0.5 rounded">{{ $job->equipment->equipment_code }}</span>
                        </div>
                        <div>
                            <p class="text-xs text-gray-400">พนักงานขับรถ</p>
                            @if ($job->assignedStaff)
                                <div class="flex items-center gap-2 mt-1">
                                    <div class="w-8 h-8 rounded-full bg-orange-100 text-orange-600 flex items-center justify-center text-xs font-bold">
                                        {{ substr($job->assignedStaff->name, 0, 1) }}
                                    </div>
                                    <span class="font-medium text-gray-800">{{ $job->assignedStaff->name }}</span>
                                </div>
                            @else
                                <span class="text-red-500 text-sm">- ยังไม่ระบุ -</span>
                            @endif
                        </div>
                    </div>
                </div>
            </div>

            {{-- 3. Timeline --}}
            <div class="bg-white p-5 rounded-2xl shadow-sm border border-gray-100">
                <h3 class="font-bold text-gray-800 mb-4 flex items-center gap-2">
                    <i class="fa-solid fa-clock text-purple-500"></i> ไทม์ไลน์เวลา
                </h3>
                <div class="flex flex-col md:flex-row gap-6">
                    <div class="flex-1 bg-gray-50 p-4 rounded-xl border border-gray-200">
                        <p class="text-xs text-gray-500 mb-1 font-bold">เวลานัดหมาย (Scheduled)</p>
                        <div class="flex justify-between items-center text-sm">
                            <span>เริ่ม:</span>
                            <span class="font-medium">{{ \Carbon\Carbon::parse($job->scheduled_start)->format('d/m/Y H:i') }}</span>
                        </div>
                        <div class="flex justify-between items-center text-sm mt-1">
                            <span>สิ้นสุด:</span>
                            <span class="font-medium">{{ \Carbon\Carbon::parse($job->scheduled_end)->format('d/m/Y H:i') }}</span>
                        </div>
                    </div>

                    <div class="flex-1 bg-purple-50 p-4 rounded-xl border border-purple-100">
                        <p class="text-xs text-purple-600 mb-1 font-bold">เวลาปฏิบัติจริง (Actual)</p>
                        @if ($job->actual_start)
                            <div class="flex justify-between items-center text-sm">
                                <span>เริ่ม:</span>
                                <span class="font-bold text-purple-800">{{ \Carbon\Carbon::parse($job->actual_start)->format('d/m/Y H:i') }}</span>
                            </div>
                            <div class="flex justify-between items-center text-sm mt-1">
                                <span>สิ้นสุด:</span>
                                <span class="font-bold text-purple-800">
                                    {{ $job->actual_end ? \Carbon\Carbon::parse($job->actual_end)->format('d/m/Y H:i') : 'กำลังทำ...' }}
                                </span>
                            </div>
                        @else
                            <p class="text-sm text-purple-400 mt-2 text-center">- ยังไม่เริ่มงาน -</p>
                        @endif
                    </div>
                </div>
            </div>
        </div>

        {{-- 👉 RIGHT COLUMN: การเงิน & รูปภาพ --}}
        <div class="space-y-6">

            {{-- Financial & Area Summary --}}
            <div class="bg-white p-6 rounded-2xl shadow-lg shadow-agri-primary/5 border border-agri-primary/20 relative overflow-hidden">
                <div class="absolute top-0 left-0 w-full h-1.5 bg-gradient-to-r from-green-400 to-agri-primary"></div>

                <h3 class="font-bold text-gray-800 mb-4 flex items-center gap-2">
                    <i class="fa-solid fa-coins text-yellow-500"></i> สรุปพื้นที่และยอดเงิน
                </h3>

                <div class="space-y-3 text-sm">
                    {{-- ✅ ข้อมูลพื้นที่ --}}
                    <div class="flex justify-between text-gray-500">
                        <span>พื้นที่ประเมิน</span>
                        <span>{{ number_format($job->estimated_area ?? 0, 1) }} ไร่</span>
                    </div>
                    <div class="flex justify-between text-green-800 font-bold bg-green-50 px-3 py-2 rounded-lg border border-green-100">
                        <span>พื้นที่ทำจริง</span>
                        <span class="text-green-700">{{ number_format($job->actual_area ?? $job->estimated_area, 1) }} ไร่</span>
                    </div>
                    <div class="flex justify-between text-gray-500">
                        <span>เรทราคาเครื่องจักร</span>
                        <span>{{ number_format($job->price_per_rai_at_booking ?? 0, 2) }} ฿/ไร่</span>
                    </div>

                    <div class="border-t border-gray-100 my-3 pt-3">
                        <div class="flex justify-between text-gray-600">
                            <span>รวมเป็นเงิน</span>
                            <span class="font-medium">{{ number_format($job->total_price, 2) }} ฿</span>
                        </div>
                        <div class="flex justify-between text-red-500 mt-1">
                            <span>หักมัดจำ</span>
                            <span>-{{ number_format($job->deposit_amount, 2) }} ฿</span>
                        </div>
                    </div>

                    <div class="border-t border-gray-100 mt-2 pt-3">
                        <div class="flex justify-between items-end">
                            <span class="font-bold text-gray-800">ยอดชำระสุทธิ</span>
                            <span class="text-2xl font-bold text-agri-primary">{{ number_format($job->total_price - $job->deposit_amount, 2) }} ฿</span>
                        </div>
                    </div>
                    <div class="flex justify-between text-gray-600 pt-2">
                        <span>วิธีชำระเงิน</span>
                        @php
                            $paymentMethodLabel = match ($job->payment_method) {
                                'cash' => 'เงินสด',
                                'transfer' => 'โอนเงิน',
                                default => '-'
                            };
                        @endphp
                        <span class="font-medium bg-gray-100 px-2 py-0.5 rounded">{{ $paymentMethodLabel }}</span>
                    </div>
                </div>

                @if ($job->status == 'completed')
                    <div class="mt-5 bg-green-50 text-green-700 text-center py-2.5 rounded-xl text-sm font-bold border border-green-200">
                        <i class="fa-solid fa-check-circle"></i> ชำระเงินครบถ้วน
                    </div>
                @endif
            </div>

            {{-- Images Gallery & Proof --}}
            <div class="bg-white p-5 rounded-2xl shadow-sm border border-gray-100">
                <h3 class="font-bold text-gray-800 mb-4 text-sm">หลักฐานรูปภาพ</h3>

                <div class="space-y-4">
                    {{-- รูปหน้างาน --}}
                    <div>
                        <p class="text-xs text-gray-400 mb-2">📸 รูปหน้างาน</p>
                        @if($job->image_path)
                            <a href="{{ asset('storage/' . $job->image_path) }}" target="_blank" class="block rounded-lg overflow-hidden border border-gray-200 hover:opacity-90 transition group relative">
                                <img src="{{ asset('storage/' . $job->image_path) }}" class="w-full h-32 object-cover">
                                <div class="absolute inset-0 flex items-center justify-center bg-black/30 opacity-0 group-hover:opacity-100 transition">
                                    <i class="fa-solid fa-magnifying-glass text-white"></i>
                                </div>
                            </a>
                        @else
                            <div class="h-24 bg-gray-50 rounded-lg border-2 border-dashed border-gray-200 flex flex-col items-center justify-center text-gray-400 text-xs">
                                <i class="fa-solid fa-image-slash mb-1"></i> ไม่มีรูป
                            </div>
                        @endif
                    </div>

                    {{-- 🔥 หลักฐานการเงิน --}}
                    <div>
                        <div class="flex justify-between items-end mb-2">
                            <p class="text-xs text-gray-400">💸 หลักฐานการชำระเงิน</p>
                            @if($job->deposit_amount > 0 && $job->status != 'completed')
                                <span class="text-[10px] bg-orange-100 text-orange-600 px-1.5 py-0.5 rounded font-bold border border-orange-200">
                                    <i class="fa-solid fa-check"></i> มีมัดจำ
                                </span>
                            @endif
                        </div>

                        @if($job->payment_method === 'cash')
                            <div class="bg-blue-50 border border-blue-200 rounded-xl p-4 text-center">
                                <i class="fa-solid fa-hand-holding-dollar text-blue-500 text-3xl mb-2"></i>
                                <p class="text-blue-700 font-bold">ชำระเงินสด</p>
                                <p class="text-xs text-blue-600">ไม่ต้องมีสลิปโอน</p>
                            </div>
                        @elseif($job->payment_proof)
                            <a href="{{ asset('storage/' . $job->payment_proof) }}" target="_blank" class="block rounded-lg overflow-hidden border border-gray-200 hover:opacity-90 transition group relative">
                                <img src="{{ asset('storage/' . $job->payment_proof) }}" class="w-full h-32 object-cover">
                                <div class="absolute inset-0 flex items-center justify-center bg-black/30 opacity-0 group-hover:opacity-100 transition">
                                    <i class="fa-solid fa-magnifying-glass text-white"></i>
                                </div>
                                <div class="absolute bottom-0 left-0 right-0 bg-black/60 text-white text-[10px] py-1 px-2 text-center backdrop-blur-sm">
                                    @if($job->status == 'completed')
                                        สลิปปิดงาน (Final Payment)
                                    @else
                                        สลิปมัดจำ (Deposit)
                                    @endif
                                </div>
                            </a>
                        @else
                            <div class="h-24 bg-gray-50 rounded-lg border-2 border-dashed border-gray-200 flex flex-col items-center justify-center text-gray-400 text-xs">
                                <i class="fa-solid fa-file-invoice-dollar mb-1"></i> ไม่มีหลักฐานโอน
                            </div>
                        @endif
                    </div>
                </div>
            </div>

            {{-- Note --}}
            @if ($job->note)
                <div class="bg-yellow-50 p-4 rounded-2xl border border-yellow-100 text-sm text-yellow-800">
                    <p class="font-bold mb-1"><i class="fa-regular fa-comment-dots"></i> หมายเหตุ:</p>
                    {{ $job->note }}
                </div>
            @endif

        </div>
    </div>
    
    {{-- Form ยกเลิกงาน --}}
    @if ($isOverdue)
        <form id="cancelForm" action="{{ route('admin.jobs.cancel', $job->id) }}" method="POST" class="hidden">@csrf</form>
    @endif
</div>

<script>
    function startJobDetail(jobId) {
        Swal.fire({
            title: 'ยืนยันเริ่มงาน?',
            icon: 'question',
            showCancelButton: true,
            confirmButtonText: 'เริ่มเลย!',
            confirmButtonColor: '#1B4D3E'
        }).then((result) => {
            if (result.isConfirmed) {
                fetch(`/staff/jobs/${jobId}/start`, {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                        'Content-Type': 'application/json',
                        'Accept': 'application/json'
                    }
                })
                .then(r => r.json())
                .then(d => {
                    if (d.success) {
                        window.location.reload();
                    }
                });
            }
        });
    }
</script>
@endsection