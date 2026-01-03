<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Staff Portal') - AgriTech</title>

    {{-- Fonts --}}
    <link href="https://fonts.googleapis.com/css2?family=Sarabun:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    {{-- Libraries --}}
    <script src="https://cdn.tailwindcss.com"></script>
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <script>
        tailwind.config = {
            theme: {
                extend: {
                    fontFamily: { sans: ['Sarabun', 'sans-serif'] },
                    colors: {
                        agri: {
                            primary: '#1B4D3E',
                            secondary: '#2C7A62',
                            accent: '#84CC16',
                            bg: '#F3F4F6'
                        }
                    },
                    boxShadow: {
                        'up': '0 -4px 6px -1px rgba(0, 0, 0, 0.1)',
                    }
                }
            }
        }
    </script>
    <style>
        [x-cloak] { display: none !important; }
        .scrollbar-hide::-webkit-scrollbar { display: none; }
        .scrollbar-hide { -ms-overflow-style: none; scrollbar-width: none; }
        * { -webkit-tap-highlight-color: transparent; }
        .pb-safe { padding-bottom: env(safe-area-inset-bottom); }
    </style>
</head>

<body class="bg-agri-bg font-sans text-gray-800 antialiased h-screen flex overflow-hidden">

    {{-- ========================================== --}}
    {{-- 🖥️ DESKTOP SIDEBAR --}}
    {{-- ========================================== --}}
    <aside class="hidden lg:flex w-72 bg-agri-primary text-white border-r border-white/5 flex-col shadow-xl z-50 shrink-0">
        {{-- Logo --}}
        <div class="h-20 flex items-center justify-center border-b border-white/10 bg-black/10">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 rounded-xl bg-white/10 flex items-center justify-center backdrop-blur-sm border border-white/20">
                    <i class="fa-solid fa-leaf text-agri-accent text-xl"></i>
                </div>
                <div>
                    <h1 class="font-bold text-lg leading-tight tracking-wide">AgriTech<span class="text-agri-accent">Pro</span></h1>
                    <p class="text-xs text-white/50 tracking-wider">STAFF PORTAL</p>
                </div>
            </div>
        </div>

        {{-- Desktop Menu --}}
        <nav class="flex-1 overflow-y-auto p-4 space-y-1">
            @php
                $menus = [
                    ['label' => 'หน้าแรก', 'route' => 'staff.dashboard', 'icon' => 'fa-home'],
                    ['label' => 'งานของฉัน', 'route' => 'staff.jobs.index', 'icon' => 'fa-clipboard-user'],
                    
                    // --- ⛽ ส่วนที่เพิ่ม: เมนูเติมน้ำมัน ---
                    ['label' => 'เติมน้ำมัน', 'route' => 'staff.fuel.create', 'icon' => 'fa-gas-pump'],
                    // ------------------------------------

                    ['label' => 'ประวัติงาน', 'route' => 'staff.jobs.history', 'icon' => 'fa-history'],
                    ['label' => 'แจ้งซ่อม/ปัญหา', 'route' => 'staff.maintenance.create', 'icon' => 'fa-triangle-exclamation'],
                ];
            @endphp

            @foreach ($menus as $item)
                @if (Route::has($item['route']))
                    @php
                        $baseRoute = str_replace('.index', '', $item['route']);
                        $isActive = request()->routeIs($item['route']) || request()->routeIs($baseRoute . '.*');
                    @endphp
                    <a href="{{ route($item['route']) }}"
                        class="flex items-center gap-3 px-4 py-3.5 rounded-xl transition-all duration-200 group relative overflow-hidden
                       {{ $isActive ? 'bg-white/10 text-white font-bold shadow-lg' : 'text-gray-300 hover:bg-white/5 hover:text-white' }}">

                        @if($isActive) <div class="absolute left-0 top-0 bottom-0 w-1 bg-agri-accent"></div> @endif

                        <div class="w-8 flex justify-center">
                            <i class="fa-solid {{ $item['icon'] }} text-lg {{ $isActive ? 'text-agri-accent' : 'text-gray-400 group-hover:text-white' }} transition-colors"></i>
                        </div>
                        <span class="relative z-10">{{ $item['label'] }}</span>
                    </a>
                @endif
            @endforeach
        </nav>

        {{-- Logout --}}
        <div class="p-4 border-t border-white/10 bg-black/10">
            <button onclick="confirmLogout()"
                class="w-full flex items-center justify-center gap-2 px-4 py-3 rounded-xl text-red-300 font-medium hover:bg-red-500/20 hover:text-red-200 transition-colors">
                <i class="fa-solid fa-right-from-bracket"></i> ออกจากระบบ
            </button>
        </div>
    </aside>

    {{-- ========================================== --}}
    {{-- MAIN CONTENT AREA --}}
    {{-- ========================================== --}}
    <div class="flex-1 flex flex-col w-full h-full relative bg-gray-50">

        {{-- 🖥️ Desktop Header --}}
        <header class="hidden lg:flex h-16 bg-white/90 backdrop-blur-md border-b border-gray-200 items-center justify-between px-8 sticky top-0 z-30 shadow-sm">
            <h1 class="text-xl font-bold text-agri-primary">@yield('header', 'Staff Dashboard')</h1>
            
            <div class="flex items-center gap-4">
                <div class="text-right">
                    <p class="text-sm font-bold text-gray-800">{{ Auth::user()->name ?? 'Staff' }}</p>
                    <p class="text-xs text-agri-secondary">พนักงานปฏิบัติการ</p>
                </div>
                <img class="w-10 h-10 rounded-full border-2 border-agri-primary/20"
                    src="https://ui-avatars.com/api/?name={{ Auth::user()->name ?? 'Staff' }}&background=1B4D3E&color=fff" alt="Avatar">
            </div>
        </header>

        {{-- 📱 Mobile Header --}}
        <header class="lg:hidden h-16 bg-white border-b border-gray-200 flex items-center justify-between px-4 shadow-sm z-10 sticky top-0">
            <div class="flex items-center gap-3">
                <div class="w-9 h-9 rounded-lg bg-agri-primary text-white flex items-center justify-center">
                    <i class="fa-solid fa-leaf text-agri-accent"></i>
                </div>
                <h1 class="font-bold text-gray-800 text-lg">@yield('header', 'AgriTech')</h1>
            </div>
            {{-- Profile Dropdown Mobile --}}
            <div class="relative" x-data="{ open: false }">
                <button @click="open = !open" class="flex items-center gap-2 focus:outline-none">
                    <img class="w-9 h-9 rounded-full border border-gray-200"
                        src="https://ui-avatars.com/api/?name={{ Auth::user()->name ?? 'Staff' }}&background=1B4D3E&color=fff" alt="Avatar">
                </button>
                <div x-show="open" @click.away="open = false" x-cloak class="absolute right-0 mt-2 w-48 bg-white rounded-xl shadow-xl border border-gray-100 py-1 z-50">
                    <div class="px-4 py-3 border-b border-gray-100">
                        <p class="text-sm font-bold text-gray-800 truncate">{{ Auth::user()->name }}</p>
                    </div>
                    <button onclick="confirmLogout()" class="w-full text-left px-4 py-3 text-sm text-red-600 hover:bg-red-50 flex items-center gap-2">
                        <i class="fa-solid fa-right-from-bracket"></i> ออกจากระบบ
                    </button>
                </div>
            </div>
        </header>

        {{-- Content Scrollable --}}
        <main class="flex-1 overflow-x-hidden overflow-y-auto scroll-smooth pb-24 lg:pb-8 p-4 lg:p-8">
            {{-- Flash Message --}}
            @if (session('success'))
                <div x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 4000)" 
                    class="mb-6 bg-green-50 border-l-4 border-green-500 p-4 rounded-r-lg shadow-sm flex items-start gap-3 animate-fade-in-down">
                    <div class="text-green-500 mt-0.5"><i class="fa-solid fa-circle-check text-xl"></i></div>
                    <div>
                        <h3 class="text-sm font-bold text-green-800">สำเร็จ!</h3>
                        <p class="text-sm text-green-700 mt-1">{{ session('success') }}</p>
                    </div>
                </div>
            @endif

            @yield('content')
        </main>

        {{-- 📱 MOBILE BOTTOM NAVBAR --}}
        <nav class="lg:hidden fixed bottom-0 left-0 right-0 z-40 pb-safe">
            <div class="bg-white/95 backdrop-blur-md border-t border-gray-100 shadow-[0_-8px_30px_rgba(0,0,0,0.08)] h-[80px] rounded-t-3xl px-6">
                {{-- 🔴 เปลี่ยน grid-cols-4 เป็น grid-cols-5 เพื่อรองรับ 5 ปุ่ม --}}
                <div class="grid grid-cols-5 h-full items-center justify-between gap-1">
                    @php
                        $mobileMenus = [
                            ['route' => 'staff.dashboard', 'icon' => 'fa-house', 'label' => 'หน้าแรก', 'color' => 'text-agri-primary'],
                            ['route' => 'staff.jobs.index', 'icon' => 'fa-clipboard-list', 'label' => 'งาน', 'color' => 'text-blue-500'],
                            
                            // --- ⛽ ส่วนที่เพิ่ม: ปุ่มเติมน้ำมัน (Mobile) ---
                            ['route' => 'staff.fuel.create', 'icon' => 'fa-gas-pump', 'label' => 'เติมน้ำมัน', 'color' => 'text-purple-500'],
                            // -------------------------------------------
                            
                            ['route' => 'staff.maintenance.create', 'icon' => 'fa-screwdriver-wrench', 'label' => 'แจ้งซ่อม', 'color' => 'text-orange-500'],
                        ];
                    @endphp

                    @foreach ($mobileMenus as $item)
                        @if (Route::has($item['route']))
                            @php $isActive = request()->routeIs($item['route']); @endphp
                            <a href="{{ route($item['route']) }}" class="group relative flex flex-col items-center justify-center w-full h-full cursor-pointer select-none">
                                <div class="absolute top-2 w-10 h-10 rounded-full bg-current opacity-0 transition-all duration-300 transform scale-0 {{ $item['color'] }} {{ $isActive ? 'opacity-10 scale-100' : 'group-hover:opacity-5' }}"></div>
                                <div class="relative transition-all duration-300 ease-out transform {{ $isActive ? '-translate-y-1.5 scale-110' : 'group-hover:-translate-y-1' }}">
                                    <i class="fa-solid {{ $item['icon'] }} text-2xl transition-colors duration-300 {{ $isActive ? $item['color'] : 'text-gray-400 group-hover:text-gray-600' }}"></i>
                                </div>
                                <span class="text-[10px] font-medium mt-1 transition-all duration-300 {{ $isActive ? 'opacity-100 translate-y-0 ' . $item['color'] : 'opacity-70 text-gray-400 translate-y-1 group-hover:text-gray-600' }}">{{ $item['label'] }}</span>
                            </a>
                        @endif
                    @endforeach

                    <button onclick="confirmLogout()" class="group relative flex flex-col items-center justify-center w-full h-full cursor-pointer select-none">
                        <div class="relative transition-all duration-300 ease-out transform group-hover:-translate-y-1">
                            <i class="fa-solid fa-power-off text-2xl text-gray-400 transition-colors duration-300 group-hover:text-red-500"></i>
                        </div>
                        <span class="text-[10px] font-medium mt-1 text-gray-400 transition-all duration-300 opacity-70 translate-y-1 group-hover:text-red-500 group-hover:translate-y-0">ออกระบบ</span>
                    </button>
                </div>
            </div>
        </nav>
    </div>

    <form id="logout-form" action="{{ route('logout') }}" method="POST" class="hidden">@csrf</form>

    <script>
        function confirmLogout() {
            Swal.fire({
                title: 'ยืนยันการออกจากระบบ?',
                text: "คุณต้องการออกจากระบบใช่หรือไม่",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#ef4444',
                cancelButtonColor: '#9ca3af',
                confirmButtonText: 'ใช่, ออกจากระบบ',
                cancelButtonText: 'ยกเลิก',
                reverseButtons: true,
                customClass: { popup: 'rounded-2xl' }
            }).then((result) => {
                if (result.isConfirmed) document.getElementById('logout-form').submit();
            });
        }
    </script>
</body>
</html>