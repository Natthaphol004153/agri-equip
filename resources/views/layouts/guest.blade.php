<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="Content-Security-Policy" content="upgrade-insecure-requests">
    <title>เข้าสู่ระบบ - มนตรีการเกษตร</title>
    
    <link href="https://fonts.googleapis.com/css2?family=Sarabun:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    
    <script src="https://cdn.tailwindcss.com"></script>
    
    {{-- ✅ เติมบรรทัดนี้ครับ (Alpine.js) --}}
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    <script>
        tailwind.config = {
            theme: {
                extend: {
                    fontFamily: { sans: ['Sarabun', 'sans-serif'] },
                    colors: {
                        agri: { primary: '#1B4D3E', accent: '#84CC16', bg: '#F3F4F6' }
                    }
                }
            }
        }
    </script>
</head>
<body class="bg-agri-bg font-sans text-gray-800 antialiased h-screen flex items-center justify-center">
    <div class="w-full max-w-md bg-white rounded-2xl shadow-xl overflow-hidden p-8 mx-4">
        <div class="text-center mb-8">
            <div class="inline-flex items-center justify-center w-16 h-16 rounded-full bg-agri-primary/10 mb-4">
                <i class="fa-solid fa-tractor text-3xl text-agri-primary"></i>
            </div>
            <h2 class="text-2xl font-bold text-agri-primary">มนตรี<span class="text-agri-accent">การเกษตร</span></h2>
            <p class="text-gray-500 text-sm mt-1">ระบบบริหารจัดการเครื่องจักรและงานบริการ</p>
        </div>
        @yield('content')
    </div>
</body>
</html>