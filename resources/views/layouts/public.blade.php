<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>บริการเครื่องจักรเกษตร</title>
    <link href="https://fonts.googleapis.com/css2?family=Sarabun:wght@300;400;600;700&display=swap" rel="stylesheet">
    <script src="https://cdn.tailwindcss.com"></script>
    <script src='https://cdn.jsdelivr.net/npm/fullcalendar@6.1.10/index.global.min.js'></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.css" />

    <style>
        body { font-family: 'Sarabun', sans-serif; }
        .fc-toolbar-title { font-size: 1.1rem !important; }
        @media (max-width: 768px) { .fc-toolbar-title { font-size: 0.9rem !important; } }
        
        /* ปรับแต่งปุ่ม Pagination ของ Swiper ให้เป็นสีเขียว */
        .swiper-pagination-bullet-active { background-color: #16a34a !important; }
        .swiper-slide { height: auto; } /* แก้ปัญหาความสูง */
    </style>
</head>
<body class="bg-gray-50 text-gray-800 flex flex-col min-h-screen">
    <nav class="bg-white shadow-md sticky top-0 z-50 px-4 py-3 flex justify-between items-center">
        <div class="flex items-center gap-2 font-bold text-green-700 text-lg">
            <i class="fa-solid fa-tractor"></i> Agri-Equip
        </div>
        <div>
            @auth('customer')
                <a href="{{ route('customer.dashboard') }}" class="text-green-700 font-bold"><i class="fa-solid fa-user"></i> {{ Auth::guard('customer')->user()->name }}</a>
            @else
                <a href="{{ route('customer.login') }}" class="bg-green-600 text-white px-4 py-2 rounded-full text-sm hover:bg-green-700 transition">เข้าสู่ระบบ</a>
            @endauth
        </div>
    </nav>

    <main class="flex-grow"> 
        @yield('content')
    </main>

    <footer class="bg-gray-800 text-white text-center py-4 mt-8 text-sm">
        © {{ date('Y') }} บริการเครื่องจักรเกษตรชุมชน
    </footer>

    <script src="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.js"></script>
    @yield('scripts')
</body>
</html>