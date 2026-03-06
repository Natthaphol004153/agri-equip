<?php

namespace App\Http\Controllers\Web\Customer;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str; // ✅ เพิ่ม: สำหรับสร้างเลขใบงาน
use Carbon\Carbon; // ✅ เพิ่ม: สำหรับจัดการวันเวลา
use App\Models\Booking;
use App\Models\Equipment; // ✅ เพิ่ม: เรียกใช้ Model Equipment
use App\Models\Setting;
use App\Services\PromptPayService;

class DashboardController extends Controller
{
    // ----------------------------------------------------------------------
    // 🟢 ส่วนแสดงผลทั่วไป (Dashboard & Detail)
    // ----------------------------------------------------------------------

    // แสดงรายการจอง (หน้าแรก Dashboard)
    public function index()
    {
        $customerId = Auth::guard('customer')->id();

        // ดึงรายการจองล่าสุด
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

    // ----------------------------------------------------------------------
    // 🟢 ส่วนการจองงานใหม่ (Booking System) - ✅ เพิ่มใหม่
    // ----------------------------------------------------------------------

    // 1. แสดงหน้าฟอร์มจอง
    public function create(Request $request)
    {
        // ดึงเครื่องจักรที่สถานะพร้อมใช้งาน (Available)
        $equipments = Equipment::where('current_status', 'available')->get();

        // รับค่าวันที่ที่ส่งมาจากหน้าปฏิทิน (ถ้ามี)
        $selectedDate = $request->query('date');

        return view('customer.booking.create', compact('equipments', 'selectedDate'));
    }

    // 2. บันทึกข้อมูลการจอง
    public function store(Request $request)
    {
        $request->merge([
            'start_date' => $this->normalizeBuddhistDateString($request->input('start_date')),
        ]);

        $request->validate([
            'equipment_id' => 'required|exists:equipment,id',
            'start_date' => 'required|date|after_or_equal:today',
            'start_time' => 'required',
            'end_time' => 'required|after:start_time',
            'note' => 'nullable|string|max:500',
        ]);

        $equipment = Equipment::findOrFail($request->equipment_id);

        $start = Carbon::parse($request->start_date . ' ' . $request->start_time)->format('Y-m-d H:i:s');
        $end = Carbon::parse($request->start_date . ' ' . $request->end_time)->format('Y-m-d H:i:s');

        // ✅ เรียกใช้ BookingService แทนการเช็คเอง เพื่อดัก Double Booking จากจุดเดียว
        $bookingService = app(\App\Services\BookingService::class);
        
        try {
             $bookingService->createBooking([
                 'customer_id' => Auth::guard('customer')->id(),
                 'equipment_id' => $equipment->id,
                 'scheduled_start' => $start,
                 'scheduled_end' => $end,
                 'status' => 'pending_approval',        
                 'payment_status' => 'pending',
                 'actual_area' => 0,           // ลูกค้ายังไม่ทราบพื้นที่เป๊ะๆ ใส่ 0 ไปก่อนให้แอดมินประเมิน
                 'estimated_area' => 0,
                 'deposit_amount' => 0,        
                 'note' => $request->note,
                 // Service จะไปคำนวณ total_price และ job_number ให้เอง
             ]);

            // ✅ ส่งกลับไปหน้า Dashboard พร้อมข้อความแจ้งเตือน
            return redirect()->route('customer.dashboard')
                ->with('success', 'ส่งคำขอจองเรียบร้อยแล้ว! เจ้าหน้าที่จะตรวจสอบรายละเอียดและแจ้งราคาให้ทราบภายหลังครับ');
        } catch (\Exception $e) {
             return back()->withInput()->withErrors(['time_slot' => '❌ ไม่สามารถจองได้: ' . $e->getMessage()]);
        }
    }

    // ----------------------------------------------------------------------
    // 🟢 ส่วนการชำระเงิน (Payment)
    // ----------------------------------------------------------------------

    // หน้าแสดง QR Code ชำระเงิน
    public function payment($id)
    {
        $booking = Booking::where('id', $id)
            ->where('customer_id', Auth::guard('customer')->id())
            ->firstOrFail();

        // เช็คสถานะ: ถ้าจ่ายแล้ว หรือ รอตรวจสอบ ไม่ต้องจ่ายซ้ำ
        if (in_array($booking->payment_status, ['paid', 'deposit_paid', 'pending_approval'])) {
            return redirect()->route('customer.booking.show', $id)
                ->with('info', 'รายการนี้อยู่ในระหว่างตรวจสอบหรือชำระเงินเรียบร้อยแล้ว');
        }

        // ดึงเบอร์พร้อมเพย์จาก Setting (ใช้ method ตามโค้ดเดิมของคุณ)
        $promptpayNo = Setting::get('company_promptpay', '0812345678');

        // สร้าง QR Code
        $promptPayService = new PromptPayService();
        $qrPayload = $promptPayService->generatePayload($promptpayNo, $booking->total_price);
        $qrUrl = "https://chart.googleapis.com/chart?chs=300x300&cht=qr&chl=" . urlencode($qrPayload) . "&choe=UTF-8";

        return view('customer.booking.payment', compact('booking', 'qrUrl', 'promptpayNo'));
    }

    // บันทึกสลิปโอนเงิน
    public function uploadSlip(Request $request, $id)
    {
        $request->validate([
            'slip_image' => 'required|image|max:5120', // 5MB
        ]);

        $booking = Booking::where('id', $id)
            ->where('customer_id', Auth::guard('customer')->id())
            ->firstOrFail();

        if ($request->hasFile('slip_image')) {
            $path = $request->file('slip_image')->store('payment_slips', 'public');

            $booking->update([
                'payment_proof' => $path,
                'payment_status' => 'pending_approval',
                'payment_method' => 'transfer'
            ]);
        }

        return redirect()->route('customer.dashboard')
            ->with('success', 'ส่งหลักฐานการโอนเรียบร้อยแล้ว รอเจ้าหน้าที่ตรวจสอบ');
    }
    // ✅ เพิ่มฟังก์ชันนี้สำหรับเช็คคิวงาน (AJAX)
    public function apiCheckSchedule(Request $request)
    {
        $request->merge([
            'date' => $this->normalizeBuddhistDateString($request->input('date')),
        ]);

        $request->validate([
            'equipment_id' => 'required',
            'date' => 'required|date',
        ]);

        $bookings = Booking::where('equipment_id', $request->equipment_id)
            ->whereDate('scheduled_start', '<=', $request->date)
            ->whereDate('scheduled_end', '>=', $request->date)
            ->where('status', '!=', 'cancelled') // ไม่เอาที่ยกเลิก
            ->orderBy('scheduled_start')
            ->get(['scheduled_start', 'scheduled_end', 'status']); // ดึงแค่นี้พอ (เพื่อ privacy)

        // แปลงข้อมูลให้ใช้งานง่าย
        $events = $bookings->map(function ($booking) {
            return [
                'start' => \Carbon\Carbon::parse($booking->scheduled_start)->format('H:i'),
                'end' => \Carbon\Carbon::parse($booking->scheduled_end)->format('H:i'),
                'status' => $booking->status,
                // ไม่ส่งชื่อลูกค้าไป เพื่อความเป็นส่วนตัว
            ];
        });

        return response()->json($events);
    }

    private function normalizeBuddhistDateString($value)
    {
        if (blank($value)) {
            return $value;
        }

        $raw = trim((string) $value);
        if (!preg_match('/^(\d{4})-(\d{2})-(\d{2})(?:[T\s](\d{2}):(\d{2})(?::(\d{2}))?)?$/', $raw, $match)) {
            return $value;
        }

        $year = (int) $match[1];
        if ($year >= 2400) {
            $year -= 543;
        }

        $month = $match[2];
        $day = $match[3];
        $hour = $match[4] ?? null;
        $minute = $match[5] ?? null;
        $second = $match[6] ?? null;

        if ($hour === null || $minute === null) {
            return sprintf('%04d-%s-%s', $year, $month, $day);
        }

        return sprintf('%04d-%s-%s %s:%s:%s', $year, $month, $day, $hour, $minute, $second ?? '00');
    }
}