@extends('layouts.customer')

@section('content')
<div class="max-w-7xl mx-auto pb-12">
    
    {{-- Header --}}
    <div class="mb-8">
        <a href="{{ route('customer.dashboard') }}" class="inline-flex items-center text-gray-500 hover:text-green-700 font-medium transition mb-3">
            <i class="fa-solid fa-arrow-left mr-2"></i> กลับหน้าหลัก
        </a>
        <h1 class="text-3xl font-bold text-gray-800 flex items-center gap-3">
            <span class="bg-green-100 text-green-700 p-2 rounded-xl text-2xl"><i class="fa-solid fa-calendar-plus"></i></span>
            จองคิวเครื่องจักร
        </h1>
        <p class="text-gray-500 mt-1 ml-12">เลือกเครื่องจักรและเวลาที่ต้องการ ระบบจะตรวจสอบคิวว่างให้อัตโนมัติ</p>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-12 gap-8 items-start">
        
        {{-- 📝 Left Column: ฟอร์มจอง (7/12) --}}
        <div class="lg:col-span-7 bg-white rounded-3xl shadow-sm border border-gray-100 p-6 md:p-8">
            <form action="{{ route('customer.booking.store') }}" method="POST">
                @csrf

                {{-- 1. เลือกเครื่องจักร --}}
                <div class="mb-8">
                    <label class="block text-lg font-bold text-gray-800 mb-4 flex items-center gap-2">
                        <span class="w-6 h-6 rounded-full bg-green-600 text-white flex items-center justify-center text-xs">1</span>
                        เลือกเครื่องจักร <span class="text-red-500">*</span>
                    </label>
                    
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        @foreach($equipments as $eq)
                        <label class="cursor-pointer group">
                            {{-- ใส่ onchange เพื่อเรียก JS เช็คคิว --}}
                            <input type="radio" name="equipment_id" value="{{ $eq->id }}" class="peer sr-only" required onchange="fetchSchedule()">
                            
                            <div class="relative border-2 border-gray-100 rounded-2xl p-4 hover:border-green-400 hover:bg-green-50/30 peer-checked:border-green-600 peer-checked:bg-green-50 peer-checked:shadow-md transition duration-200">
                                <div class="absolute top-3 right-3 text-green-600 opacity-0 peer-checked:opacity-100 transition-opacity transform scale-50 peer-checked:scale-100">
                                    <i class="fa-solid fa-circle-check text-xl"></i>
                                </div>

                                <div class="flex items-center gap-4">
                                    <div class="w-16 h-16 bg-gray-200 rounded-xl overflow-hidden shrink-0">
                                        @if($eq->image_path)
                                            <img src="{{ asset('storage/'.$eq->image_path) }}" class="w-full h-full object-cover">
                                        @else
                                            <div class="flex items-center justify-center h-full text-gray-400 bg-gray-100"><i class="fa-solid fa-tractor text-2xl"></i></div>
                                        @endif
                                    </div>
                                    <div>
                                        <h3 class="font-bold text-gray-800 group-hover:text-green-700 transition">{{ $eq->name }}</h3>
                                        <p class="text-sm text-gray-500 mb-1">{{ $eq->registration_number }}</p>
                                        <span class="inline-block bg-green-100 text-green-700 text-xs px-2 py-0.5 rounded-md font-bold">
                                            {{ number_format($eq->hourly_rate) }} บ./ชม.
                                        </span>
                                    </div>
                                </div>
                            </div>
                        </label>
                        @endforeach
                    </div>
                </div>

                {{-- 2. วันที่และเวลา --}}
                <div class="mb-8">
                    <label class="block text-lg font-bold text-gray-800 mb-4 flex items-center gap-2">
                        <span class="w-6 h-6 rounded-full bg-green-600 text-white flex items-center justify-center text-xs">2</span>
                        ระบุวันและเวลา
                    </label>
                    
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                        <div class="md:col-span-1">
                            <label class="block text-sm font-medium text-gray-700 mb-2">วันที่ต้องการจอง</label>
                            <div class="relative">
                                <span class="absolute left-3 top-3 text-gray-400"><i class="fa-regular fa-calendar"></i></span>
                                <input type="date" name="start_date" id="start_date"
                                       value="{{ old('start_date', $selectedDate) }}"
                                       min="{{ date('Y-m-d') }}"
                                       class="pl-10 w-full rounded-xl border-gray-300 focus:ring-green-500 focus:border-green-500 shadow-sm cursor-pointer" 
                                       required onchange="fetchSchedule()">
                            </div>
                        </div>
                        
                        {{-- ✅ เปลี่ยนเป็น Dropdown (Select) แบบไทยๆ --}}
                        <div class="md:col-span-2">
                            <label class="block text-sm font-medium text-gray-700 mb-2">ช่วงเวลาทำงาน</label>
                            <div class="flex items-center gap-3">
                                <div class="relative w-full">
                                    
                                    <select name="start_time" class="pl-10 w-full rounded-xl border-gray-300 focus:ring-green-500 focus:border-green-500 shadow-sm cursor-pointer appearance-none bg-white" required>
                                        @for($i = 6; $i <= 18; $i++)
                                            @php $time = sprintf('%02d:00', $i); @endphp
                                            <option value="{{ $time }}" {{ old('start_time', '08:00') == $time ? 'selected' : '' }}>{{ $time }} น.</option>
                                            
                                            @php $time30 = sprintf('%02d:30', $i); @endphp
                                            @if($i < 18) {{-- ไม่เอา 18:30 --}}
                                                <option value="{{ $time30 }}" {{ old('start_time') == $time30 ? 'selected' : '' }}>{{ $time30 }} น.</option>
                                            @endif
                                        @endfor
                                    </select>
                                    
                                </div>
                                <span class="text-gray-400 font-medium">ถึง</span>
                                <div class="relative w-full">
                                    
                                    <select name="end_time" class="pl-10 w-full rounded-xl border-gray-300 focus:ring-green-500 focus:border-green-500 shadow-sm cursor-pointer appearance-none bg-white" required>
                                        @for($i = 7; $i <= 19; $i++) {{-- เริ่ม 07:00 จบ 19:00 --}}
                                            @php $time = sprintf('%02d:00', $i); @endphp
                                            <option value="{{ $time }}" {{ old('end_time', '17:00') == $time ? 'selected' : '' }}>{{ $time }} น.</option>
                                            
                                            @php $time30 = sprintf('%02d:30', $i); @endphp
                                            @if($i < 19)
                                                <option value="{{ $time30 }}" {{ old('end_time') == $time30 ? 'selected' : '' }}>{{ $time30 }} น.</option>
                                            @endif
                                        @endfor
                                    </select>
                                    
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    @if($errors->has('time_slot'))
                        <div class="mt-4 bg-red-50 border border-red-200 text-red-700 p-4 rounded-xl flex items-start gap-3 animate-pulse">
                            <i class="fa-solid fa-circle-exclamation mt-1"></i>
                            <div>
                                <p class="font-bold">ขออภัย! ช่วงเวลานี้ไม่ว่าง</p>
                                <p class="text-sm">{{ $errors->first('time_slot') }}</p>
                            </div>
                        </div>
                    @endif
                </div>

                {{-- 3. หมายเหตุ --}}
                <div class="mb-8">
                    <label class="block text-lg font-bold text-gray-800 mb-4 flex items-center gap-2">
                        <span class="w-6 h-6 rounded-full bg-green-600 text-white flex items-center justify-center text-xs">3</span>
                        ข้อมูลเพิ่มเติม
                    </label>
                    <textarea name="note" rows="3" placeholder="ระบุสถานที่หน้างาน หรือรายละเอียดเพิ่มเติม..." 
                              class="w-full rounded-xl border-gray-300 focus:ring-green-500 focus:border-green-500 shadow-sm resize-none"></textarea>
                </div>

                {{-- ปุ่มกด --}}
                <div class="pt-4 border-t border-gray-100">
                    <button type="submit" class="w-full bg-gradient-to-r from-green-600 to-green-700 text-white py-4 rounded-xl font-bold text-xl hover:from-green-700 hover:to-green-800 shadow-lg hover:shadow-xl transition transform hover:-translate-y-0.5 flex items-center justify-center gap-2">
                        <i class="fa-solid fa-paper-plane"></i> ยืนยันการจอง
                    </button>
                </div>
            </form>
        </div>

        {{-- 📅 Right Column: ตารางเช็คคิว --}}
        <div class="lg:col-span-5 sticky top-6">
            <div class="bg-gray-900 text-white rounded-3xl shadow-2xl p-6 md:p-8 border border-gray-800 relative overflow-hidden">
                
                <div class="absolute top-0 right-0 -mr-16 -mt-16 w-64 h-64 bg-green-500 opacity-10 rounded-full blur-3xl"></div>
                <div class="absolute bottom-0 left-0 -ml-16 -mb-16 w-40 h-40 bg-yellow-400 opacity-10 rounded-full blur-3xl"></div>

                <div class="relative z-10">
                    <h4 class="font-bold text-xl mb-6 flex items-center gap-3">
                        <span class="bg-gray-800 p-2 rounded-lg border border-gray-700 text-green-400">
                            <i class="fa-regular fa-calendar-check"></i>
                        </span>
                        ตารางการใช้รถ
                    </h4>

                    <div class="bg-gray-800/50 rounded-2xl p-5 mb-6 text-center border border-gray-700 backdrop-blur-sm">
                        <p class="text-gray-400 text-xs uppercase tracking-wider mb-2">วันที่เลือกดู</p>
                        <h5 id="selected_date_display" class="text-2xl font-bold text-white">-- เลือกวัน --</h5>
                        <p id="machine_name_display" class="text-sm text-green-400 mt-1 h-5"></p>
                    </div>

                    <div id="loading_spinner" class="hidden text-center py-8">
                        <i class="fa-solid fa-circle-notch fa-spin text-3xl text-green-500 mb-2"></i>
                        <p class="text-gray-400 text-sm">กำลังตรวจสอบคิว...</p>
                    </div>

                    <div class="space-y-3 max-h-[400px] overflow-y-auto pr-2 custom-scrollbar" id="schedule_list">
                        <div class="text-center py-10 text-gray-600 border-2 border-dashed border-gray-800 rounded-xl">
                            <i class="fa-solid fa-arrow-pointer text-3xl mb-3 opacity-50"></i>
                            <p class="text-sm font-medium">กรุณาเลือก "เครื่องจักร" และ "วันที่"<br>เพื่อดูตารางเวลาว่าง</p>
                        </div>
                    </div>

                    <div class="mt-6 pt-4 border-t border-gray-800 flex items-center justify-between text-xs text-gray-400">
                        <div class="flex items-center gap-2">
                            <span class="w-2 h-2 rounded-full bg-red-500"></span> ไม่ว่าง
                        </div>
                        <div class="flex items-center gap-2">
                            <span class="w-2 h-2 rounded-full bg-green-500"></span> ว่าง (จองได้)
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="mt-4 bg-yellow-50 border border-yellow-200 rounded-2xl p-4 flex gap-3 text-yellow-800 text-sm">
                <i class="fa-solid fa-lightbulb mt-1 text-yellow-600"></i>
                <p>แนะนำให้จองล่วงหน้าอย่างน้อย 1-2 วัน เพื่อให้มั่นใจว่าจะได้คิวแน่นอนครับ</p>
            </div>
        </div>

    </div>
