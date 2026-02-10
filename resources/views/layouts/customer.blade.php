<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Customer Dashboard - AgriTech Management</title>
    
    <link href="https://fonts.googleapis.com/css2?family=Sarabun:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <script src="https://cdn.tailwindcss.com"></script>
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    <script>
        tailwind.config = {
            theme: {
                extend: {
                    fontFamily: { sans: ['Sarabun', 'sans-serif'] },
                    colors: {
                        agri: { 
                            primary: '#1B4D3E', 
                            secondary: '#163E31',
                            accent: '#84CC16', 
                            bg: '#F3F4F6' 
                        }
                    }
                }
            }
        }
    </script>
</head>
<body class="bg-agri-bg font-sans text-gray-800 antialiased min-h-screen flex flex-col">

    {{-- Navbar --}}
    <nav class="bg-agri-primary text-white shadow-lg sticky top-0 z-50">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between h-16">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-full bg-white/10 flex items-center justify-center">
                        <i class="fa-solid fa-tractor text-agri-accent text-xl"></i>
                    </div>
                    <span class="font-bold text-xl tracking-wide">AgriTech<span class="text-agri-accent">Pro</span></span>
                </div>
                
                <div class="flex items-center gap-4">
                    <div class="hidden md:block text-right">
                        <div class="text-sm font-medium">{{ Auth::guard('customer')->user()->name }}</div>
                        <div class="text-xs text-agri-accent">{{ Auth::guard('customer')->user()->phone }}</div>
                    </div>
                    <form action="{{ route('customer.logout') }}" method="POST">
                        @csrf
                        <button type="submit" class="bg-red-500 hover:bg-red-600 text-white px-4 py-2 rounded-lg text-sm transition shadow-md flex items-center gap-2">
                            <i class="fa-solid fa-right-from-bracket"></i> <span class="hidden md:inline">ออกจากระบบ</span>
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
        <div class="max-w-7xl mx-auto px-4 text-center text-gray-500 text-sm">
            &copy; {{ date('Y') }} AgriTech Management System. All rights reserved.
        </div>
    </footer>

</body>
</html>