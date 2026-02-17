@extends('layouts.public')

@section('content')

<div class="relative w-full bg-gray-900">
    
    <swiper-container 
        class="mySwiper w-full h-[400px] md:h-[600px]" 
        pagination="true" 
        pagination-clickable="true" 
        navigation="true" 
        autoplay-delay="5000" 
        loop="true" 
        effect="fade">

        {{-- ✅ 1. เช็คว่ามีรูปจาก Admin หรือไม่ --}}
        @if(!empty($banners) && count($banners) > 0)
            @foreach($banners as $banner)
                <swiper-slide class="relative w-full h-full">
                    {{-- รูปภาพจาก Database --}}
                    <img src="{{ asset('storage/' . $banner) }}" 
                         class="w-full h-full object-cover" 
                         alt="Banner Slide">
                    {{-- Overlay สีดำจางๆ เพื่อให้อ่านตัวหนังสือออก --}}
                    <div class="absolute inset-0 bg-black/40"></div>
                </swiper-slide>
            @endforeach
        @else
            {{-- ⚠️ 2. Fallback: ถ้าไม่มีรูป ให้ใช้รูป Default --}}
            <swiper-slide class="relative w-full h-full">
                <img src="https://images.unsplash.com/photo-1605039068225-639879d5fb50?q=80&w=1974&auto=format&fit=crop" 
                     class="w-full h-full object-cover" alt="รถไถนา">
                <div class="absolute inset-0 bg-black/40"></div>
            </swiper-slide>

            <swiper-slide class="relative w-full h-full">
                <img src="https://images.unsplash.com/photo-1625246333195-5848c42807f4?q=80&w=1932&auto=format&fit=crop" 
                     class="w-full h-full object-cover" alt="รถเกี่ยวข้าว">
                <div class="absolute inset-0 bg-black/40"></div>
            </swiper-slide>

            <swiper-slide class="relative w-full h-full">
                <img src="https://images.unsplash.com/photo-1500382017468-9049fed747ef?q=80&w=2832&auto=format&fit=crop" 
                     class="w-full h-full object-cover" alt="ทุ่งนา">
                <div class="absolute inset-0 bg-black/40"></div>
            </swiper-slide>
        @endif

    </swiper-container>

    <div class="absolute inset-0 z-10 flex flex-col items-center justify-center text-center text-white px-4 pointer-events-none">
        <div class="pointer-events-auto max-w-3xl animate-fade-in-up">
            <h1 class="text-3xl md:text-5xl lg:text-6xl font-bold mb-4 leading-tight drop-shadow-lg">
                บริการเครื่องจักรเกษตร<br>
                <span class="text-yellow-400">ทันสมัย เพื่อชุมชน</span>
            </h1>
            <p class="text-lg md:text-2xl mb-8 opacity-95 drop-shadow-md font-light">
                จองง่าย ได้มาตรฐาน ราคาเป็นธรรม พร้อมบริการถึงที่นาของคุณ
            </p>
            
            <div class="flex flex-col sm:flex-row gap-4 justify-center">
                @auth('customer')
                    <a href="{{ route('customer.dashboard') }}" 
                       class="group bg-yellow-400 text-green-900 px-8 py-4 rounded-full font-bold text-lg shadow-xl hover:bg-yellow-300 transition transform hover:-translate-y-1 hover:scale-105 flex items-center justify-center">
                        <i class="fa-solid fa-calendar-plus mr-2 group-hover:rotate-12 transition"></i> 
                        จองคิวงานทันที
                    </a>
                @else
                    <button onclick="promptLogin()" 
                            class="group bg-white text-green-800 px-8 py-4 rounded-full font-bold text-lg shadow-xl hover:bg-gray-100 transition transform hover:-translate-y-1 hover:scale-105 flex items-center justify-center">
                        <i class="fa-solid fa-calendar-check mr-2 group-hover:rotate-12 transition"></i> 
                        จองคิวงานตอนนี้
                    </button>
                @endauth
            </div>
        </div>
    </div>
