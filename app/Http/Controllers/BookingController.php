<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\BookingService;
use App\Models\Booking;
use Exception;
use Carbon\Carbon; // ✅ เพิ่ม Import Carbon

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
            'total_price' => 'required|numeric',
            'payment_method' => 'nullable|string'
        ]);

        // ✅ ลบ Logic เช็คพนักงานที่ซ้ำซ้อนออกไปแล้ว (Service จะจัดการให้เองทั้งหมด)

        try {
            // ✅ ใช้ $validated ที่ได้จากด้านบนส่งเข้าไปได้เลย
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