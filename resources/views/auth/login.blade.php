@extends('layouts.guest')

@section('title', 'เข้าสู่ระบบ')

@section('content')
<div class="w-full max-w-md mx-auto p-6">
    <div class="text-center mb-8">
        {{-- โลโก้ (ถ้ามี) --}}
        <div class="inline-flex items-center justify-center w-16 h-16 rounded-full bg-agri-primary/10 text-agri-primary mb-4">
            <i class="fa-solid fa-tractor text-3xl"></i>
        </div>
        <h2 class="text-2xl font-bold text-gray-800">เข้าสู่ระบบ Agri-Equip</h2>
        <p class="text-gray-500 text-sm mt-2">ระบบบริหารจัดการเครื่องจักรและงานบริการ</p>
    </div>

    {{-- แสดง Error --}}
    @if ($errors->any())
        <div class="mb-4 p-4 bg-red-50 border-l-4 border-red-500 rounded-r-lg">
            <div class="flex items-center gap-2 text-red-700 font-bold mb-1">
                <i class="fa-solid fa-circle-exclamation"></i> ผิดพลาด
            </div>
            <ul class="list-disc list-inside text-sm text-red-600">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form method="POST" action="{{ route('login.submit') }}" class="space-y-5">
        @csrf

        {{-- Username --}}
        <div>
            <label for="username" class="block text-sm font-bold text-gray-700 mb-1">ชื่อผู้ใช้ (Username)</label>
            <div class="relative">
                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-gray-400">
                    <i class="fa-solid fa-user"></i>
                </div>
                <input type="text" id="username" name="username" value="{{ old('username') }}" required autofocus
                    class="block w-full pl-10 pr-3 py-3 border border-gray-200 rounded-xl focus:ring-2 focus:ring-agri-primary focus:border-agri-primary transition outline-none"
                    placeholder="ระบุชื่อผู้ใช้ของคุณ">
            </div>
        </div>

        {{-- Password --}}
        <div>
            <label for="password" class="block text-sm font-bold text-gray-700 mb-1">รหัสผ่าน (Password)</label>
            <div class="relative">
                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-gray-400">
                    <i class="fa-solid fa-lock"></i>
                </div>
                <input type="password" id="password" name="password" required
                    class="block w-full pl-10 pr-3 py-3 border border-gray-200 rounded-xl focus:ring-2 focus:ring-agri-primary focus:border-agri-primary transition outline-none"
                    placeholder="ระบุรหัสผ่าน">
            </div>
        </div>

        {{-- Remember Me --}}
        <div class="flex items-center justify-between">
            <label class="flex items-center gap-2 cursor-pointer">
                <input type="checkbox" name="remember" class="w-4 h-4 text-agri-primary border-gray-300 rounded focus:ring-agri-primary">
                <span class="text-sm text-gray-600">จดจำฉันไว้ในระบบ</span>
            </label>
        </div>

        {{-- Submit Button --}}
        <button type="submit" 
            class="w-full bg-agri-primary text-white py-3.5 rounded-xl font-bold text-lg hover:bg-agri-hover hover:shadow-lg transition-all duration-200 active:scale-95 flex items-center justify-center gap-2">
            <span>เข้าสู่ระบบ</span>
            <i class="fa-solid fa-arrow-right"></i>
        </button>
    </form>
    <div class="mt-4 text-center">
    <span class="text-gray-500 text-sm">หรือ</span>
    <a href="{{ route('staff.login') }}" class="text-green-600 hover:text-green-800 font-bold text-sm ml-1">
        <i class="fa-solid fa-id-card-clip"></i> เข้าสู่ระบบพนักงาน (PIN)
    </a>
</div>
    <div class="mt-8 text-center text-xs text-gray-400">
        &copy; {{ date('Y') }} Agri-Equip Management System
    </div>
</div>
@endsection