</div>

<div class="max-w-6xl mx-auto px-4 py-12 space-y-16">

    <div class="bg-white rounded-3xl shadow-lg border border-gray-100 overflow-hidden">
        <div class="bg-green-50 p-6 flex flex-col md:flex-row justify-between items-center gap-4 border-b border-green-100">
            <div>
                <h2 class="font-bold text-2xl text-green-800 flex items-center gap-3">
                    <span class="bg-green-200 text-green-700 p-2 rounded-lg"><i class="fa-regular fa-calendar-days"></i></span> 
                    ตารางคิวงาน
                </h2>
                <p class="text-gray-500 text-sm mt-1 ml-12">ตรวจสอบวันว่างก่อนทำการจอง</p>
            </div>
            <div class="flex items-center gap-3 bg-white px-4 py-2 rounded-full shadow-sm">
                <span class="flex items-center text-sm font-medium text-red-500">
                    <span class="w-3 h-3 bg-red-500 rounded-full mr-2"></span> ไม่ว่าง (จองแล้ว)
                </span>
                <span class="w-px h-4 bg-gray-300"></span>
                <span class="flex items-center text-sm font-medium text-green-600">
                    <span class="w-3 h-3 bg-white border-2 border-green-500 rounded-full mr-2"></span> ว่าง
                </span>
            </div>
        </div>
        
        <div class="p-4 md:p-8">
            <div id="calendar" class="min-h-[500px] font-sarabun"></div>
        </div>
    </div>

    <div>
        <div class="text-center mb-10">
            <h2 class="font-bold text-3xl text-gray-800 mb-2">🚜 เครื่องจักรของเรา</h2>
            <p class="text-gray-500">เครื่องจักรคุณภาพ พร้อมคนขับมืออาชีพ</p>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
            @foreach($equipments as $eq)
            <div class="group bg-white rounded-2xl shadow-md border border-gray-100 overflow-hidden hover:shadow-xl transition duration-300 flex flex-col h-full">
                
                <div class="relative h-56 overflow-hidden bg-gray-200">
                    @if($eq->image_path)
                        <img src="{{ asset('storage/'.$eq->image_path) }}" 
                             class="w-full h-full object-cover group-hover:scale-110 transition duration-700"
                             alt="{{ $eq->name }}">
                    @else
                        <div class="w-full h-full flex flex-col items-center justify-center text-gray-400 bg-gray-100">
                            <i class="fa-solid fa-tractor text-5xl mb-2 opacity-50"></i>
                            <span class="text-sm">ไม่มีรูปภาพ</span>
                        </div>
                    @endif
                    
                    <div class="absolute top-3 right-3">
                        <span class="bg-green-500/90 backdrop-blur text-white text-xs font-bold px-3 py-1 rounded-full shadow-sm">
                            <i class="fa-solid fa-check-circle mr-1"></i> พร้อมบริการ
                        </span>
                    </div>
                </div>

                <div class="p-6 flex flex-col flex-grow">
                    <h3 class="font-bold text-xl text-gray-800 mb-1 group-hover:text-green-700 transition">
                        {{ $eq->name }}
                    </h3>
                    <p class="text-sm text-gray-500 flex items-center gap-2 mb-4">
                        <i class="fa-solid fa-barcode text-gray-400"></i> {{ $eq->registration_number }}
                    </p>
                    
                    <div class="mt-auto pt-4 border-t border-gray-100 flex justify-between items-center">
                        <div class="text-green-700">
                            <span class="text-xs text-gray-500">ราคาเริ่มต้น</span><br>
                            <span class="font-bold text-xl">{{ number_format($eq->hourly_rate) }}</span> <span class="text-sm">บาท/ชม.</span>
                        </div>
                        
                        @auth('customer')
                            <a href="{{ route('customer.dashboard') }}" 
                               class="bg-gray-50 hover:bg-green-600 hover:text-white text-green-700 w-10 h-10 rounded-full flex items-center justify-center transition shadow-sm">
                                <i class="fa-solid fa-arrow-right"></i>
                            </a>
                        @else
                            <button onclick="promptLogin()" 
                                    class="bg-gray-50 hover:bg-green-600 hover:text-white text-green-700 w-10 h-10 rounded-full flex items-center justify-center transition shadow-sm">
                                <i class="fa-solid fa-arrow-right"></i>
                            </button>
                        @endauth
                    </div>
                </div>
            </div>
            @endforeach
        </div>
    </div>
