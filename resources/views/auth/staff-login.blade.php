<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>เข้าสู่ระบบพนักงาน - Agri-Equip</title>
    {{-- Tailwind CSS --}}
    <script src="https://cdn.tailwindcss.com"></script>
    {{-- FontAwesome --}}
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    {{-- Google Fonts --}}
    <link href="https://fonts.googleapis.com/css2?family=Sarabun:wght@300;400;600&display=swap" rel="stylesheet">
    {{-- SweetAlert2 (สำหรับ Alert สวยๆ) --}}
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    
    <style> 
        body { font-family: 'Sarabun', sans-serif; } 
        .bounce-in { animation: bounceIn 0.5s; }
        @keyframes bounceIn {
            0% { transform: scale(0.3); opacity: 0; }
            50% { transform: scale(1.05); opacity: 1; }
            70% { transform: scale(0.9); }
            100% { transform: scale(1); }
        }
    </style>
</head>
<body class="bg-slate-100 min-h-screen flex items-center justify-center p-4">

    <div class="max-w-md w-full bg-white rounded-2xl shadow-xl overflow-hidden">
        
        {{-- Header --}}
        <div class="bg-green-600 p-6 text-center text-white">
            <h1 class="text-2xl font-bold mb-1"><i class="fa-solid fa-tractor"></i> Agri-Equip</h1>
            <p class="text-green-100 text-sm">ระบบบันทึกงานสำหรับพนักงาน</p>
        </div>

        {{-- Staff List --}}
        <div class="p-6">
            <p class="text-gray-500 text-center mb-6">เลือกบัญชีผู้ใช้งานของคุณ</p>
            
            <div class="grid grid-cols-2 gap-4">
                @foreach($staffs as $staff)
                <button onclick="openPinModal('{{ $staff->id }}', '{{ $staff->name }}')" 
                        class="flex flex-col items-center justify-center p-4 rounded-xl border-2 border-slate-100 hover:border-green-500 hover:bg-green-50 transition group focus:outline-none focus:ring-2 focus:ring-green-500">
                    <div class="w-16 h-16 rounded-full bg-slate-200 mb-3 flex items-center justify-center text-2xl text-slate-500 group-hover:bg-green-100 group-hover:text-green-600">
                        <i class="fa-solid fa-user"></i>
                    </div>
                    <span class="font-bold text-gray-700 group-hover:text-green-700">{{ $staff->name }}</span>
                    <span class="text-xs text-gray-400">แตะเพื่อเข้าสู่ระบบ</span>
                </button>
                @endforeach
            </div>
        </div>
        
        <div class="bg-gray-50 p-4 text-center border-t border-gray-100">
            <a href="{{ url('/login') }}" class="text-sm text-gray-500 hover:text-green-600">
                สำหรับผู้ดูแลระบบ (Admin Login)
            </a>
        </div>
    </div>

    {{-- PIN Modal --}}
    <div id="pinModal" class="fixed inset-0 bg-black/60 hidden items-center justify-center z-50 backdrop-blur-sm transition-opacity">
        <div class="bg-white rounded-2xl w-full max-w-sm mx-4 p-6 shadow-2xl bounce-in relative">
            
            {{-- Close Button --}}
            <button onclick="closeModal()" class="absolute top-4 right-4 text-gray-400 hover:text-gray-600">
                <i class="fa-solid fa-xmark text-xl"></i>
            </button>

            <div class="text-center mb-6">
                <h3 class="text-lg font-bold text-gray-800">สวัสดี, <span id="staffName" class="text-green-600">...</span></h3>
                <p class="text-gray-500 text-sm">กรุณาระบุรหัส PIN 4 หลัก</p>
            </div>

            <form action="{{ route('staff.login.submit') }}" method="POST" id="pinForm">
                @csrf
                <input type="hidden" name="user_id" id="userIdInput">
                <input type="hidden" name="pin" id="pinInput">

                {{-- PIN Display Dots (ปรับเหลือ 4 จุด) --}}
                <div class="flex justify-center gap-4 mb-8">
                    @for($i=1; $i<=4; $i++)
                        <div class="w-5 h-5 rounded-full bg-gray-200 pin-dot transition-all duration-200 transform border border-gray-300" id="dot-{{$i}}"></div>
                    @endfor
                </div>

                {{-- Numpad --}}
                <div class="grid grid-cols-3 gap-4 px-4">
                    @foreach([1,2,3,4,5,6,7,8,9] as $num)
                        <button type="button" onclick="addPin({{$num}})" class="h-16 rounded-full bg-gray-50 text-2xl font-bold text-gray-700 hover:bg-gray-200 active:bg-green-100 active:text-green-600 transition shadow-sm border border-gray-200">
                            {{ $num }}
                        </button>
                    @endforeach
                    
                    {{-- ปุ่มยกเลิก/Clear --}}
                    <button type="button" onclick="clearPin()" class="h-16 rounded-full text-yellow-600 hover:bg-yellow-50 font-bold text-sm flex flex-col items-center justify-center">
                        ล้าง
                    </button>
                    
                    <button type="button" onclick="addPin(0)" class="h-16 rounded-full bg-gray-50 text-2xl font-bold text-gray-700 hover:bg-gray-200 shadow-sm border border-gray-200">0</button>
                    
                    {{-- ปุ่มลบทีละตัว --}}
                    <button type="button" onclick="deletePin()" class="h-16 rounded-full text-gray-500 hover:bg-red-50 hover:text-red-500 flex items-center justify-center">
                        <i class="fa-solid fa-delete-left text-2xl"></i>
                    </button>
                </div>
            </form>
        </div>
    </div>

    {{-- Script Section --}}
    <script>
        let currentPin = '';
        const PIN_LENGTH = 4; // ✅ ปรับเป็น 4 หลัก

        // 1. ตรวจสอบ Error จาก Session (กรณี PIN ผิด) แล้วแสดง Alert
        @if(session('error'))
            Swal.fire({
                icon: 'error',
                title: 'รหัส PIN ไม่ถูกต้อง',
                text: 'กรุณาลองใหม่อีกครั้ง',
                confirmButtonText: 'ตกลง',
                confirmButtonColor: '#d33',
                timer: 3000
            });
        @endif

        function openPinModal(id, name) {
            document.getElementById('userIdInput').value = id;
            document.getElementById('staffName').innerText = name;
            document.getElementById('pinModal').classList.remove('hidden');
            document.getElementById('pinModal').classList.add('flex');
            clearPin();
        }

        function closeModal() {
            document.getElementById('pinModal').classList.add('hidden');
            document.getElementById('pinModal').classList.remove('flex');
            clearPin();
        }

        function addPin(num) {
            // เช็คว่ายังไม่ครบตามจำนวน (ถ้าครบแล้วจะไม่ให้กดเพิ่ม)
            if (currentPin.length < PIN_LENGTH) {
                currentPin += num;
                updateDots();

                // ถ้าครบ 4 หลัก ให้ส่งข้อมูลทันที
                if (currentPin.length === PIN_LENGTH) {
                    submitLogin();
                }
            }
        }

        function updateDots() {
            document.getElementById('pinInput').value = currentPin;
            for (let i = 1; i <= PIN_LENGTH; i++) {
                const dot = document.getElementById(`dot-${i}`);
                if (i <= currentPin.length) {
                    dot.classList.remove('bg-gray-200', 'border-gray-300');
                    dot.classList.add('bg-green-500', 'border-green-600', 'scale-125'); 
                } else {
                    dot.classList.add('bg-gray-200', 'border-gray-300');
                    dot.classList.remove('bg-green-500', 'border-green-600', 'scale-125');
                }
            }
        }

        function deletePin() {
            currentPin = currentPin.slice(0, -1);
            updateDots();
        }

        function clearPin() {
            currentPin = '';
            updateDots();
        }

        function submitLogin() {
            // แสดง Alert ว่ากำลังตรวจสอบ
            Swal.fire({
                title: 'กำลังตรวจสอบ...',
                text: 'กรุณารอสักครู่',
                allowOutsideClick: false,
                didOpen: () => {
                    Swal.showLoading();
                }
            });

            // หน่วงเวลานิดนึงเพื่อให้เห็นว่ากดครบแล้ว (UX)
            setTimeout(() => {
                document.getElementById('pinForm').submit();
            }, 300);
        }
    </script>
</body>
</html>