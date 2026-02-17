<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="Content-Security-Policy" content="upgrade-insecure-requests">
    <title>Customer Dashboard - AgriTech Management</title>
    
    {{-- 1. Fonts & Icons (โหลด CSS ก่อน) --}}
    <link href="https://fonts.googleapis.com/css2?family=Sarabun:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    
    {{-- ✅ อัปเดต Font Awesome เป็น 6.5.1 (ใหม่ล่าสุด) --}}
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css" integrity="sha512-DTOQO9RWCH3ppGqcWaEA1BIZOC6xxalwEsw9c2QQeAIftl+Vegovlnee1c9QX4TctnWMn13TZye+giMm8e2LwA==" crossorigin="anonymous" referrerpolicy="no-referrer" />

    {{-- 2. Styles & Scripts --}}
    <script src="https://cdn.tailwindcss.com"></script>
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>

    <script>
        tailwind.config = {
            theme: {
                extend: {
                    fontFamily: { sans: ['Sarabun', 'sans-serif'] },
                    colors: {
                        agri: { 
                            primary: '#1B4D3E',   // เขียวเข้ม (Main)
                            secondary: '#163E31', // เขียวเข้มกว่า (Hover)
                            accent: '#84CC16',    // เขียวอ่อน (Highlight)
                            bg: '#F3F4F6'         // พื้นหลังสีเทาอ่อน
                        }
                    }
                }
            }
        }
    </script>

    <style>
        /* ป้องกันไอคอนเบี้ยวหรือโหลดช้า */
        .fa-solid, .fas { font-family: "Font Awesome 6 Free" !important; }
        [x-cloak] { display: none !important; }
    </style>
</head>
<body class="bg-agri-bg font-sans text-gray-800 antialiased min-h-screen flex flex-col">

    {{-- Navbar --}}
    <nav class="bg-agri-primary text-white shadow-lg sticky top-0 z-50">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between h-16">
                
                {{-- Logo --}}
                <div class="flex items-center gap-3">
                    <a href="{{ route('customer.dashboard') }}" class="flex items-center gap-3 hover:opacity-90 transition">
                        <div class="w-10 h-10 rounded-full bg-white/10 flex items-center justify-center border border-white/20">
                            <i class="fa-solid fa-tractor text-agri-accent text-xl"></i>
                        </div>
                        <span class="font-bold text-xl tracking-wide">AgriTech<span class="text-agri-accent">Pro</span></span>
                    </a>
                </div>
                
                {{-- User Menu --}}
                <div class="flex items-center gap-4">
                    {{-- ชื่อผู้ใช้งาน (ซ่อนบนมือถือ) --}}
                    <div class="hidden md:block text-right">
                        <div class="text-sm font-medium text-gray-100">{{ Auth::guard('customer')->user()->name }}</div>
                        <div class="text-xs text-agri-accent flex items-center justify-end gap-1">
                            <i class="fa-solid fa-phone text-[10px]"></i> {{ Auth::guard('customer')->user()->phone }}
                        </div>
                    </div>

                    {{-- Logout Button --}}
                    <form action="{{ route('customer.logout') }}" method="POST">
                        @csrf
                        <button type="submit" class="bg-red-500/90 hover:bg-red-600 text-white px-4 py-2 rounded-lg text-sm transition shadow-md flex items-center gap-2 group">
                            <i class="fa-solid fa-right-from-bracket group-hover:rotate-180 transition-transform duration-300"></i> 
                            <span class="hidden md:inline">ออกจากระบบ</span>
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </nav>

    {{-- Content --}}
    <main class="flex-grow py-8 px-4 sm:px-6 lg:px-8">
        <div class="max-w-7xl mx-auto">
            @yield('content')
        </div>
    </main>

    {{-- Footer --}}
    <footer class="bg-white border-t border-gray-200 py-6 mt-auto">
        <div class="max-w-7xl mx-auto px-4 text-center text-gray-500 text-sm flex flex-col md:flex-row justify-between items-center gap-2">
            <p>&copy; {{ date('Y') }} AgriTech Management System. All rights reserved.</p>
            <p class="text-xs text-gray-400">
                <i class="fa-solid fa-code text-agri-primary"></i> Developed for Community
            </p>
        </div>
    </footer>

</body>
</html>