</div>

@endsection

@section('scripts')
<script src="https://cdn.jsdelivr.net/npm/swiper@11/swiper-element-bundle.min.js"></script>

<script>
    // 🔔 Function แจ้งเตือน Login
    function promptLogin() {
        Swal.fire({
            title: 'กรุณาเข้าสู่ระบบ',
            text: "เพื่อความสะดวกในการจองและติดตามสถานะ กรุณาเข้าสู่ระบบก่อนครับ",
            imageUrl: 'https://cdn-icons-png.flaticon.com/512/295/295128.png',
            imageWidth: 100,
            imageHeight: 100,
            showCancelButton: true,
            confirmButtonText: 'เข้าสู่ระบบ / สมัครสมาชิก',
            cancelButtonText: 'ไว้ก่อน',
            confirmButtonColor: '#16a34a',
            cancelButtonColor: '#9ca3af',
            reverseButtons: true,
            customClass: {
                popup: 'rounded-2xl font-sarabun',
                confirmButton: 'rounded-xl px-4 py-2',
                cancelButton: 'rounded-xl px-4 py-2'
            }
        }).then((result) => {
            if (result.isConfirmed) window.location.href = "{{ route('customer.login') }}";
        });
    }

    // 📅 FullCalendar Setup
    document.addEventListener('DOMContentLoaded', function() {
        var calendarEl = document.getElementById('calendar');
        var isMobile = window.innerWidth < 768;

        var calendar = new FullCalendar.Calendar(calendarEl, {
            locale: 'th',
            initialView: isMobile ? 'listWeek' : 'dayGridMonth',
            headerToolbar: {
                left: 'prev,next today',
                center: 'title',
                right: isMobile ? '' : 'dayGridMonth,listWeek'
            },
            buttonText: { today: 'วันนี้', month: 'เดือน', list: 'รายการ' },
            events: '{{ route("public.calendar") }}',
            eventClick: function(info) {
                Swal.fire({ 
                    title: info.event.title, 
                    html: `ช่วงเวลานี้มีคิวงานแล้ว<br><small class="text-gray-500">กรุณาเลือกช่วงเวลาอื่น</small>`,
                    icon: 'warning', 
                    confirmButtonText: 'รับทราบ',
                    confirmButtonColor: '#d33',
                    customClass: { popup: 'rounded-2xl font-sarabun' }
                });
            },
            dateClick: function(info) {
                @auth('customer')
                    window.location.href = "{{ route('customer.dashboard') }}?date=" + info.dateStr;
                @else
                    promptLogin();
                @endauth
            },
            eventColor: '#ef4444',
            eventTextColor: '#ffffff',
            height: 'auto',
            expandRows: true
        });
        calendar.render();
    });
</script>

<style>
    /* CSS Animation เพิ่มเติม */
    @keyframes fadeInUp {
        from { opacity: 0; transform: translateY(20px); }
        to { opacity: 1; transform: translateY(0); }
    }
    .animate-fade-in-up {
        animation: fadeInUp 0.8s ease-out forwards;
    }
    /* ปรับแต่ง Swiper Pagination */
    swiper-container::part(bullet-active) {
        background-color: #16a34a;
        width: 12px;
        height: 12px;
    }
</style>
@endsection