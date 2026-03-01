@extends('layouts.admin')

@section('title', 'เมนูทั้งหมด')
@section('header', 'เมนูระบบ (All Apps)')

@section('content')
<div class="max-w-5xl mx-auto pb-12">

    {{-- Hero Section --}}
    <div class="bg-gradient-to-r from-agri-primary to-green-700 rounded-2xl p-6 text-white shadow-lg mb-8 relative overflow-hidden">
        <div class="relative z-10 flex justify-between items-center">
            <div>
                <h2 class="text-2xl font-bold mb-1">ยินดีต้อนรับ, {{ Auth::user()->name }} 👋</h2>
                <p class="text-green-100 text-sm">เลือกเมนูที่ต้องการจัดการ</p>
            </div>
            <div class="hidden md:block opacity-20">
                <i class="fa-solid fa-cubes-stacked text-6xl"></i>
            </div>
        </div>
        {{-- Decoration Circles --}}
        <div class="absolute top-0 right-0 w-32 h-32 bg-white/10 rounded-full -mr-10 -mt-10 blur-xl"></div>
        <div class="absolute bottom-0 left-0 w-24 h-24 bg-white/10 rounded-full -ml-5 -mb-5 blur-xl"></div>
    </div>

    {{-- Grid Container --}}
    <div class="space-y-8">

        {{-- 1. งานปฏิบัติการ (Operations) --}}
        <div>
            <div class="flex items-center gap-3 mb-4 px-2">
                <div class="w-8 h-8 rounded-lg bg-blue-100 flex items-center justify-center text-blue-600">
                    <i class="fa-solid fa-briefcase"></i>
                </div>
                <h3 class="font-bold text-gray-700 text-lg">งานปฏิบัติการ</h3>
            </div>
            
            <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                {{-- Jobs --}}
                <a href="{{ route('admin.jobs.index') }}" class="group bg-white p-6 rounded-2xl shadow-sm border border-gray-100 hover:shadow-md hover:border-blue-200 transition-all duration-300 relative overflow-hidden">
                    <div class="absolute right-0 top-0 p-4 opacity-5 group-hover:scale-125 transition-transform duration-500">
                        <i class="fa-solid fa-clipboard-list text-6xl text-blue-600"></i>
                    </div>
                    <div class="w-14 h-14 rounded-2xl bg-blue-50 text-blue-600 flex items-center justify-center text-2xl mb-3 group-hover:scale-110 transition-transform">
                        <i class="fa-solid fa-clipboard-list"></i>
                    </div>
                    <h4 class="font-bold text-gray-800 group-hover:text-blue-600 transition-colors">งานบริการ</h4>
                    <p class="text-xs text-gray-400 mt-1">จัดการคิวงานและสถานะ</p>
                </a>

                {{-- Maintenance --}}
                <a href="{{ route('admin.maintenance.index') }}" class="group bg-white p-6 rounded-2xl shadow-sm border border-gray-100 hover:shadow-md hover:border-orange-200 transition-all duration-300 relative overflow-hidden">
                    <div class="absolute right-0 top-0 p-4 opacity-5 group-hover:scale-125 transition-transform duration-500">
                        <i class="fa-solid fa-screwdriver-wrench text-6xl text-orange-500"></i>
                    </div>
                    <div class="w-14 h-14 rounded-2xl bg-orange-50 text-orange-500 flex items-center justify-center text-2xl mb-3 group-hover:scale-110 transition-transform">
                        <i class="fa-solid fa-screwdriver-wrench"></i>
                    </div>
                    <h4 class="font-bold text-gray-800 group-hover:text-orange-500 transition-colors">แจ้งซ่อมบำรุง</h4>
                    <p class="text-xs text-gray-400 mt-1">ติดตามการซ่อมเครื่องจักร</p>
                </a>

                {{-- Equipments --}}
                <a href="{{ route('admin.equipments.index') }}" class="group bg-white p-6 rounded-2xl shadow-sm border border-gray-100 hover:shadow-md hover:border-indigo-200 transition-all duration-300 relative overflow-hidden">
                    <div class="absolute right-0 top-0 p-4 opacity-5 group-hover:scale-125 transition-transform duration-500">
                        <i class="fa-solid fa-tractor text-6xl text-indigo-600"></i>
                    </div>
                    <div class="w-14 h-14 rounded-2xl bg-indigo-50 text-indigo-600 flex items-center justify-center text-2xl mb-3 group-hover:scale-110 transition-transform">
                        <i class="fa-solid fa-tractor"></i>
                    </div>
                    <h4 class="font-bold text-gray-800 group-hover:text-indigo-600 transition-colors">เครื่องจักร</h4>
                    <p class="text-xs text-gray-400 mt-1">ทะเบียนและสถานะรถ</p>
                </a>
            </div>
        </div>

        {{-- 2. ระบบน้ำมัน (Fuel System) --}}
        <div>
            <div class="flex items-center gap-3 mb-4 px-2">
                <div class="w-8 h-8 rounded-lg bg-yellow-100 flex items-center justify-center text-yellow-600">
                    <i class="fa-solid fa-gas-pump"></i>
                </div>
                <h3 class="font-bold text-gray-700 text-lg">ระบบน้ำมัน</h3>
            </div>

            <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                {{-- Fuel Stock --}}
                <a href="{{ route('admin.fuel.index') }}" class="group bg-white p-6 rounded-2xl shadow-sm border border-gray-100 hover:shadow-md hover:border-yellow-300 transition-all duration-300 relative overflow-hidden">
                    <div class="absolute right-0 top-0 p-4 opacity-5 group-hover:scale-125 transition-transform duration-500">
                        <i class="fa-solid fa-layer-group text-6xl text-yellow-600"></i>
                    </div>
                    <div class="w-14 h-14 rounded-2xl bg-yellow-50 text-yellow-600 flex items-center justify-center text-2xl mb-3 group-hover:scale-110 transition-transform">
                        <i class="fa-solid fa-layer-group"></i>
                    </div>
                    <h4 class="font-bold text-gray-800 group-hover:text-yellow-600 transition-colors">คลังน้ำมัน</h4>
                    <p class="text-xs text-gray-400 mt-1">เช็คสต็อก/ถังน้ำมัน</p>
                </a>

                {{-- Fuel Purchase --}}
                <a href="{{ route('admin.fuel.purchase') }}" class="group bg-white p-6 rounded-2xl shadow-sm border border-gray-100 hover:shadow-md hover:border-cyan-200 transition-all duration-300 relative overflow-hidden">
                    <div class="absolute right-0 top-0 p-4 opacity-5 group-hover:scale-125 transition-transform duration-500">
                        <i class="fa-solid fa-cart-plus text-6xl text-cyan-600"></i>
                    </div>
                    <div class="w-14 h-14 rounded-2xl bg-cyan-50 text-cyan-600 flex items-center justify-center text-2xl mb-3 group-hover:scale-110 transition-transform">
                        <i class="fa-solid fa-cart-plus"></i>
                    </div>
                    <h4 class="font-bold text-gray-800 group-hover:text-cyan-600 transition-colors">ซื้อน้ำมันเข้า</h4>
                    <p class="text-xs text-gray-400 mt-1">บันทึกรับน้ำมัน (Stock In)</p>
                </a>
            </div>
        </div>

        {{-- 3. ลูกค้า (Customers) --}}
        <div>
            <div class="flex items-center gap-3 mb-4 px-2">
                <div class="w-8 h-8 rounded-lg bg-teal-100 flex items-center justify-center text-teal-600">
                    <i class="fa-solid fa-users"></i>
                </div>
                <h3 class="font-bold text-gray-700 text-lg">ลูกค้า</h3>
            </div>

            <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                <a href="{{ route('admin.customers.index') }}" class="group bg-white p-6 rounded-2xl shadow-sm border border-gray-100 hover:shadow-md hover:border-teal-200 transition-all duration-300">
                    <div class="w-14 h-14 rounded-2xl bg-teal-50 text-teal-600 flex items-center justify-center text-2xl mb-3 group-hover:scale-110 transition-transform">
                        <i class="fa-solid fa-users"></i>
                    </div>
                    <h4 class="font-bold text-gray-800 group-hover:text-teal-600 transition-colors">ลูกค้า</h4>
                    <p class="text-xs text-gray-400 mt-1">รายชื่อผู้ใช้บริการ</p>
                </a>
            </div>
        </div>

        {{-- 4. บุคลากร (Personnel) --}}
        <div>
            <div class="flex items-center gap-3 mb-4 px-2">
                <div class="w-8 h-8 rounded-lg bg-green-100 flex items-center justify-center text-green-600">
                    <i class="fa-solid fa-users-gear"></i>
                </div>
                <h3 class="font-bold text-gray-700 text-lg">บุคลากร</h3>
            </div>

            <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                <a href="{{ route('admin.users.index') }}" class="group bg-white p-6 rounded-2xl shadow-sm border border-gray-100 hover:shadow-md hover:border-green-200 transition-all duration-300">
                    <div class="w-14 h-14 rounded-2xl bg-green-50 text-green-600 flex items-center justify-center text-2xl mb-3 group-hover:scale-110 transition-transform">
                        <i class="fa-solid fa-id-card-clip"></i>
                    </div>
                    <h4 class="font-bold text-gray-800 group-hover:text-green-600 transition-colors">พนักงาน</h4>
                    <p class="text-xs text-gray-400 mt-1">จัดการทีมงาน/คนขับ</p>
                </a>
            </div>
        </div>

        {{-- 5. รายงานและระบบ (Reports & System) --}}
        <div>
            <div class="flex items-center gap-3 mb-4 px-2">
                <div class="w-8 h-8 rounded-lg bg-purple-100 flex items-center justify-center text-purple-600">
                    <i class="fa-solid fa-chart-pie"></i>
                </div>
                <h3 class="font-bold text-gray-700 text-lg">รายงานและระบบ</h3>
            </div>

            <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                <a href="{{ route('admin.reports.index') }}" class="group bg-white p-6 rounded-2xl shadow-sm border border-gray-100 hover:shadow-md hover:border-purple-200 transition-all duration-300 relative overflow-hidden">
                    <div class="absolute right-0 top-0 p-4 opacity-5 group-hover:scale-125 transition-transform duration-500">
                        <i class="fa-solid fa-file-invoice-dollar text-6xl text-purple-600"></i>
                    </div>
                    <div class="w-14 h-14 rounded-2xl bg-purple-50 text-purple-600 flex items-center justify-center text-2xl mb-3 group-hover:scale-110 transition-transform">
                        <i class="fa-solid fa-file-invoice-dollar"></i>
                    </div>
                    <h4 class="font-bold text-gray-800 group-hover:text-purple-600 transition-colors">รายงานสรุป</h4>
                    <p class="text-xs text-gray-400 mt-1">สถิติและการเงิน</p>
                </a>

                <a href="{{ route('admin.profile') }}" class="group bg-white p-6 rounded-2xl shadow-sm border border-gray-100 hover:shadow-md hover:border-gray-300 transition-all duration-300">
                    <div class="w-14 h-14 rounded-2xl bg-gray-50 text-gray-600 flex items-center justify-center text-2xl mb-3 group-hover:scale-110 transition-transform">
                        <i class="fa-solid fa-user-circle"></i>
                    </div>
                    <h4 class="font-bold text-gray-800 group-hover:text-gray-900 transition-colors">โปรไฟล์</h4>
                    <p class="text-xs text-gray-400 mt-1">ข้อมูลส่วนตัว</p>
                </a>
                
                @if(Route::has('admin.settings.index'))
                <a href="{{ route('admin.settings.index') }}" class="group bg-white p-6 rounded-2xl shadow-sm border border-gray-100 hover:shadow-md hover:border-gray-300 transition-all duration-300">
                    <div class="w-14 h-14 rounded-2xl bg-gray-50 text-gray-600 flex items-center justify-center text-2xl mb-3 group-hover:scale-110 group-hover:rotate-90 transition-transform duration-500">
                        <i class="fa-solid fa-gear"></i>
                    </div>
                    <h4 class="font-bold text-gray-800 group-hover:text-gray-900 transition-colors">ตั้งค่าระบบ</h4>
                    <p class="text-xs text-gray-400 mt-1">Configuration</p>
                </a>
                @endif

                <button onclick="confirmLogout()" class="group bg-white p-6 rounded-2xl shadow-sm border border-gray-100 hover:shadow-md hover:border-red-200 hover:bg-red-50 transition-all duration-300 text-left w-full">
                    <div class="w-14 h-14 rounded-2xl bg-red-50 text-red-500 flex items-center justify-center text-2xl mb-3 group-hover:bg-white group-hover:scale-110 transition-transform">
                        <i class="fa-solid fa-power-off"></i>
                    </div>
                    <h4 class="font-bold text-gray-800 group-hover:text-red-600 transition-colors">ออกจากระบบ</h4>
                    <p class="text-xs text-gray-400 mt-1 group-hover:text-red-400">Logout</p>
                </button>
            </div>
        </div>

    </div>
</div>
@endsection