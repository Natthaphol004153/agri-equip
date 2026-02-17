<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Booking;
use App\Models\User;
use App\Models\Customer;
use App\Models\Equipment;
use App\Services\BookingService;
use Carbon\Carbon;
use Exception;

class JobController extends Controller
{
    protected $bookingService;

    public function __construct(BookingService $bookingService)
    {
        $this->bookingService = $bookingService;
    }

    /*
    |--------------------------------------------------------------------------
    | 1. 📋 READ ZONE (ดูข้อมูล)
    |--------------------------------------------------------------------------
    */

    public function index(Request $request)
    {
        $status = $request->get('status', 'all');
        $machineType = $request->get('machine_type', 'all');
        $search = $request->get('search');

        $query = Booking::with(['customer', 'equipment', 'assignedStaff'])->latest();

        if ($status !== 'all') {
            $query->where('status', $status);
        }

        if ($machineType !== 'all') {
            $query->whereHas('equipment', function ($q) use ($machineType) {
                $q->where('type', $machineType);
            });
        }

        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->whereHas('customer', function ($sub) use ($search) {
                    $sub->where('name', 'like', "%$search%");
                })->orWhere('job_number', 'like', "%$search%");
            });
        }

        $jobs = $query->paginate(10)->withQueryString();
        $staffs = User::where('role', 'staff')->where('is_active', true)->get();

        if ($request->ajax()) {
            return view('admin.jobs.table', compact('jobs'))->render();
        }

        return view('admin.jobs.index', compact('jobs', 'staffs'));
    }

    public function show($id)
    {
        $job = Booking::with(['customer', 'equipment', 'assignedStaff'])->findOrFail($id);
        return view('admin.jobs.show', compact('job'));
    }

    /*
    |--------------------------------------------------------------------------
    | 2. 📝 CREATE & EDIT ZONE (เพิ่ม/แก้ไข)
    |--------------------------------------------------------------------------
    */

    public function create()
    {
        $customers = Customer::all();
        $equipments = Equipment::where('current_status', '!=', 'maintenance')->get();
        $staffs = User::where('role', 'staff')->where('is_active', true)->get();

        return view('admin.jobs.create', compact('customers', 'equipments', 'staffs'));
    }

    public function store(Request $request)
    {
        // Validation สำหรับการสร้างงานโดยแอดมิน (อาจจะกรอกราคาเลยก็ได้)
        $request->validate([
            'customer_id' => 'required|exists:customers,id',
            'equipment_id' => 'required|exists:equipment,id',
            'assigned_staff_id' => 'nullable|exists:users,id',
            'scheduled_start' => 'required|date',
            'scheduled_end' => 'required|date|after:scheduled_start',
            'total_price' => 'required|numeric|min:0',
            'deposit_amount' => 'nullable|numeric|min:0',
            'payment_proof' => 'nullable|image|max:5120',
            'payment_method' => 'nullable|in:transfer,cash',
        ]);

        try {
            $data = $request->only([
                'customer_id', 'equipment_id', 'assigned_staff_id',
                'scheduled_start', 'scheduled_end', 'total_price',
                'deposit_amount', 'payment_method'
            ]);

            // ถ้าแอดมินสร้างเองและใส่ยอดมัดจำ > 0 ให้ถือว่าจ่ายมัดจำแล้ว
            $data['payment_status'] = ($request->deposit_amount > 0) ? 'deposit_paid' : 'pending';

            if ($request->hasFile('payment_proof')) {
                $data['payment_proof'] = $request->file('payment_proof')->store('payments', 'public');
            }

            $this->bookingService->createBooking($data);

            return redirect()->route('admin.jobs.index')->with('success', 'สร้างงานใหม่สำเร็จ!');

        } catch (Exception $e) {
            return back()->with('error', 'ไม่สามารถจองได้: ' . $e->getMessage())->withInput();
        }
    }

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
     * - รองรับการแก้ราคาและมัดจำโดยแอดมิน
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
        // ✅ ปรับ Validation ให้ยืดหยุ่นขึ้น (nullable)
        $request->validate([
            'status' => 'required',
            'total_price' => 'nullable|numeric|min:0',    // แอดมินมากรอกทีหลังได้
            'deposit_amount' => 'nullable|numeric|min:0', // แอดมินมากรอกทีหลังได้
            'assigned_staff_id' => 'nullable|exists:users,id',
            // ข้อมูลอื่นๆ ถ้าต้องการให้แก้ได้
            // 'scheduled_start' => 'required|date',
            // 'scheduled_end' => 'required|date',
        ]);

        // ✅ อัปเดตข้อมูล (รวมถึงราคาและมัดจำ)
        $job->update([
            'status' => $request->status,
            'total_price' => $request->total_price ?? $job->total_price,
            'deposit_amount' => $request->deposit_amount ?? $job->deposit_amount,
            'assigned_staff_id' => $request->assigned_staff_id,
            'note' => $request->note,
            // ถ้ามีการแก้วันที่ในฟอร์มด้วย ให้ uncomment ด้านล่าง
            // 'scheduled_start' => $request->scheduled_start,
            // 'scheduled_end' => $request->scheduled_end,
        ]);

        return redirect()->route('admin.jobs.index')->with('success', 'บันทึกข้อมูลงานและราคาเรียบร้อยแล้ว');
    }

    /*
    |--------------------------------------------------------------------------
    | 3. ⚙️ ACTION ZONE (ดำเนินการ)
    |--------------------------------------------------------------------------
    */

    /**
     * ✅ เพิ่มใหม่: อนุมัติการจอง (เพื่อให้ลูกค้าจ่ายเงินได้)
     */
    public function approveBooking($id)
    {
        $booking = Booking::findOrFail($id);
        
        // เปลี่ยนสถานะเป็น scheduled (เพื่อให้ลูกค้าเห็นปุ่มจ่ายเงิน)
        $booking->update([
            'status' => 'scheduled' 
        ]);

        return back()->with('success', 'อนุมัติงานเรียบร้อยแล้ว ลูกค้าสามารถชำระเงินได้ทันที');
    }

    public function review($id)
    {
        $job = Booking::with(['customer', 'equipment', 'assignedStaff'])->findOrFail($id);
        return view('admin.jobs.review', compact('job'));
    }

    public function approve(Request $request, $id)
    {
        $job = Booking::findOrFail($id);
        $job->update(['status' => 'completed']);
        return redirect()->route('admin.jobs.index')->with('success', 'อนุมัติงานและปิด Job เรียบร้อยแล้ว!');
    }

    public function cancel($id)
    {
        $job = Booking::findOrFail($id);
        $job->update(['status' => 'cancelled']);
        return response()->json(['success' => true, 'message' => 'ยกเลิกงานเรียบร้อย']);
    }

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
    */

    public function getBookingsByDate(Request $request)
    {
        $date = $request->date;
        $equipmentId = $request->equipment_id;

        $query = Booking::whereDate('scheduled_start', $date)
            ->where('status', '!=', 'cancelled'); // แก้คำผิด canceled -> cancelled

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

    public function receipt($id)
    {
        $booking = Booking::with(['customer', 'equipment', 'assignedStaff'])->findOrFail($id);

        $net_total = $booking->total_price - $booking->deposit_amount;
        $baht_text = $this->baht_text($net_total);

        return view('admin.jobs.receipt', compact('booking', 'net_total', 'baht_text'));
    }

    private function baht_text($number)
    {
        if (!is_numeric($number) || $number < 0) return "-";

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
                if ($first == 1) $satang_text .= "สิบ";
                elseif ($first == 2) $satang_text .= "ยี่สิบ";
                else $satang_text .= $text_numbers[$first] . "สิบ";
            }

            if ($second > 0) {
                if ($first > 0 && $second == 1) $satang_text .= "เอ็ด";
                else $satang_text .= $text_numbers[$second];
            }

            $baht_text .= $satang_text . "สตางค์";
        }

        return $baht_text;
    }
}