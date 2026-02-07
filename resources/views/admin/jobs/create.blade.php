@extends('layouts.admin')

@section('title', 'สร้างงานใหม่')
@section('header', 'เพิ่มรายการจอง')

@section('content')
    <div class="max-w-6xl mx-auto">

        <div class="grid grid-cols-1 lg:grid-cols-12 gap-6">

            {{-- 🟢 LEFT: Form --}}
            <div class="lg:col-span-7">
                {{-- 🔥 แก้ไข: เพิ่ม enctype="multipart/form-data" เพื่อให้ส่งไฟล์รูปได้ --}}
                <form action="{{ route('admin.jobs.store') }}" method="POST" enctype="multipart/form-data"
                    class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6 relative overflow-hidden">
                    @csrf
                    <div class="absolute top-0 left-0 w-full h-1 bg-agri-primary"></div>

                    <h3 class="font-bold text-gray-800 text-lg mb-6 flex items-center gap-2">
                        <span
                            class="w-8 h-8 rounded-full bg-agri-primary text-white flex items-center justify-center text-sm">1</span>
                        ข้อมูลการจ้างงาน
                    </h3>

                    {{-- ลูกค้า --}}
                    <div class="mb-5">
    <label class="block text-sm font-medium text-gray-700 mb-1.5">ลูกค้า (Customer) <span class="text-red-500">*</span></label>
    <div class="flex gap-2">
        <div class="relative w-full">
            {{-- 1. ใส่ ID ให้ select เพื่อไว้อ้างอิง --}}
            <select id="customer_select" name="customer_id" class="w-full" required placeholder="ค้นหา หรือ เลือกรายชื่อ...">
                <option value="">-- เลือกลูกค้า --</option>
                @foreach ($customers as $customer)
                    <option value="{{ $customer->id }}" {{ old('customer_id') == $customer->id ? 'selected' : '' }}>
                        {{ $customer->name }} ({{ $customer->phone }})
                    </option>
                @endforeach
            </select>
        </div>
        <a href="{{ route('admin.customers.create') }}" class="shrink-0 w-11 h-11 flex items-center justify-center bg-green-50 text-agri-primary rounded-xl border border-green-100 hover:bg-agri-primary hover:text-white transition" title="เพิ่มลูกค้าใหม่">
            <i class="fa-solid fa-plus"></i>
        </a>
    </div>
