@extends('layouts.admin')

@section('title', 'ตั้งค่าระบบ')
@section('header', 'Configuration')

@section('content')
<div class="max-w-5xl mx-auto pb-20">

    {{-- 🌟 Hero Header --}}
    <div class="bg-gradient-to-r from-agri-primary to-green-700 rounded-2xl p-8 text-white shadow-lg mb-8 relative overflow-hidden">
        <div class="relative z-10 flex justify-between items-center">
            <div>
                <h2 class="text-3xl font-bold mb-2">ตั้งค่าระบบ (Settings) ⚙️</h2>
                <p class="text-green-100 opacity-90">จัดการข้อมูลบริษัท, การเงิน และการแจ้งเตือนต่างๆ</p>
            </div>
            {{-- Decoration Icon --}}
            <div class="hidden md:block bg-white/20 p-4 rounded-2xl backdrop-blur-sm">
                <i class="fa-solid fa-sliders text-4xl text-white"></i>
            </div>
        </div>
        {{-- Background Shapes --}}
        <div class="absolute top-0 right-0 w-64 h-64 bg-white/5 rounded-full -mr-20 -mt-20 blur-2xl"></div>
        <div class="absolute bottom-0 left-0 w-32 h-32 bg-white/10 rounded-full -ml-10 -mb-10 blur-xl"></div>
    </div>

    {{-- ✅ Flash Message --}}
    @if(session('success'))
        <div x-data="{ show: true }" x-show="show" class="bg-green-50 border-l-4 border-green-500 p-4 mb-6 rounded-r-xl shadow-sm flex items-start gap-3 animate-fade-in-down">
            <div class="text-green-500 mt-0.5"><i class="fa-solid fa-circle-check text-xl"></i></div>
            <div class="flex-1">
                <h3 class="text-sm font-bold text-green-800">บันทึกสำเร็จ!</h3>
                <p class="text-sm text-green-700 mt-1">{{ session('success') }}</p>
            </div>
            <button @click="show = false" class="text-green-400 hover:text-green-600"><i class="fa-solid fa-xmark"></i></button>
        </div>
    @endif

    <form action="{{ route('admin.settings.update') }}" method="POST">
        @csrf

        {{-- 🏢 Section 1: ข้อมูลบริษัท --}}
        <div class="bg-white rounded-2xl shadow-md border border-gray-100 overflow-hidden mb-8 hover:shadow-lg transition-shadow duration-300">
            <div class="bg-gradient-to-r from-blue-50 to-white px-6 py-5 border-b border-blue-100 flex items-center gap-4">
                <div class="w-12 h-12 rounded-xl bg-blue-100 text-blue-600 flex items-center justify-center shadow-inner">
                    <i class="fa-solid fa-building text-xl"></i>
                </div>
                <div>
                    <h3 class="font-bold text-gray-800 text-lg">ข้อมูลบริษัท</h3>
                    <p class="text-xs text-gray-500">ใช้สำหรับแสดงในใบเสร็จและเอกสารต่างๆ</p>
                </div>
            </div>
            
            <div class="p-6 md:p-8 grid grid-cols-1 md:grid-cols-2 gap-x-8 gap-y-6">
                <div class="col-span-2">
                    <label class="block text-sm font-bold text-gray-700 mb-2">ชื่อกิจการ / บริษัท <span class="text-red-500">*</span></label>
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-gray-400">
                            <i class="fa-solid fa-font"></i>
                        </div>
                        <input type="text" name="company_name" value="{{ $settings['company_name'] ?? '' }}" 
                            class="w-full pl-10 border-gray-200 bg-gray-50 rounded-xl focus:bg-white focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 transition-all py-2.5" 
                            placeholder="เช่น บริษัท แอกกริเทค เซอร์วิส จำกัด">
                    </div>
                </div>

                <div>
                    <label class="block text-sm font-bold text-gray-700 mb-2">เบอร์โทรศัพท์ติดต่อ</label>
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-gray-400">
                            <i class="fa-solid fa-phone"></i>
                        </div>
                        <input type="text" name="company_phone" value="{{ $settings['company_phone'] ?? '' }}" 
                            class="w-full pl-10 border-gray-200 bg-gray-50 rounded-xl focus:bg-white focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 transition-all py-2.5"
                            placeholder="0xx-xxx-xxxx">
                    </div>
                </div>

                <div>
                    <label class="block text-sm font-bold text-gray-700 mb-2">เลขประจำตัวผู้เสียภาษี</label>
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-gray-400">
                            <i class="fa-solid fa-id-card"></i>
                        </div>
                        <input type="text" name="company_tax_id" value="{{ $settings['company_tax_id'] ?? '' }}" 
                            class="w-full pl-10 border-gray-200 bg-gray-50 rounded-xl focus:bg-white focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 transition-all py-2.5"
                            placeholder="13 หลัก">
                    </div>
                </div>

                <div class="col-span-2">
                    <label class="block text-sm font-bold text-gray-700 mb-2">ที่อยู่</label>
                    <div class="relative">
                        <div class="absolute top-3 left-3 pointer-events-none text-gray-400">
                            <i class="fa-solid fa-map-location-dot"></i>
                        </div>
                        <textarea name="company_address" rows="3" 
                            class="w-full pl-10 border-gray-200 bg-gray-50 rounded-xl focus:bg-white focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 transition-all py-2.5"
                            placeholder="ที่อยู่บริษัท...">{{ $settings['company_address'] ?? '' }}</textarea>
                    </div>
                </div>
            </div>
        </div>

        {{-- 💰 Section 2: การเงิน --}}
        <div class="bg-white rounded-2xl shadow-md border border-gray-100 overflow-hidden mb-8 hover:shadow-lg transition-shadow duration-300">
            <div class="bg-gradient-to-r from-green-50 to-white px-6 py-5 border-b border-green-100 flex items-center gap-4">
                <div class="w-12 h-12 rounded-xl bg-green-100 text-green-600 flex items-center justify-center shadow-inner">
                    <i class="fa-solid fa-wallet text-xl"></i>
                </div>
                <div>
                    <h3 class="font-bold text-gray-800 text-lg">การเงิน & การจอง</h3>
                    <p class="text-xs text-gray-500">ตั้งค่าบัญชีรับเงินและมัดจำ</p>
                </div>
            </div>

            <div class="p-6 md:p-8 space-y-8">
                {{-- แถวบน: PromptPay & Deposit --}}
                <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                    <div class="bg-gray-50 p-5 rounded-2xl border border-gray-200 relative group hover:border-green-300 transition-colors">
                        <div class="absolute -top-3 left-4 bg-green-600 text-white text-[10px] px-2 py-0.5 rounded font-bold shadow-sm">
                            แนะนำ
                        </div>
                        <label class="block text-sm font-bold text-gray-700 mb-2">เลข PromptPay (สร้าง QR Code)</label>
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-green-600">
                                <i class="fa-solid fa-qrcode text-lg"></i>
                            </div>
                            <input type="text" name="promptpay_number" value="{{ $settings['promptpay_number'] ?? '' }}" 
                                class="w-full pl-10 border-gray-300 rounded-xl focus:ring-2 focus:ring-green-500/50 focus:border-green-500 text-lg font-mono text-gray-800 tracking-wide py-2.5"
                                placeholder="เบอร์โทร / เลขบัตร ปชช.">
                        </div>
                        <p class="text-xs text-gray-500 mt-2 flex items-center gap-1">
                            <i class="fa-solid fa-circle-info text-green-500"></i> ระบบจะนำเลขนี้ไปสร้าง QR Code รับเงินอัตโนมัติ
                        </p>
                    </div>

                    <div class="p-5">
                        <label class="block text-sm font-bold text-gray-700 mb-2">เปอร์เซ็นต์มัดจำ (%)</label>
                        <div class="relative max-w-[150px]">
                            <input type="number" name="deposit_percentage" value="{{ $settings['deposit_percentage'] ?? '30' }}" 
                                class="w-full border-gray-300 rounded-xl focus:ring-2 focus:ring-green-500/50 focus:border-green-500 text-center font-bold text-2xl text-green-700 py-2.5"
                                min="0" max="100">
                            <span class="absolute right-4 top-1/2 -translate-y-1/2 text-gray-400 font-bold">%</span>
                        </div>
                        <p class="text-xs text-gray-500 mt-2">ยอดที่ลูกค้าต้องจ่ายก่อนเริ่มงาน</p>
                    </div>
                </div>

                {{-- ส่วนบัญชีธนาคาร --}}
                <div class="border-t border-dashed border-gray-200 pt-6">
                    <h4 class="font-bold text-sm text-gray-500 mb-4 uppercase tracking-wider flex items-center gap-2">
                        <i class="fa-solid fa-building-columns"></i> ข้อมูลบัญชีธนาคาร (ทางเลือก)
                    </h4>
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                        <div>
                            <label class="block text-xs font-bold text-gray-600 mb-1">ชื่อธนาคาร</label>
                            <div class="relative">
                                <i class="fa-solid fa-bank absolute left-3 top-3 text-gray-400 text-xs"></i>
                                <input type="text" name="bank_name" value="{{ $settings['bank_name'] ?? '' }}" class="w-full pl-8 text-sm border-gray-200 bg-gray-50 rounded-lg focus:bg-white focus:ring-green-500 focus:border-green-500 py-2" placeholder="เช่น กสิกรไทย">
                            </div>
                        </div>
                        <div>
                            <label class="block text-xs font-bold text-gray-600 mb-1">เลขบัญชี</label>
                            <div class="relative">
                                <i class="fa-solid fa-list-ol absolute left-3 top-3 text-gray-400 text-xs"></i>
                                <input type="text" name="bank_account_number" value="{{ $settings['bank_account_number'] ?? '' }}" class="w-full pl-8 text-sm border-gray-200 bg-gray-50 rounded-lg focus:bg-white focus:ring-green-500 focus:border-green-500 py-2 font-mono" placeholder="xxx-x-xxxxx-x">
                            </div>
                        </div>
                        <div>
                            <label class="block text-xs font-bold text-gray-600 mb-1">ชื่อบัญชี</label>
                            <div class="relative">
                                <i class="fa-solid fa-user-tag absolute left-3 top-3 text-gray-400 text-xs"></i>
                                <input type="text" name="bank_account_name" value="{{ $settings['bank_account_name'] ?? '' }}" class="w-full pl-8 text-sm border-gray-200 bg-gray-50 rounded-lg focus:bg-white focus:ring-green-500 focus:border-green-500 py-2" placeholder="ชื่อบัญชี...">
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- 🔔 Section 3: LINE Notify --}}
        <div class="bg-white rounded-2xl shadow-md border border-gray-100 overflow-hidden mb-12 hover:shadow-lg transition-shadow duration-300">
            <div class="bg-gradient-to-r from-[#06C755]/10 to-white px-6 py-5 border-b border-[#06C755]/20 flex items-center gap-4">
                <div class="w-12 h-12 rounded-xl bg-[#06C755] text-white flex items-center justify-center shadow-md">
                    <i class="fa-brands fa-line text-3xl"></i>
                </div>
                <div>
                    <h3 class="font-bold text-gray-800 text-lg">LINE Notify Token</h3>
                    <p class="text-xs text-gray-500">ตั้งค่าการแจ้งเตือนไปยังกลุ่มไลน์</p>
                </div>
            </div>

            <div class="p-6 md:p-8 space-y-6">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    {{-- Admin Token --}}
                    <div>
                        <label class="block text-sm font-bold text-gray-700 mb-2 flex justify-between">
                            <span>Token สำหรับ Admin</span>
                            <span class="text-[10px] bg-red-100 text-red-600 px-2 py-0.5 rounded-full">แจ้งเตือนงานใหม่/สลิป</span>
                        </label>
                        <div class="relative group">
                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-[#06C755]">
                                <i class="fa-solid fa-key"></i>
                            </div>
                            <input type="password" name="line_token_admin" value="{{ $settings['line_token_admin'] ?? '' }}" 
                                class="w-full pl-10 border-gray-200 bg-gray-50 rounded-xl focus:bg-white focus:ring-2 focus:ring-[#06C755]/30 focus:border-[#06C755] font-mono text-sm py-3 transition-all" 
                                placeholder="วาง Token ที่นี่...">
                        </div>
                    </div>

                    {{-- Staff Token --}}
                    <div>
                        <label class="block text-sm font-bold text-gray-700 mb-2 flex justify-between">
                            <span>Token สำหรับกลุ่มช่าง</span>
                            <span class="text-[10px] bg-blue-100 text-blue-600 px-2 py-0.5 rounded-full">แจ้งเตือนมอบหมายงาน</span>
                        </label>
                        <div class="relative group">
                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-blue-500">
                                <i class="fa-solid fa-users-gear"></i>
                            </div>
                            <input type="password" name="line_token_staff" value="{{ $settings['line_token_staff'] ?? '' }}" 
                                class="w-full pl-10 border-gray-200 bg-gray-50 rounded-xl focus:bg-white focus:ring-2 focus:ring-[#06C755]/30 focus:border-[#06C755] font-mono text-sm py-3 transition-all" 
                                placeholder="วาง Token ที่นี่...">
                        </div>
                    </div>
                </div>

                {{-- Guide Box --}}
                <div class="bg-[#06C755]/5 border border-[#06C755]/20 rounded-xl p-4 flex gap-3 items-start">
                    <i class="fa-solid fa-circle-question text-[#06C755] mt-1"></i>
                    <div class="text-sm text-gray-600">
                        <p class="font-bold mb-1">วิธีกาขอ Token:</p>
                        <ol class="list-decimal list-inside space-y-1 text-xs">
                            <li>ไปที่ <a href="https://notify-bot.line.me/my/" target="_blank" class="text-[#06C755] font-bold hover:underline">https://notify-bot.line.me/my/</a> แล้วเข้าสู่ระบบ</li>
                            <li>เลื่อนลงมาด้านล่าง กดปุ่ม <strong>"ออก Token" (Generate Token)</strong></li>
                            <li>ใส่ชื่อบอท (เช่น AgriTech Admin) และเลือกกลุ่มไลน์ที่ต้องการให้แจ้งเตือน</li>
                            <li>กด "ออก Token" แล้วคัดลอกรหัสมาวางในช่องด้านบน</li>
                        </ol>
                    </div>
                </div>
            </div>
        </div>

        {{-- 💾 Floating Save Button --}}
        <div class="fixed bottom-6 right-6 md:bottom-10 md:right-10 z-30">
            <button type="submit" class="bg-gradient-to-r from-agri-primary to-green-700 text-white font-bold py-4 px-8 rounded-full shadow-2xl hover:scale-105 hover:shadow-green-500/30 transition-all flex items-center gap-3 border-4 border-white/20 backdrop-blur-md">
                <i class="fa-solid fa-save text-xl"></i> 
                <span class="text-lg">บันทึกการตั้งค่า</span>
            </button>
        </div>

    </form>
</div>
@endsection