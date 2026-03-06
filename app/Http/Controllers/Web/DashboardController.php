<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Booking;
use App\Models\Equipment;
use App\Models\MaintenanceLog;
use App\Models\User;
use App\Models\FuelLog;
use App\Models\FuelPurchase;
use Carbon\Carbon;

class DashboardController extends Controller
{
    public function index()
    {
        // ------------------------------------------------------------------
        // 1. Financial Overview (การเงินภาพรวม) - *เก็บไว้เผื่อใช้หน้า Report
        // ------------------------------------------------------------------
        $totalIncome = Booking::where('status', 'completed')->sum('total_price');
        $maintenanceCost = MaintenanceLog::sum('total_cost'); 
        $fuelPurchaseCost = FuelPurchase::sum('total_cost'); 
        $totalExpense = $maintenanceCost + $fuelPurchaseCost;
        $netProfit = $totalIncome - $totalExpense;
        $fuelCostThisMonth = FuelPurchase::whereMonth('purchase_date', Carbon::now()->month)
            ->whereYear('purchase_date', Carbon::now()->year)
            ->sum('total_cost');

        // ------------------------------------------------------------------
        // 2. Operations (การดำเนินงาน)
        // ------------------------------------------------------------------
        $today = Carbon::today();

        $completedJobs = Booking::where('status', 'completed')
            ->whereMonth('actual_end', $today->month)
            ->whereYear('actual_end', $today->year)
            ->count();
            
        $activeMachines = Booking::where('status', 'in_progress')->count();
        $availableStaff = User::where('role', 'staff')->count(); // อาจต้องเพิ่ม logic เช็คพนักงานที่ว่างจริงๆ
        $fuelRequests = FuelLog::whereDate('created_at', $today)->count();

        // ------------------------------------------------------------------
        // 3. Today's Action & Exceptions (จัดการด่วน & แจ้งเตือนความผิดปกติ) 🚨
        // ------------------------------------------------------------------
        
        // 3.1 คิวงานปกติที่มีกำหนดทำ "วันนี้"
        $todayJobs = Booking::with(['customer', 'equipment'])
            ->whereDate('scheduled_start', $today)
            ->whereNotIn('status', ['cancelled']) // ไม่เอางานที่ยกเลิกไปแล้ว
            ->get()
            ->map(function ($job) {
                return (object) [
                    'id' => $job->id,               // 🟢 เพิ่ม ID เพื่อให้ปุ่มลิงก์ทำงานได้
                    'status' => $job->status,       // 🟢 เพิ่ม Status เพื่อให้ป้ายเปลี่ยนสีตามจริง
                    'job_number' => $job->job_number ?? 'JOB-'.$job->id,
                    'time_range' => Carbon::parse($job->scheduled_start)->format('H:i') . ' - ' . Carbon::parse($job->scheduled_end)->format('H:i'),
                    'customer_name' => $job->customer->name ?? 'ไม่ระบุลูกค้า',
                    'equipment_name' => $job->equipment->name ?? 'ไม่ระบุเครื่องจักร'
                ];
            });

        // 3.2 งานรออนุมัติปกติ (ที่มีกำหนดในอนาคต หรือ วันนี้)
        $pendingJobs = Booking::whereIn('status', ['pending_approval', 'pending'])
            ->whereDate('scheduled_start', '>=', $today)
            ->get();
            
        $pendingJobsCount = $pendingJobs->count(); // สำหรับแสดงในกล่องสถิติ

        // 3.3 งานรออนุมัติ แต่เลยวันนัดหมายมาแล้ว (Expired Pending) ⚠️
        $expiredPendingJobs = Booking::whereIn('status', ['pending_approval', 'pending'])
            ->whereDate('scheduled_start', '<', $today)
            ->get();

        // 3.4 งานอนุมัติแล้ว/มอบหมายแล้ว แต่เลยวันนัดหมายพนักงานยังไม่ทำ (Overdue) 🚨
        $overdueJobs = Booking::whereIn('status', ['scheduled', 'approved', 'assigned'])
            ->whereDate('scheduled_start', '<', $today)
            ->get();

        // แจ้งเตือนซ่อมบำรุง (ใกล้ถึงชั่วโมงซ่อม)
        $maintenanceAlerts = Equipment::whereRaw('current_hours >= (maintenance_hour_threshold - 10)')
            ->orderByRaw('(maintenance_hour_threshold - current_hours) ASC')
            ->get();

        // ------------------------------------------------------------------
        // 4. Calendar Events (ปฏิทินงาน)
        // ------------------------------------------------------------------
        $calendarBookings = Booking::with(['customer', 'equipment', 'assignedStaff'])
            ->where('status', '!=', 'cancelled')
            ->get()
            ->map(function ($job) {
                $statusConfig = match ($job->status) {
                    'pending' => ['color' => 'bg-gray-100 text-gray-600 border-gray-200', 'icon' => 'fa-clock', 'label' => 'รออนุมัติ'],
                    'approved' => ['color' => 'bg-blue-50 text-blue-700 border-blue-200', 'icon' => 'fa-calendar-check', 'label' => 'นัดหมายแล้ว'],
                    'assigned' => ['color' => 'bg-indigo-50 text-indigo-700 border-indigo-200', 'icon' => 'fa-user-check', 'label' => 'จ่ายงานแล้ว'],
                    'in_progress' => ['color' => 'bg-purple-50 text-purple-700 border-purple-200', 'icon' => 'fa-spinner fa-spin', 'label' => 'กำลังดำเนินการ'],
                    'completed_pending_approval' => ['color' => 'bg-orange-50 text-orange-700 border-orange-200', 'icon' => 'fa-clipboard-question', 'label' => 'รอตรวจงาน'],
                    'completed' => ['color' => 'bg-green-50 text-green-700 border-green-200', 'icon' => 'fa-circle-check', 'label' => 'เสร็จสิ้น'],
                    default => ['color' => 'bg-gray-50 text-gray-500 border-gray-200', 'icon' => 'fa-circle', 'label' => '-'],
                };

                $start = $job->scheduled_start ? Carbon::parse($job->scheduled_start) : $job->created_at;
                $end = $job->scheduled_end ? Carbon::parse($job->scheduled_end) : $start->copy()->addHours(2);

                return [
                    'id' => $job->id,
                    'job_number' => $job->job_number ?? 'JOB-'.$job->id,
                    'title' => $job->customer->name ?? '-',
                    'phone' => $job->customer->phone ?? '-',
                    'location' => $job->customer->address ?? '-',
                    'equipment' => $job->equipment->name ?? '-',
                    'equipment_code' => $job->equipment->equipment_code ?? '',
                    'staff' => $job->assignedStaff ? $job->assignedStaff->name : 'ยังไม่ระบุช่าง',
                    'staff_avatar' => $job->assignedStaff 
                        ? 'https://ui-avatars.com/api/?name='.urlencode($job->assignedStaff->name).'&background=random&color=fff&size=64' 
                        : null,
                    'start_date' => $start->format('Y-m-d'),
                    'time_range' => $start->format('H:i') . ' - ' . $end->format('H:i'),
                    'price' => number_format($job->total_price),
                    'status' => $statusConfig,
                    'url' => route('admin.jobs.show', $job->id)
                ];
            });

        return view('admin.dashboard', compact(
            'totalIncome', 'totalExpense', 'netProfit', 'fuelPurchaseCost', 'fuelCostThisMonth',
            'maintenanceCost', 
            'completedJobs', 'pendingJobsCount', 'activeMachines', 'availableStaff', 'fuelRequests',
            'maintenanceAlerts', 'calendarBookings',
            'todayJobs', 'pendingJobs', 'expiredPendingJobs', 'overdueJobs' // ✅ ส่งตัวแปรใหม่ไป View
        ));
    }

