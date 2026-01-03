<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, viewport-fit=cover">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'AgriTech Admin')</title>
    
    {{-- Fonts & Icons --}}
    <link href="https://fonts.googleapis.com/css2?family=Sarabun:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    {{-- Tailwind & Alpine --}}
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
                            bg: '#F8FAFC', 
                            hover: '#143d30' 
                        } 
                    }
                }
            }
        }
    </script>
    <style>
        .pb-safe { padding-bottom: env(safe-area-inset-bottom); }
        ::-webkit-scrollbar { width: 6px; }
        ::-webkit-scrollbar-thumb { background: #cbd5e1; border-radius: 3px; }
        [x-cloak] { display: none !important; }
        .glass-nav {
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(10px);
            border-top: 1px solid rgba(0,0,0,0.05);
        }
    </style>
</head>
<body class="bg-agri-bg font-sans text-gray-800 antialiased h-screen overflow-hidden flex selection:bg-agri-accent selection:text-white">

    {{-- DESKTOP SIDEBAR --}}
    <aside class="hidden lg:flex flex-col w-72 bg-agri-primary text-white shadow-2xl z-50 shrink-0">
        {{-- Logo --}}
        <div class="flex items-center gap-3 px-6 h-20 shrink-0 bg-black/10">
            <div class="w-10 h-10 rounded-xl bg-gradient-to-br from-agri-accent to-green-600 flex items-center justify-center text-white shadow-lg">
                <i class="fa-solid fa-leaf text-lg"></i>
            </div>
            <div>
                <span class="font-bold text-xl tracking-wide block leading-none">AgriTech</span>
                <span class="text-[10px] text-agri-accent tracking-widest font-medium opacity-80">ADMIN SYSTEM</span>
            </div>
        </div>

        {{-- Menu List --}}
        <nav class="flex-1 overflow-y-auto py-6 px-4 space-y-1.5 scrollbar-hide">
            @php
                $desktopMenus = [
                    ['label' => 'ภาพรวมระบบ', 'route' => 'admin.dashboard', 'icon' => 'fa-chart-pie'],
                    ['label' => 'งานบริการ', 'route' => 'admin.jobs.index', 'icon' => 'fa-clipboard-list'],
                    ['label' => 'ข้อมูลลูกค้า', 'route' => 'admin.customers.index', 'icon' => 'fa-users'],
                    ['label' => 'จัดการพนักงาน', 'route' => 'admin.users.index', 'icon' => 'fa-id-card-clip'],
                    ['label' => 'เครื่องจักร', 'route' => 'admin.equipments.index', 'icon' => 'fa-tractor'],
                    
                    // --- ⛽ ส่วนที่เพิ่ม: ระบบน้ำมัน ---
                    ['label' => 'คลังน้ำมัน', 'route' => 'admin.fuel.index', 'icon' => 'fa-gas-pump'],
                    ['label' => 'ซื้อน้ำมันเข้า', 'route' => 'admin.fuel.purchase', 'icon' => 'fa-cart-plus'],
                    // -------------------------------
                    
                    ['label' => 'การซ่อมบำรุง', 'route' => 'admin.maintenance.index', 'icon' => 'fa-screwdriver-wrench'],
                    ['label' => 'รายงาน', 'route' => 'admin.reports.index', 'icon' => 'fa-file-invoice-dollar'],
                ];
            @endphp
            
            @foreach($desktopMenus as $menu)
                @if(Route::has($menu['route']))
                    @php 
                        $baseRoute = explode('.', $menu['route']); 
                        $prefix = $baseRoute[0] . '.' . $baseRoute[1];
                        $isActive = request()->routeIs($menu['route']) || request()->routeIs($prefix . '*');
                    @endphp
                    <a href="{{ route($menu['route']) }}" 
                       class="flex items-center gap-3 px-4 py-3 rounded-xl transition-all duration-200 group relative
                       {{ $isActive ? 'bg-white/10 text-white font-bold shadow-inner' : 'text-gray-300 hover:bg-white/5 hover:text-white' }}">
                        
                        @if($isActive) <div class="absolute left-0 top-1/2 -translate-y-1/2 h-8 w-1 bg-agri-accent rounded-r-full"></div> @endif
                        
                        <i class="fa-solid {{ $menu['icon'] }} w-6 text-center transition-transform group-hover:scale-110 {{ $isActive ? 'text-agri-accent' : '' }}"></i>
                        <span>{{ $menu['label'] }}</span>
                    </a>
                @endif
            @endforeach
        </nav>
        
        {{-- Logout --}}
        <div class="p-4 border-t border-white/5 bg-black/20">
            <button onclick="confirmLogout()" class="flex items-center gap-3 px-4 py-3 w-full rounded-xl text-red-200 hover:bg-red-500/20 hover:text-white transition-all group">
                <i class="fa-solid fa-arrow-right-from-bracket group-hover:translate-x-1 transition-transform"></i>
                <span>ออกจากระบบ</span>
            </button>
        </div>
    </aside>

    {{-- MAIN CONTENT WRAPPER --}}
    <div class="flex flex-col flex-1 overflow-hidden w-full relative">
        
        {{-- Topbar --}}
        <header class="h-16 bg-white/80 backdrop-blur-md border-b border-gray-200 flex items-center justify-between px-4 lg:px-8 sticky top-0 z-30">
            <h1 class="text-lg font-bold text-agri-primary lg:hidden">
                AgriTech <span class="font-normal text-gray-500 text-sm">| @yield('header')</span>
            </h1>

            <h1 class="text-xl font-bold text-gray-800 hidden lg:block">
                @yield('header', 'Dashboard')
            </h1>
            
            <div class="flex items-center gap-4">
                <a href="{{ route('admin.all-menus') }}" class="lg:hidden w-9 h-9 flex items-center justify-center rounded-full bg-gray-100 text-gray-600 hover:bg-agri-primary hover:text-white transition">
                    <i class="fa-solid fa-table-cells-large"></i>
                </a>

                <a href="{{ route('admin.profile') }}" class="flex items-center gap-3 hover:bg-gray-50 py-1 px-2 rounded-lg transition group">
                    <div class="text-right hidden md:block">
                        <span class="block text-sm font-bold text-gray-800 group-hover:text-agri-primary transition">{{ Auth::user()->name ?? 'Admin' }}</span>
                        <span class="block text-[10px] text-gray-400 font-medium uppercase tracking-wider">Administrator</span>
                    </div>
                    <div class="relative">
                        <img class="w-9 h-9 rounded-full border border-gray-200 shadow-sm" 
                             src="https://ui-avatars.com/api/?name={{ Auth::user()->name ?? 'Admin' }}&background=1B4D3E&color=fff" alt="Profile">
                        <span class="absolute bottom-0 right-0 w-2.5 h-2.5 bg-green-500 border-2 border-white rounded-full"></span>
                    </div>
                </a>
            </div>
        </header>

        {{-- Content Area --}}
        <main class="flex-1 overflow-x-hidden overflow-y-auto p-4 lg:p-8 pb-28 lg:pb-12 scroll-smooth">
            @yield('content')
        </main>
    </div>

    {{-- MOBILE BOTTOM NAVBAR --}}
    <nav class="lg:hidden fixed bottom-0 left-0 right-0 z-50 pb-safe pointer-events-none">
        <div class="glass-nav h-[70px] shadow-[0_-5px_20px_rgba(0,0,0,0.05)] flex justify-around items-center px-2 pointer-events-auto">
            @php
                $mobileMenus = [
                    ['label' => 'หน้าแรก', 'route' => 'admin.dashboard', 'icon' => 'fa-house'],
                    ['label' => 'งาน', 'route' => 'admin.jobs.index', 'icon' => 'fa-clipboard-list'],
                    ['label' => 'รายงาน', 'route' => 'admin.reports.index', 'icon' => 'fa-chart-simple'],
                    ['label' => 'เมนู', 'route' => 'admin.all-menus', 'icon' => 'fa-table-cells-large'], 
                    ['label' => 'ฉัน', 'route' => 'admin.profile', 'icon' => 'fa-user'],
                ];
            @endphp
            
            @foreach($mobileMenus as $item)
                @if(Route::has($item['route']))
                    @php
                        $baseRoute = explode('.', $item['route']);
                        $prefix = $baseRoute[0] . '.' . $baseRoute[1];
                        $isActive = request()->routeIs($item['route']) || request()->routeIs($prefix . '*');
                    @endphp
                    <a href="{{ route($item['route']) }}" 
                       class="flex flex-col items-center justify-center w-full h-full gap-1 group relative {{ $isActive ? 'text-agri-primary' : 'text-gray-400' }}">
                        
                        @if($isActive) 
                            <div class="absolute -top-3 w-12 h-1 bg-agri-primary rounded-b-lg shadow-sm"></div>
                        @endif

                        <i class="fa-solid {{ $item['icon'] }} text-xl transition-transform duration-200 {{ $isActive ? '-translate-y-1' : 'group-hover:-translate-y-1' }}"></i>
                        <span class="text-[10px] font-medium">{{ $item['label'] }}</span>
                    </a>
                @endif
            @endforeach
        </div>
    </nav>

    <form id="logout-form" action="{{ route('logout') }}" method="POST" class="hidden">@csrf</form>

    <script>
        function confirmLogout() {
            Swal.fire({
                title: 'ออกจากระบบ?',
                text: "คุณต้องการออกจากระบบ Admin ใช่หรือไม่",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#1B4D3E',
                cancelButtonColor: '#94a3b8',
                confirmButtonText: 'ใช่, ออกจากระบบ',
                cancelButtonText: 'ยกเลิก',
                reverseButtons: true,
                customClass: { popup: 'rounded-2xl font-sans' }
            }).then((result) => {
                if (result.isConfirmed) document.getElementById('logout-form').submit();
            });
        }
    </script>

    @stack('scripts') 

</body>
</html>