<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\BookingService;
use App\Models\Booking;
use App\Models\Equipment; // ✅ เพิ่ม Import Model เครื่องจักร
use Exception;
use Carbon\Carbon;

class BookingController extends Controller
{
    protected $bookingService;

    public function __construct(BookingService $bookingService)
    {
        $this->bookingService = $bookingService;
    }

    public function store(Request $request)
    {
        // Validate ข้อมูล
        $validated = $request->validate([
            'customer_id' => 'required|exists:customers,id',
            'equipment_id' => 'required|exists:equipment,id',
            'assigned_staff_id' => 'nullable|exists:users,id', 
            'scheduled_start' => 'required|date|after:now',
            'scheduled_end' => 'required|date|after:scheduled_start|before_or_equal:' . Carbon::parse($request->scheduled_start)->addDays(30)->format('Y-m-d H:i:s'),
            
            // ✅ เพิ่ม Validation สำหรับพื้นที่ไร่
            'estimated_area' => 'nullable|numeric|min:0', // พื้นที่ประเมิน (อาจจะ null ได้ถ้าไม่ได้ดึงจากโปรไฟล์ลูกค้ามา)
            'actual_area' => 'required|numeric|min:0.1',  // พื้นที่ทำจริง (บังคับกรอก ต้องมากกว่า 0)
            
            'payment_method' => 'nullable|string'
            // ❌ เอา 'total_price' ออกจากการ Validate รับค่าตรงๆ เพราะเราจะคำนวณเองด้านล่าง
        ]);

        try {
            // ✅ 1. ดึงข้อมูลเครื่องจักร เพื่อดู "ราคาต่อไร่" ปัจจุบัน
            $equipment = Equipment::findOrFail($validated['equipment_id']);
            
            // (ถ้าเครื่องจักรตัวไหนไม่มีราคาต่อไร่ ให้ default เป็น 0 ไว้ก่อน)
            $price_per_rai = $equipment->price_per_rai ?? 0;

            // ✅ 2. คำนวณราคารวม (Total Price) ฝั่ง Backend ป้องกันการ Hack จากหน้าบ้าน
            $total_price = $validated['actual_area'] * $price_per_rai;

            // ✅ 3. แนบค่าที่ต้องใช้เพิ่มเติม (Snapshot ราคา และ ยอดรวม) ก่อนส่งเข้า Service
            $validated['price_per_rai_at_booking'] = $price_per_rai;
            $validated['total_price'] = $total_price;

            // ✅ 4. ส่งข้อมูลทั้งหมดให้ Service จัดการต่อ
            $booking = $this->bookingService->createBooking($validated);
            
            return response()->json([
                'message' => 'จองงานและมอบหมายพนักงานสำเร็จ',
                'data' => $booking
            ], 201);

        } catch (Exception $e) {
            return response()->json([
                'message' => 'จองไม่สำเร็จ',
                'error' => $e->getMessage()
            ], 422); 
        }
    }
}