</div>
@endsection

@section('scripts')
<script>
    function normalizeDateInputValue(value) {
        const match = String(value || '').match(/^(\d{4})-(\d{2})-(\d{2})$/);
        if (!match) {
            return {
                normalizedValue: value,
                dateObj: null,
                dateOnly: ''
            };
        }

        let year = parseInt(match[1], 10);
        const month = parseInt(match[2], 10);
        const day = parseInt(match[3], 10);

        if (year >= 2400) {
            year -= 543;
        }

        const pad2 = (num) => String(num).padStart(2, '0');
        const dateOnly = `${year}-${pad2(month)}-${pad2(day)}`;

        return {
            normalizedValue: dateOnly,
            dateObj: new Date(year, month - 1, day),
            dateOnly
        };
    }

    // เรียก fetchSchedule ทันทีถ้ามีค่า old input
    document.addEventListener('DOMContentLoaded', function() {
        const bookingForm = document.querySelector('form[action="{{ route('customer.booking.store') }}"]');
        if (bookingForm) {
            bookingForm.addEventListener('submit', function() {
                const startDateInput = document.getElementById('start_date');
                if (!startDateInput || !startDateInput.value) return;

                const parsed = normalizeDateInputValue(startDateInput.value);
                if (parsed.normalizedValue) {
                    startDateInput.value = parsed.normalizedValue;
                }
            });
        }

        if(document.querySelector('input[name="equipment_id"]:checked') && document.getElementById('start_date').value) {
            fetchSchedule();
        }
    });

    function fetchSchedule() {
        const equipmentRadio = document.querySelector('input[name="equipment_id"]:checked');
        const dateInput = document.getElementById('start_date');
        
        const scheduleList = document.getElementById('schedule_list');
        const dateDisplay = document.getElementById('selected_date_display');
        const machineDisplay = document.getElementById('machine_name_display');
        const loadingSpinner = document.getElementById('loading_spinner');

        if (!equipmentRadio || !dateInput.value) {
            return;
        }

        const parsedDate = normalizeDateInputValue(dateInput.value);
        if (parsedDate.normalizedValue && parsedDate.normalizedValue !== dateInput.value) {
            dateInput.value = parsedDate.normalizedValue;
        }

        dateDisplay.innerText = parsedDate.dateObj
            ? parsedDate.dateObj.toLocaleDateString('th-TH', { day: 'numeric', month: 'long', year: 'numeric' })
            : '--';
        
        const machineName = equipmentRadio.closest('label').querySelector('h3').innerText;
        machineDisplay.innerText = machineName;

        loadingSpinner.classList.remove('hidden');
        scheduleList.innerHTML = '';

        fetch(`{{ route('customer.booking.check_schedule') }}?equipment_id=${equipmentRadio.value}&date=${parsedDate.dateOnly || dateInput.value}`)
            .then(response => response.json())
            .then(data => {
                loadingSpinner.classList.add('hidden');
                scheduleList.innerHTML = '';

                if (data.length === 0) {
                    scheduleList.innerHTML = `
                        <div class="bg-green-500/10 border border-green-500/20 rounded-xl p-4 text-center">
                            <i class="fa-regular fa-circle-check text-green-400 text-3xl mb-2"></i>
                            <h5 class="text-green-400 font-bold">วันนี้ว่างทั้งวัน</h5>
                            <p class="text-gray-400 text-xs">สามารถเลือกเวลาได้ตามสะดวกครับ</p>
                        </div>
                    `;
                } else {
                    data.forEach(event => {
                        const item = `
                            <div class="bg-gray-800 rounded-xl p-3 flex items-center justify-between border-l-4 border-red-500">
                                <div class="flex items-center gap-3">
                                    <div class="bg-red-500/20 p-2 rounded-lg text-red-500">
                                        <i class="fa-solid fa-clock"></i>
                                    </div>
                                    <div>
                                        <p class="text-white font-bold text-sm">${event.start} - ${event.end} น.</p>
                                        <p class="text-xs text-gray-400">ไม่ว่าง (${translateStatus(event.status)})</p>
                                    </div>
                                </div>
                                <span class="text-xs bg-red-500/20 text-red-400 px-2 py-1 rounded">ถูกจองแล้ว</span>
                            </div>
                        `;
                        scheduleList.innerHTML += item;
                    });
                }
            })
            .catch(error => {
                console.error('Error:', error);
                loadingSpinner.classList.add('hidden');
            });
    }

    function translateStatus(status) {
        switch(status) {
            case 'scheduled': return 'จองแล้ว';
            case 'in_progress': return 'กำลังทำงาน';
            case 'completed': return 'เสร็จสิ้น';
            default: return status;
        }
    }
</script>

<style>
    .custom-scrollbar::-webkit-scrollbar {
        width: 6px;
    }
    .custom-scrollbar::-webkit-scrollbar-track {
        background: #1f2937; 
        border-radius: 4px;
    }
    .custom-scrollbar::-webkit-scrollbar-thumb {
        background: #4b5563; 
        border-radius: 4px;
    }
    .custom-scrollbar::-webkit-scrollbar-thumb:hover {
        background: #6b7280; 
    }
</style>
@endsection