    public function getFinancialData(Request $request)
    {
        $start = Carbon::parse($request->start_date)->startOfDay();
        $end = Carbon::parse($request->end_date)->endOfDay();

        $labels = [];
        $current = $start->copy();
        while ($current <= $end) {
            $labels[] = $current->format('d M');
            $current->addDay();
        }

        // รายรับ (Income)
        $incomes = Booking::selectRaw('DATE(actual_end) as date, SUM(total_price) as total')
            ->where('status', 'completed')
            ->whereBetween('actual_end', [$start, $end])
            ->groupBy('date')->pluck('total', 'date');

        // รายจ่ายค่าน้ำมัน (Fuel Cost - จากการซื้อเข้า)
        $fuelCosts = FuelPurchase::selectRaw('DATE(purchase_date) as date, SUM(total_cost) as total')
            ->whereBetween('purchase_date', [$start, $end])
            ->groupBy('date')->pluck('total', 'date');

        // รายจ่ายค่าซ่อมบำรุง (Maintenance Cost)
        $maintCosts = MaintenanceLog::selectRaw('DATE(completion_date) as date, SUM(total_cost) as total')
            ->where('status', 'completed')
            ->whereBetween('completion_date', [$start, $end])
            ->groupBy('date')->pluck('total', 'date');

        // ชั่วโมงทำงาน (Operational Hours)
        $hours = Booking::selectRaw('DATE(scheduled_start) as date, SUM(TIMESTAMPDIFF(HOUR, scheduled_start, scheduled_end)) as total')
            ->where('status', 'completed')
            ->whereBetween('scheduled_start', [$start, $end])
            ->groupBy('date')->pluck('total', 'date');

        $incomeData = [];
        $costData = [];
        $hourData = [];
        $sumIncome = 0; $sumCost = 0; $sumHours = 0;

        $current = $start->copy();
        while ($current <= $end) {
            $dateKey = $current->format('Y-m-d');
            $inc = $incomes[$dateKey] ?? 0;
            $fc = $fuelCosts[$dateKey] ?? 0;
            $mc = $maintCosts[$dateKey] ?? 0;
            $hr = $hours[$dateKey] ?? 0;
            
            $totalC = $fc + $mc;
            
            $incomeData[] = $inc;
            $costData[] = $totalC;
            $hourData[] = $hr;

            $sumIncome += $inc;
            $sumCost += $totalC;
            $sumHours += $hr;
            $current->addDay();
        }

        $summaryMaintenance = MaintenanceLog::where('status', 'completed')
            ->whereBetween('completion_date', [$start, $end])
            ->sum('total_cost');

        $summaryFuel = FuelPurchase::whereBetween('purchase_date', [$start, $end])
            ->sum('total_cost');

        return response()->json([
            'labels' => $labels,
            'income' => $incomeData,
            'costs' => $costData,
            'hours' => $hourData,
            'summary' => [
                'total_income' => $sumIncome,
                'total_cost' => $sumCost,
                'net_profit' => $sumIncome - $sumCost,
                'total_hours' => $sumHours,
                'total_maintenance' => $summaryMaintenance, 
                'total_fuel' => $summaryFuel 
            ]
        ]);
    }
}