</div>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-5 mb-6">
                        {{-- เครื่องจักร --}}
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1.5">เครื่องจักร (Equipment) <span
                                    class="text-red-500">*</span></label>
                            <div class="relative">
                                <i class="fa-solid fa-tractor absolute left-3 top-1/2 -translate-y-1/2 text-gray-400"></i>
                                <select name="equipment_id" id="equipment_select"
                                    class="w-full pl-10 pr-4 py-2.5 rounded-xl border border-gray-200 focus:ring-2 focus:ring-agri-primary/20 focus:border-agri-primary outline-none bg-gray-50/50"
                                    required>
                                    <option value="">-- เลือกเครื่องจักร --</option>
                                    @foreach ($equipments as $eq)
                                        <option value="{{ $eq->id }}"
                                            {{ old('equipment_id') == $eq->id ? 'selected' : '' }}>
                                            {{ $eq->name }} ({{ $eq->registration_number ?? $eq->equipment_code }})
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                        </div>

                        {{-- พนักงาน --}}
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1.5">พนักงานขับ (Staff) <span
                                    class="text-red-500">*</span></label>
                            <div class="relative">
                                <i class="fa-solid fa-id-card absolute left-3 top-1/2 -translate-y-1/2 text-gray-400"></i>
                                <select name="assigned_staff_id"
                                    class="w-full pl-10 pr-4 py-2.5 rounded-xl border border-gray-200 focus:ring-2 focus:ring-agri-primary/20 focus:border-agri-primary outline-none bg-gray-50/50">
                                    <option value="">-- ยังไม่ระบุ --</option>
                                    @foreach ($staffs as $staff)
                                        <option value="{{ $staff->id }}"
                                            {{ old('assigned_staff_id') == $staff->id ? 'selected' : '' }}>
                                            {{ $staff->name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                    </div>

                    <hr class="border-gray-100 my-6">

                    <h3 class="font-bold text-gray-800 text-lg mb-6 flex items-center gap-2">
                        <span
                            class="w-8 h-8 rounded-full bg-agri-primary text-white flex items-center justify-center text-sm">2</span>
                        กำหนดการและราคา
                    </h3>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-5 mb-5">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1.5">เริ่มงานวันที่ <span
                                    class="text-red-500">*</span></label>
                            <input type="datetime-local" name="scheduled_start" id="scheduled_start"
                                class="w-full px-4 py-2.5 rounded-xl border border-gray-200 focus:ring-2 focus:ring-agri-primary/20 focus:border-agri-primary outline-none transition"
                                value="{{ old('scheduled_start') }}" required>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1.5">สิ้นสุดวันที่ (โดยประมาณ) <span
                                    class="text-red-500">*</span></label>
                            <input type="datetime-local" name="scheduled_end"
                                class="w-full px-4 py-2.5 rounded-xl border border-gray-200 focus:ring-2 focus:ring-agri-primary/20 focus:border-agri-primary outline-none transition"
                                value="{{ old('scheduled_end') }}" required>
                        </div>
                    </div>

                    {{-- 🔥 ส่วนการเงินและมัดจำ (Alpine.js) --}}
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-5 mb-8" x-data="{ method: 'transfer' }">
                        {{-- 1. ราคาประเมินรวม --}}
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1.5">ราคาประเมินรวม (Total) <span
                                    class="text-red-500">*</span></label>
                            <div class="relative">
                                <span class="absolute left-4 top-1/2 -translate-y-1/2 text-gray-500 font-bold">฿</span>
                                <input type="number" name="total_price"
                                    class="w-full pl-10 pr-4 py-2.5 rounded-xl border border-gray-200 focus:ring-2 focus:ring-agri-primary/20 focus:border-agri-primary outline-none font-bold text-gray-800 text-lg"
                                    placeholder="0.00" min="0" step="0.01" value="{{ old('total_price') }}"
                                    required>
                            </div>
                        </div>

                        {{-- 2. ยอดมัดจำ + อัปโหลดสลิป --}}
                        <div
                            class="bg-orange-50 p-4 rounded-xl border border-orange-100 relative transition-all duration-300">
                            <label class="block text-sm font-bold text-orange-800 mb-2">
                                <i class="fa-solid fa-hand-holding-dollar mr-1"></i> รับเงินมัดจำ (Deposit)
                            </label>

                            <div class="flex gap-2 mb-3">
                                <div class="relative flex-1">
                                    <span
                                        class="absolute left-3 top-1/2 -translate-y-1/2 text-orange-500 font-bold">฿</span>
                                    <input type="number" name="deposit_amount"
                                        class="w-full pl-8 pr-2 py-2 rounded-lg border border-orange-200 text-orange-800 font-bold focus:ring-2 focus:ring-orange-500/20 focus:border-orange-400 outline-none placeholder:text-orange-300 bg-white"
                                        placeholder="ยอดเงิน" min="0" step="0.01"
                                        value="{{ old('deposit_amount', 0) }}">
                                </div>
                                <div class="w-[45%]">
                                    <select name="payment_method" x-model="method"
                                        class="w-full px-2 py-2 rounded-lg border border-orange-200 text-orange-700 font-medium bg-white focus:outline-none cursor-pointer">
                                        <option value="transfer">📱 โอนเงิน</option>
                                        <option value="cash">💵 เงินสด</option>
                                    </select>
                                </div>
                            </div>

                            {{-- ช่องอัปโหลดสลิป (แสดงเฉพาะเลือกโอน) --}}
                            <div x-show="method === 'transfer'" x-transition
                                class="bg-white p-2 rounded-lg border border-orange-200 border-dashed">
                                <label
                                    class="flex items-center gap-2 cursor-pointer text-xs text-gray-500 hover:text-orange-600 transition">
                                    <i class="fa-solid fa-paperclip"></i>
                                    <span class="truncate" id="file-name-label">แนบสลิป/หลักฐานการโอน...</span>
                                    <input type="file" name="payment_proof" accept="image/*" class="hidden"
                                        onchange="document.getElementById('file-name-label').innerText = this.files[0].name">
                                </label>
                            </div>
                        </div>
                    </div>

                    <div class="flex items-center justify-end gap-3 pt-4 border-t border-gray-100">
                        <a href="{{ route('admin.jobs.index') }}"
                            class="px-5 py-2.5 rounded-xl text-sm font-medium text-gray-500 hover:bg-gray-50 transition">ยกเลิก</a>
                        <button type="submit"
                            class="bg-agri-primary text-white px-8 py-2.5 rounded-xl shadow-lg shadow-agri-primary/30 hover:bg-agri-hover hover:-translate-y-0.5 transition font-bold flex items-center gap-2">
                            <i class="fa-solid fa-save"></i> บันทึกงาน
                        </button>
                    </div>
                </form>
            </div>

            {{-- 🔵 RIGHT: Schedule Check --}}
            <div class="lg:col-span-5">
                <div
                    class="bg-gray-800 text-white rounded-2xl shadow-lg p-6 h-full border border-gray-700 relative overflow-hidden">
                    <div class="absolute top-0 right-0 -mr-10 -mt-10 w-40 h-40 bg-white opacity-5 rounded-full blur-3xl">
                    </div>

                    <h4 class="font-bold text-lg mb-4 flex items-center gap-2 relative z-10">
                        <i class="fa-regular fa-calendar-check text-green-400"></i> ตรวจสอบคิวงาน
                    </h4>

                    <div class="bg-gray-700/50 rounded-xl p-4 mb-4 text-center border border-gray-600">
                        <p class="text-gray-400 text-xs uppercase mb-1">วันที่เลือก</p>
                        <h5 id="selected_date_display" class="text-xl font-bold text-green-400">-- เลือกวันเริ่มงาน --
                        </h5>
                    </div>

                    <div class="space-y-3 max-h-[400px] overflow-y-auto pr-2 custom-scrollbar" id="schedule_list">
                        <div class="text-center py-8 text-gray-500">
                            <i class="fa-solid fa-list-ul text-2xl mb-2 opacity-50"></i>
                            <p class="text-sm">กรุณาเลือกวันและเครื่องจักร<br>เพื่อตรวจสอบคิวว่าง</p>
                        </div>
                    </div>

                    <div class="mt-6 pt-4 border-t border-gray-700 text-xs text-gray-400 flex gap-2">
                        <i class="fa-solid fa-circle-info mt-0.5"></i>
                        <span>ระบบจะแสดงงานอื่นที่จองเครื่องจักรคันเดียวกันในวันที่เลือก เพื่อป้องกันการจองเวลาชนกัน</span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Alpine.js for interactivity --}}
    <script src="//unpkg.com/alpinejs" defer></script>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const dateInput = document.getElementById('scheduled_start');
            const equipmentSelect = document.getElementById('equipment_select');

            function loadSchedule() {
                const dateVal = dateInput.value;
                const equipmentVal = equipmentSelect.value;
                const displayDate = document.getElementById('selected_date_display');
                const listContainer = document.getElementById('schedule_list');

                if (!dateVal) return;

                const dateObj = new Date(dateVal);
                const dateStr = dateVal.split('T')[0];

                displayDate.innerText = dateObj.toLocaleDateString('th-TH', {
                    day: 'numeric',
                    month: 'long',
                    year: 'numeric'
                });

                if (!equipmentVal) {
                    listContainer.innerHTML =
                        '<div class="text-center py-4 text-gray-400">กรุณาเลือกเครื่องจักร...</div>';
                    return;
                }

                listContainer.innerHTML =
                    '<div class="text-center py-4 text-gray-400"><i class="fa-solid fa-circle-notch fa-spin"></i> กำลังตรวจสอบ...</div>';

                let url = `{{ route('admin.jobs.get_bookings') }}?date=${dateStr}&equipment_id=${equipmentVal}`;

                fetch(url)
                    .then(response => response.json())
                    .then(data => {
                        listContainer.innerHTML = '';

                        if (data.length === 0) {
                            listContainer.innerHTML = `
                            <div class="bg-green-500/10 border border-green-500/20 rounded-xl p-4 text-center">
                                <i class="fa-solid fa-check-circle text-green-400 text-3xl mb-2"></i>
                                <p class="text-green-300 font-bold">ว่างตลอดทั้งวัน</p>
                                <p class="text-xs text-green-400/70">สามารถจองเวลาไหนก็ได้</p>
                            </div>`;
                            return;
                        }

                        data.forEach(job => {
                            let statusText = job.status === 'in_progress' ? 'กำลังทำ' : 'จองแล้ว';

                            const item = `
                            <div class="bg-gray-700 rounded-lg p-3 border-l-4 border-yellow-500 flex justify-between items-center">
                                <div>
                                    <p class="text-white font-bold text-sm">${job.time_start} - ${job.time_end}</p>
                                    <p class="text-xs text-gray-400 mt-0.5"><i class="fa-solid fa-hashtag"></i> ${job.job_number}</p>
                                </div>
                                <span class="px-2 py-1 rounded bg-white/10 text-xs text-white border border-white/20">${statusText}</span>
                            </div>
                        `;
                            listContainer.innerHTML += item;
                        });
                    })
                    .catch(error => {
                        console.error('Error:', error);
                        listContainer.innerHTML =
                            '<p class="text-red-400 text-center text-sm">เกิดข้อผิดพลาดในการโหลด</p>';
                    });
            }

            dateInput.addEventListener('change', loadSchedule);
            equipmentSelect.addEventListener('change', loadSchedule);
            if (dateInput.value) loadSchedule();
        });
    </script>

    <style>
        .custom-scrollbar::-webkit-scrollbar {
            width: 4px;
        }

        .custom-scrollbar::-webkit-scrollbar-thumb {
            background: #4b5563;
            border-radius: 2px;
        }

        .custom-scrollbar::-webkit-scrollbar-track {
            background: transparent;
        }

        /* ปรับแต่งให้กล่องมันโค้งมนเหมือนอันอื่น */
        .ts-control {
            border-radius: 0.75rem !important;
            /* rounded-xl */
            padding: 0.625rem 1rem !important;
            /* py-2.5 px-4 */
            border-color: #e5e7eb !important;
            /* border-gray-200 */
        }

        .ts-wrapper.focus .ts-control {
            border-color: #1B4D3E !important;
            /* agri-primary */
            box-shadow: 0 0 0 2px rgba(27, 77, 62, 0.2) !important;
        }
    </style>
@endsection
