@extends('layouts.guest')

@section('content')
{{-- SweetAlert2 CDN --}}
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<div class="min-h-screen flex items-center justify-center bg-slate-100 p-4 font-sans">
    
    {{-- Card Container --}}
    <div class="w-full max-w-md bg-white rounded-2xl shadow-xl overflow-hidden border border-gray-100">
        
        {{-- Header สีเขียว --}}
        <div class="bg-green-600 p-6 text-center relative overflow-hidden">
            <div class="absolute top-0 left-0 w-full h-full opacity-10">
                <i class="fa-solid fa-tractor absolute bottom-2 right-2 text-6xl text-white transform rotate-12"></i>
            </div>

            <div class="relative z-10 text-white">
                <div class="w-14 h-14 bg-white/20 backdrop-blur-sm rounded-full flex items-center justify-center mx-auto mb-3 shadow-sm">
                    <i class="fa-solid fa-user-tag text-2xl"></i>
                </div>
                <h2 class="text-xl font-bold tracking-wide">เข้าสู่ระบบลูกค้า</h2>
                <p class="text-green-100 text-sm">Agri-Equip Service</p>
            </div>
        </div>

        {{-- Form Content --}}
        <div class="p-8">
            <form method="POST" action="{{ route('customer.login.submit') }}" class="space-y-5" id="customerLoginForm">
                @csrf

                {{-- 1. เบอร์โทรศัพท์ --}}
                <div>
                    <label for="phone" class="block text-sm font-medium text-gray-700 mb-1">เบอร์โทรศัพท์</label>
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            <i class="fa-solid fa-phone text-gray-400"></i>
                        </div>
                        <input id="phone" type="tel" name="phone" value="{{ old('phone') }}" required autofocus
                               class="block w-full pl-10 pr-3 py-3 border border-gray-300 rounded-xl text-gray-900 placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-transparent transition" 
                               placeholder="08X-XXX-XXXX">
                    </div>
                    @error('phone')
                        <p class="mt-1 text-xs text-red-500 flex items-center gap-1"><i class="fa-solid fa-circle-exclamation"></i> {{ $message }}</p>
                    @enderror
                </div>

                {{-- 2. รหัสผ่าน --}}
                <div>
                    <label for="password" class="block text-sm font-medium text-gray-700 mb-1">
                        รหัสผ่าน <span class="text-xs text-gray-400 font-normal">(4 ตัวท้ายเบอร์โทร)</span>
                    </label>
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            <i class="fa-solid fa-lock text-gray-400"></i>
                        </div>
                        <input id="password" type="password" name="password" required
                               class="block w-full pl-10 pr-3 py-3 border border-gray-300 rounded-xl text-gray-900 placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-transparent transition" 
                               placeholder="••••">
                    </div>
                    @error('password')
                        <p class="mt-1 text-xs text-red-500 flex items-center gap-1"><i class="fa-solid fa-circle-exclamation"></i> {{ $message }}</p>
                    @enderror
                </div>

                {{-- Remember Me --}}
                <div class="flex items-center">
                    <input id="remember" name="remember" type="checkbox" class="h-4 w-4 text-green-600 focus:ring-green-500 border-gray-300 rounded cursor-pointer">
                    <label for="remember" class="ml-2 block text-sm text-gray-600 cursor-pointer select-none">จำฉันไว้ในระบบ</label>
                </div>

                {{-- ปุ่ม Login --}}
                <button type="submit" 
                        class="w-full py-3 bg-green-600 text-white font-bold rounded-xl shadow-md hover:bg-green-700 hover:shadow-lg focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500 transition-all transform active:scale-95 flex items-center justify-center gap-2">
                    <i class="fa-solid fa-right-to-bracket"></i> เข้าสู่ระบบ
                </button>

            </form>

            {{-- Footer Links --}}
            <div class="mt-6 pt-6 border-t border-gray-100 text-center">
                <a href="{{ route('login') }}" class="text-sm text-gray-400 hover:text-green-600 transition inline-flex items-center gap-1">
                    <i class="fa-solid fa-user-shield"></i> เข้าสู่ระบบเจ้าหน้าที่
                </a>
            </div>
        </div>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        
        // ✅ 1. แจ้งเตือนเมื่อ Login ผิดพลาด (Error)
        @if(session('error'))
            Swal.fire({
                icon: 'error',
                title: 'เข้าสู่ระบบไม่สำเร็จ',
                text: "{{ session('error') }}",
                confirmButtonColor: '#16a34a', // green-600
                confirmButtonText: 'ตกลง',
                timer: 3000,
                timerProgressBar: true
            });
        @endif

        // ✅ 2. แจ้งเตือนเมื่อ Logout สำเร็จ (Success)
        @if(session('status'))
            Swal.fire({
                icon: 'success',
                title: 'สำเร็จ',
                text: "{{ session('status') }}",
                confirmButtonColor: '#16a34a',
                timer: 2000,
                showConfirmButton: false
            });
        @endif

        // ✅ 3. เพิ่มลูกเล่น Loading เมื่อกดปุ่ม Submit
        const form = document.getElementById('customerLoginForm');
        form.addEventListener('submit', function() {
            // เช็คก่อนว่ากรอกข้อมูลครบไหม (ง่ายๆ)
            const phone = document.getElementById('phone').value;
            const pass = document.getElementById('password').value;

            if(phone && pass) {
                Swal.fire({
                    title: 'กำลังตรวจสอบข้อมูล',
                    text: 'กรุณารอสักครู่...',
                    allowOutsideClick: false,
                    showConfirmButton: false,
                    didOpen: () => {
                        Swal.showLoading();
                    }
                });
            }
        });
    });
</script>
@endsection