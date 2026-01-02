<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Booking;
use App\Models\User;
use App\Models\Customer;
use App\Models\Equipment;
use Carbon\Carbon;

class JobController extends Controller
{
    /**
     * 🟢 แสดงรายการงานทั้งหมด (Admin View)
     */
    public function index(Request $request)
    {
        // 1. รับค่า Filter (เพิ่ม machine_type)
        $status = $request->get('status', 'all');
        $machineType = $request->get('machine_type', 'all'); // <--- 🔥 เพิ่มบรรทัดนี้
        $search = $request->get('search');

        // 2. Query ข้อมูล
        $query = Booking::with(['customer', 'equipment', 'assignedStaff'])
            ->latest(); // เรียงจากใหม่ไปเก่า

        // 3. กรองตามสถานะ
        if ($status !== 'all') {
            $query->where('status', $status);
        }

        // 4. 🔥 กรองตามประเภทเครื่องจักร [เพิ่มใหม่]
        if ($machineType !== 'all') {
            $query->whereHas('equipment', function ($q) use ($machineType) {
                $q->where('type', $machineType);
            });
        }

        // 5. ค้นหา (ชื่อลูกค้า หรือ เลข Job)
        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->whereHas('customer', function ($sub) use ($search) {
                    $sub->where('name', 'like', "%$search%");
                })->orWhere('job_number', 'like', "%$search%");
            });
        }

        // 6. Pagination
        $jobs = $query->paginate(10)->withQueryString();

        // 7. โหลดข้อมูล Staff ไว้สำหรับ Modal "มอบหมายงานด่วน"
        $staffs = User::where('role', 'staff')->where('is_active', true)->get();

        // ถ้าเป็น AJAX Request (ตอนกด Tab หรือ Search) ให้ส่งเฉพาะตารางกลับไป
        if ($request->ajax()) {
            return view('admin.jobs.table', compact('jobs'))->render();
        }

        return view('admin.jobs.index', compact('jobs', 'staffs'));
    }

    /**
     * 🟢 ฟอร์มสร้างงานใหม่
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
     * 🟢 บันทึกงานใหม่
     */
    public function store(Request $request)
    {
        $request->validate([
            'customer_id' => 'required|exists:customers,id',
            'equipment_id' => 'required|exists:equipment,id',
            'assigned_staff_id' => 'required|exists:users,id',
            'scheduled_start' => 'required|date',
            'scheduled_end' => 'required|date|after:scheduled_start',
            'total_price' => 'required|numeric|min:0',
            'deposit_amount' => 'nullable|numeric|min:0',
        ]);

        // สร้าง Job Number (เช่น JOB-20240101-0001)
        $dateStr = date('Ymd');
        $lastJob = Booking::where('job_number', 'like', "JOB-$dateStr-%")->latest()->first();
        $nextNum = $lastJob ? intval(substr($lastJob->job_number, -4)) + 1 : 1;
        $jobNumber = "JOB-$dateStr-" . sprintf('%04d', $nextNum);

        Booking::create([
            'job_number' => $jobNumber,
            'customer_id' => $request->customer_id,
            'equipment_id' => $request->equipment_id,
            'assigned_staff_id' => $request->assigned_staff_id,
            'scheduled_start' => $request->scheduled_start,
            'scheduled_end' => $request->scheduled_end,
            'total_price' => $request->total_price,
            'deposit_amount' => $request->deposit_amount ?? 0,
            'payment_status' => ($request->deposit_amount > 0) ? 'deposit_paid' : 'pending',
            'status' => 'scheduled',
        ]);

        return redirect()->route('admin.jobs.index')->with('success', 'สร้างงานใหม่สำเร็จ!');
    }

    /**
     * 🟢 แสดงรายละเอียดงาน
     */
    public function show($id)
    {
        $job = Booking::with(['customer', 'equipment', 'assignedStaff'])->findOrFail($id);
        return view('admin.jobs.show', compact('job'));
    }

    /**
     * 🟢 แก้ไขงาน
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
     * 🟢 อัปเดตงาน
     */
    public function update(Request $request, $id)
    {
        $job = Booking::findOrFail($id);

        // ถ้าเป็น AJAX Request (จาก Quick Assign Modal)
        if ($request->ajax() && $request->has('assigned_staff_id')) {
            $job->update(['assigned_staff_id' => $request->assigned_staff_id]);
            return response()->json(['success' => true, 'message' => 'มอบหมายงานสำเร็จ']);
        }

        // ถ้าเป็น Form Submit ปกติ (จากหน้า Edit)
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

    /**
     * 🟢 API เช็คคิวงาน (สำหรับหน้า Create)
     */
    public function getBookingsByDate(Request $request)
    {
        $date = $request->date; // Y-m-d
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
     * 🟢 เปลี่ยนคนขับ (API)
     */
    public function updateDriver(Request $request, $id)
    {
        $job = Booking::findOrFail($id);
        $job->update(['assigned_staff_id' => $request->staff_id]);
        return back()->with('success', 'เปลี่ยนคนขับเรียบร้อย');
    }

    /**
     * 🟢 ยกเลิกงาน
     */
    public function cancel($id)
    {
        $job = Booking::findOrFail($id);
        // หมายเหตุ: เช็ค enum ใน database ด้วยนะครับว่าเป็น 'canceled' หรือ 'cancelled' (เบิ้ล l)
        $job->update(['status' => 'cancelled']); 
        return response()->json(['success' => true, 'message' => 'ยกเลิกงานเรียบร้อย']);
    }

    /**
     * 🟢 หน้าตรวจสอบงาน (Review)
     */
    public function review($id)
    {
        $job = Booking::with(['customer', 'equipment', 'assignedStaff'])->findOrFail($id);
        return view('admin.jobs.review', compact('job'));
    }

    /**
     * 🟢 อนุมัติงาน (Approve)
     */
    public function approve(Request $request, $id)
    {
        $job = Booking::findOrFail($id);

        // อัปเดตสถานะเป็น "เสร็จสิ้นสมบูรณ์" (completed)
        $job->update([
            'status' => 'completed',
        ]);

        return redirect()->route('admin.jobs.index')->with('success', 'อนุมัติงานและปิด Job เรียบร้อยแล้ว!');
    }

    // ==========================================
    // 🛠️ ส่วนที่แก้ไข: ใบเสร็จรับเงิน
    // ==========================================

    public function receipt($id)
    {
        // 1. เปลี่ยนชื่อตัวแปรเป็น $booking ให้ตรงกับ View
        $booking = Booking::with(['customer', 'equipment', 'assignedStaff'])->findOrFail($id);
        
        // 2. คำนวณยอดเงิน
        $net_total = $booking->total_price - $booking->deposit_amount;
        
        // 3. แปลงเลขเป็นคำอ่านภาษาไทย
        $baht_text = $this->baht_text($net_total);

        return view('admin.jobs.receipt', compact('booking', 'net_total', 'baht_text'));
    }

    /**
     * ฟังก์ชันแปลงตัวเลขเป็นภาษาไทย (Baht Text)
     */
    private function baht_text($number)
    {
        if (!is_numeric($number) || $number < 0) return "-";

        $number = number_format($number, 2, '.', '');
        $number_parts = explode('.', $number);
        $integer_part = (int)$number_parts[0];
        $fraction_part = (int)$number_parts[1];

        $text_numbers = ['ศูนย์', 'หนึ่ง', 'สอง', 'สาม', 'สี่', 'ห้า', 'หก', 'เจ็ด', 'แปด', 'เก้า'];
        $text_digits = ['', 'สิบ', 'ร้อย', 'พัน', 'หมื่น', 'แสน', 'ล้าน'];

        if ($integer_part == 0) {
            $baht_text = "ศูนย์บาท";
        } else {
            $baht_text = "";
            $str_int = strrev((string)$integer_part);
            $len = strlen($str_int);

            for ($i = 0; $i < $len; $i++) {
                $digit = (int)$str_int[$i];
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
            $str_satang = ($fraction_part < 10) ? "0" . $fraction_part : (string)$fraction_part;
            $satang_text = "";
            $first = (int)$str_satang[0];
            $second = (int)$str_satang[1];

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