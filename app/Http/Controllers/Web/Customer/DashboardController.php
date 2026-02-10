<?php

namespace App\Http\Controllers\Web\Customer;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use App\Models\Booking;
use App\Models\Setting; // ✅ เรียกใช้ Model Setting
use App\Services\PromptPayService;

class DashboardController extends Controller
{
    // แสดงรายการจอง (หน้าแรก)
    public function index()
    {
        $customerId = Auth::guard('customer')->id();

        // ดึงรายการจอง
        $bookings = Booking::where('customer_id', $customerId)
                            ->with('equipment') 
                            ->latest()
                            ->get();

        return view('customer.dashboard', compact('bookings'));
    }

    // แสดงรายละเอียดงาน
    public function show($id)
    {
        $booking = Booking::where('id', $id)
            ->where('customer_id', Auth::guard('customer')->id())
            ->with(['equipment', 'assignedStaff', 'activities']) 
            ->firstOrFail();

        return view('customer.booking.show', compact('booking'));
    }

    // ✅ หน้าแสดง QR Code ชำระเงิน
    public function payment($id)
    {
        $booking = Booking::where('id', $id)
            ->where('customer_id', Auth::guard('customer')->id())
            ->firstOrFail();

        // 1. เช็คสถานะ: ถ้าจ่ายแล้ว หรือ รอตรวจสอบ ไม่ต้องจ่ายซ้ำ
        // (รวม deposit_paid และ pending_approval ด้วย)
        if (in_array($booking->payment_status, ['paid', 'deposit_paid', 'pending_approval'])) {
            return redirect()->route('customer.booking.show', $id)
                ->with('info', 'รายการนี้อยู่ในระหว่างตรวจสอบหรือชำระเงินเรียบร้อยแล้ว');
        }

        // 2. ดึงเบอร์พร้อมเพย์จาก Setting (ถ้าไม่มีให้ใช้ค่า Default 08xxxxxx)
        // คีย์ 'company_promptpay' ต้องตรงกับในฐานข้อมูลตาราง settings
        $promptpayNo = Setting::get('company_promptpay', '0812345678'); 

        // 3. สร้าง QR Code Payload
        $promptPayService = new PromptPayService();
        $qrPayload = $promptPayService->generatePayload($promptpayNo, $booking->total_price);
        
        // 4. แปลง Payload เป็น URL รูปภาพ (Google Chart API)
        $qrUrl = "https://chart.googleapis.com/chart?chs=300x300&cht=qr&chl=" . urlencode($qrPayload) . "&choe=UTF-8";

        return view('customer.booking.payment', compact('booking', 'qrUrl', 'promptpayNo'));
    }

    // ✅ บันทึกสลิปโอนเงิน
    public function uploadSlip(Request $request, $id)
    {
        $request->validate([
            'slip_image' => 'required|image|max:5120', // ✅ เพิ่มขนาดเป็น 5MB (เผื่อกล้องมือถือชัดๆ)
        ]);

        $booking = Booking::where('id', $id)
            ->where('customer_id', Auth::guard('customer')->id())
            ->firstOrFail();

        // อัปโหลดรูป
        if ($request->hasFile('slip_image')) {
            // เก็บไฟล์ในโฟลเดอร์ payment_slips (Disk: public)
            $path = $request->file('slip_image')->store('payment_slips', 'public');
            
            // อัปเดตข้อมูลใน Database
            $booking->update([
                'payment_proof' => $path,
                'payment_status' => 'pending_approval', // เปลี่ยนสถานะเป็น "รอตรวจสอบ"
                'payment_method' => 'transfer' // ระบุว่าจ่ายแบบโอน
            ]);
        }

        return redirect()->route('customer.dashboard')
            ->with('success', 'ส่งหลักฐานการโอนเรียบร้อยแล้ว รอเจ้าหน้าที่ตรวจสอบ');
    }
}