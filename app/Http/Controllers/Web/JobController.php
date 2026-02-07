<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Booking;
use App\Models\User;
use App\Models\Customer;
use App\Models\Equipment;
use App\Services\BookingService; // ✅ แก้ไข: ต้องมี s ต่อท้าย Services
use Carbon\Carbon;
use Exception; // ✅ เพิ่ม: สำหรับดักจับ Error เวลาจองไม่ได้

class JobController extends Controller
{
    // ตัวแปรสำหรับเรียกใช้ Service
    protected $bookingService;

    // ✅ Constructor: เชื่อมต่อกับ BookingService
    public function __construct(BookingService $bookingService)
    {
        $this->bookingService = $bookingService;
    }

    /*
    |--------------------------------------------------------------------------
    | 1. 📋 READ ZONE (ดูข้อมูล)
    |--------------------------------------------------------------------------
    | ส่วนของการแสดงผลรายการ, การค้นหา, และรายละเอียดงาน
    */

    /**
     * 🟢 หน้าแสดงรายการงานทั้งหมด (Dashboard / List)
     */
    public function index(Request $request)
    {
        // --- 1. รับค่า Filter ---
        $status = $request->get('status', 'all');
        $machineType = $request->get('machine_type', 'all');
        $search = $request->get('search');

        // --- 2. เริ่ม Query ข้อมูล ---
        $query = Booking::with(['customer', 'equipment', 'assignedStaff'])->latest();

        // --- 3. กรองข้อมูล (Filter) ---
        if ($status !== 'all') {
            $query->where('status', $status);
        }

        if ($machineType !== 'all') {
            $query->whereHas('equipment', function ($q) use ($machineType) {
                $q->where('type', $machineType);
            });
        }

        // --- 4. ค้นหา (Search) ---
        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->whereHas('customer', function ($sub) use ($search) {
                    $sub->where('name', 'like', "%$search%");
                })->orWhere('job_number', 'like', "%$search%");
            });
        }

        // --- 5. แบ่งหน้า (Pagination) ---
        $jobs = $query->paginate(10)->withQueryString();

        // --- 6. เตรียมข้อมูล Staff สำหรับ Modal ---
        $staffs = User::where('role', 'staff')->where('is_active', true)->get();

        // กรณีเป็น AJAX (เช่น กดเปลี่ยนหน้า) ส่งกลับเฉพาะตาราง
        if ($request->ajax()) {
            return view('admin.jobs.table', compact('jobs'))->render();
        }

        return view('admin.jobs.index', compact('jobs', 'staffs'));
    }

    /**
     * 🟢 หน้าแสดงรายละเอียดงานรายตัว (Show Detail)
     */
    public function show($id)
    {
        $job = Booking::with(['customer', 'equipment', 'assignedStaff'])->findOrFail($id);
        return view('admin.jobs.show', compact('job'));
    }

    /*
    |--------------------------------------------------------------------------
    | 2. 📝 CREATE & EDIT ZONE (เพิ่ม/แก้ไข)
    |--------------------------------------------------------------------------
    | ส่วนของการสร้างงานใหม่ และแก้ไขข้อมูลงานเดิม
    */

    /**
     * 🟢 แสดงฟอร์มสร้างงานใหม่
     */
    public function create()
    {
        $customers = Customer::all();
        // ดึงเฉพาะรถที่ว่าง หรือ กำลังใช้งาน (ไม่เอารถซ่อม)
        $equipments = Equipment::where('current_status', '!=', 'maintenance')->get();
        $staffs = User::where('role', 'staff')->where('is_active', true)->get();

        return view('admin.jobs.create', compact('customers', 'equipments', 'staffs'));
    }

    /**
     * 🟢 บันทึกงานใหม่ (Store) - 🔥 แก้ไขให้ใช้ Service เช็คคิว
     */
    public function store(Request $request)
    {
        $request->validate([
            'customer_id' => 'required|exists:customers,id',
            'equipment_id' => 'required|exists:equipment,id',
            'assigned_staff_id' => 'nullable|exists:users,id', // แก้เป็น nullable เผื่อยังไม่ระบุคน
            'scheduled_start' => 'required|date',
            'scheduled_end' => 'required|date|after:scheduled_start',
            'total_price' => 'required|numeric|min:0',
            'deposit_amount' => 'nullable|numeric|min:0',
            'payment_proof' => 'nullable|image|max:5120',
            'payment_method' => 'nullable|in:transfer,cash', // ✅ รับค่านี้เพิ่ม
        ]);

        try {
            // เตรียมข้อมูลส่งให้ Service
            $data = $request->only([
                'customer_id',
                'equipment_id',
                'assigned_staff_id',
                'scheduled_start',
                'scheduled_end',
                'total_price',
                'deposit_amount',
                'payment_method'
            ]);

            // กำหนดสถานะการจ่ายเงิน
            $data['payment_status'] = ($request->deposit_amount > 0) ? 'deposit_paid' : 'pending';

            // ✅ เพิ่ม: อัปโหลดรูปสลิป (ถ้ามี)
            if ($request->hasFile('payment_proof')) {
                $data['payment_proof'] = $request->file('payment_proof')->store('payments', 'public');
            }
            // ✅ เรียกใช้ Service (ระบบจะเช็คคิวซ้อนและสถานะรถให้เองที่นี่)
            $this->bookingService->createBooking($data);

            return redirect()->route('admin.jobs.index')->with('success', 'สร้างงานใหม่สำเร็จ!');

        } catch (Exception $e) {
            // ❌ ถ้าจองไม่ได้ (คิวเต็ม/รถเสีย) ให้เด้งกลับพร้อมแจ้ง Error
            return back()->with('error', 'ไม่สามารถจองได้: ' . $e->getMessage())->withInput();
        }
    }

    /**
     * 🟢 แสดงฟอร์มแก้ไขงาน
     */
    public function edit($id)
    {
        $job = Booking::findOrFail($id);
        $customers = Customer::all();
        $equipments = Equipment::all();
        $staffs = User::where('role', 'staff')->get();

        return view('admin.jobs.edit', compact('job', 'customers', 'equipments', 'staffs'));
    }

    /**
     * 🟢 อัปเดตข้อมูลงาน (Update)
     */
    public function update(Request $request, $id)
    {
        $job = Booking::findOrFail($id);

        // กรณี 1: แก้ไขแค่คนขับ (Quick Assign Modal)
        if ($request->ajax() && $request->has('assigned_staff_id')) {
            $job->update(['assigned_staff_id' => $request->assigned_staff_id]);
            return response()->json(['success' => true, 'message' => 'มอบหมายงานสำเร็จ']);
        }

        // กรณี 2: แก้ไขข้อมูลทั่วไป (Full Edit Form)
        $validated = $request->validate([
            'customer_id' => 'required',
            'equipment_id' => 'required',
            'assigned_staff_id' => 'required',
            'scheduled_start' => 'required|date',
            'scheduled_end' => 'required|date',
            'total_price' => 'required|numeric',
        ]);

        $job->update($validated);

        return redirect()->route('admin.jobs.index')->with('success', 'อัปเดตข้อมูลสำเร็จ');
    }

    /*
    |--------------------------------------------------------------------------
    | 3. ⚙️ ACTION ZONE (ดำเนินการ)
    |--------------------------------------------------------------------------
    | ส่วนของการอนุมัติ, ยกเลิก, เปลี่ยนสถานะต่างๆ
    */

    /**
     * 🟢 หน้าตรวจสอบงานก่อนอนุมัติ (Review)
     */
    public function review($id)
    {
        $job = Booking::with(['customer', 'equipment', 'assignedStaff'])->findOrFail($id);
        return view('admin.jobs.review', compact('job'));
    }

    /**
     * 🟢 อนุมัติงานและปิด Job (Approve & Complete)
     */
    public function approve(Request $request, $id)
    {
        $job = Booking::findOrFail($id);
        $job->update(['status' => 'completed']);
        return redirect()->route('admin.jobs.index')->with('success', 'อนุมัติงานและปิด Job เรียบร้อยแล้ว!');
    }

    /**
     * 🟢 ยกเลิกงาน (Cancel)
     */
    public function cancel($id)
    {
        $job = Booking::findOrFail($id);
        $job->update(['status' => 'cancelled']);
        return response()->json(['success' => true, 'message' => 'ยกเลิกงานเรียบร้อย']);
    }

    /**
     * 🟢 เปลี่ยนคนขับด่วน (API Endpoint)
     */
    public function updateDriver(Request $request, $id)
    {
        $job = Booking::findOrFail($id);
        $job->update(['assigned_staff_id' => $request->staff_id]);
        return back()->with('success', 'เปลี่ยนคนขับเรียบร้อย');
    }

    /*
    |--------------------------------------------------------------------------
    | 4. 🛠️ HELPER ZONE (ตัวช่วย)
    |--------------------------------------------------------------------------
    | API เช็คข้อมูล, พิมพ์ใบเสร็จ, ฟังก์ชันแปลงค่าเงิน
    */

    /**
     * 🟢 API: เช็คคิวงานตามวันที่ (ใช้ตอนเลือกวันในหน้า Create)
     */
    public function getBookingsByDate(Request $request)
    {
        $date = $request->date;
        $equipmentId = $request->equipment_id;

        $query = Booking::whereDate('scheduled_start', $date)
            ->where('status', '!=', 'canceled');

        if ($equipmentId) {
            $query->where('equipment_id', $equipmentId);
        }

        $bookings = $query->get()->map(function ($job) {
            return [
                'job_number' => $job->job_number,
                'time_start' => Carbon::parse($job->scheduled_start)->format('H:i'),
                'time_end' => Carbon::parse($job->scheduled_end)->format('H:i'),
                'status' => $job->status,
            ];
        });

        return response()->json($bookings);
    }

    /**
     * 🟢 พิมพ์ใบเสร็จรับเงิน (Receipt)
     */
    public function receipt($id)
    {
        $booking = Booking::with(['customer', 'equipment', 'assignedStaff'])->findOrFail($id);

        $net_total = $booking->total_price - $booking->deposit_amount;
        $baht_text = $this->baht_text($net_total); // แปลงเลขเป็นคำอ่าน

        return view('admin.jobs.receipt', compact('booking', 'net_total', 'baht_text'));
    }

    /**
     * 🔢 ฟังก์ชันแปลงตัวเลขเป็นภาษาไทย (Baht Text)
     */
    private function baht_text($number)
    {
        if (!is_numeric($number) || $number < 0)
            return "-";

        $number = number_format($number, 2, '.', '');
        $number_parts = explode('.', $number);
        $integer_part = (int) $number_parts[0];
        $fraction_part = (int) $number_parts[1];

        $text_numbers = ['ศูนย์', 'หนึ่ง', 'สอง', 'สาม', 'สี่', 'ห้า', 'หก', 'เจ็ด', 'แปด', 'เก้า'];
        $text_digits = ['', 'สิบ', 'ร้อย', 'พัน', 'หมื่น', 'แสน', 'ล้าน'];

        if ($integer_part == 0) {
            $baht_text = "ศูนย์บาท";
        } else {
            $baht_text = "";
            $str_int = strrev((string) $integer_part);
            $len = strlen($str_int);

            for ($i = 0; $i < $len; $i++) {
                $digit = (int) $str_int[$i];
                if ($digit != 0) {
                    if ($i % 6 == 1 && $digit == 1) {
                        $baht_text = "ยี่" . $text_digits[$i % 6] . $baht_text;
                    } elseif ($i % 6 == 1 && $digit == 2) {
                        $baht_text = "ยี่" . $text_digits[$i % 6] . $baht_text;
                    } elseif ($i % 6 == 0 && $digit == 1 && $i > 0) {
                        $baht_text = "เอ็ด" . $text_digits[$i % 6] . $baht_text;
                    } else {
                        $baht_text = $text_numbers[$digit] . $text_digits[$i % 6] . $baht_text;
                    }
                }
            }
            $baht_text = str_replace("หนึ่งสิบ", "สิบ", $baht_text);
            $baht_text = str_replace("สองสิบ", "ยี่สิบ", $baht_text);
            $baht_text = str_replace("สิบหนึ่ง", "สิบเอ็ด", $baht_text);
            $baht_text .= "บาท";
        }

        if ($fraction_part == 0) {
            $baht_text .= "ถ้วน";
        } else {
            $str_satang = ($fraction_part < 10) ? "0" . $fraction_part : (string) $fraction_part;
            $satang_text = "";
            $first = (int) $str_satang[0];
            $second = (int) $str_satang[1];

            if ($first > 0) {
                if ($first == 1)
                    $satang_text .= "สิบ";
                elseif ($first == 2)
                    $satang_text .= "ยี่สิบ";
                else
                    $satang_text .= $text_numbers[$first] . "สิบ";
            }

            if ($second > 0) {
                if ($first > 0 && $second == 1)
                    $satang_text .= "เอ็ด";
                else
                    $satang_text .= $text_numbers[$second];
            }

            $baht_text .= $satang_text . "สตางค์";
        }

        return $baht_text;
    }
}