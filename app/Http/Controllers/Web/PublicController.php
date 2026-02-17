<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Equipment;
use App\Models\Booking;
use App\Models\Setting;
use Carbon\Carbon;

class PublicController extends Controller
{
    // หน้าแรก (Landing Page)
    public function index()
    {
        // ❌ แก้จาก 'status' เป็น 'current_status'
        $equipments = Equipment::where('current_status', 'available')->get();

        $banners = json_decode(Setting::where('key', 'home_banners')->value('value'), true);
        return view('public.home', compact('equipments','banners'));
    }

    // API สำหรับดึงข้อมูลปฏิทิน (ส่งกลับเป็น JSON)
    public function getCalendarEvents(Request $request)
    {
        $start = $request->query('start');
        $end = $request->query('end');

        // ดึงการจองในช่วงเวลานั้น (ไม่เอาที่ยกเลิก)
        $bookings = Booking::where(function ($q) use ($start, $end) {
            $q->whereBetween('scheduled_start', [$start, $end])
                ->orWhereBetween('scheduled_end', [$start, $end]);
        })
            ->where('scheduled_end', '>=', now()) // <--- เพิ่มบรรทัดนี้: ถ้าจบไปแล้ว (เป็นอดีต) ไม่ต้องเอามาโชว์
            ->whereIn('status', ['scheduled', 'in_progress', 'completed_pending_approval', 'completed'])
            ->with('equipment')
            ->get();

        // แปลงข้อมูลเพื่อส่งให้ FullCalendar (ปกปิดชื่อลูกค้า)
        $events = $bookings->map(function ($booking) {
            return [
                'title' => "❌ ไม่ว่าง: " . ($booking->equipment->name ?? 'เครื่องจักร'),
                'start' => $booking->scheduled_start,
                'end' => $booking->scheduled_end,
                'color' => '#ef4444', // สีแดง
                'textColor' => '#ffffff',
                'allDay' => false,
            ];
        });

        return response()->json($events);
    }
}