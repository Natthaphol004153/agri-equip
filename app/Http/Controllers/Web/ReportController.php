<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\Booking;
use App\Models\Equipment;
use App\Models\FuelLog;
use App\Models\MaintenanceLog;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ReportController extends Controller
{
    public function index(Request $request)
    {
        $selectedMonth = $request->input('month', Carbon::now()->format('Y-m'));

        try {
            $periodStart = Carbon::createFromFormat('Y-m', $selectedMonth)->startOfMonth();
        } catch (\Throwable $e) {
            $periodStart = Carbon::now()->startOfMonth();
            $selectedMonth = $periodStart->format('Y-m');
        }
        $periodEnd = $periodStart->copy()->endOfMonth();

        $totalRevenue = Booking::whereIn('status', ['completed', 'completed_pending_approval'])
            ->whereBetween('scheduled_start', [$periodStart, $periodEnd])
            ->sum('total_price');

        $completedJobs = Booking::where('status', 'completed')
            ->whereBetween('scheduled_start', [$periodStart, $periodEnd])
            ->count();

        $cancelledJobs = Booking::where('status', 'cancelled')
            ->whereBetween('scheduled_start', [$periodStart, $periodEnd])
            ->count();

        $maintenanceCost = MaintenanceLog::where('status', 'completed')
            ->whereBetween('completion_date', [$periodStart, $periodEnd])
            ->sum('total_cost');

        $maintenanceJobs = MaintenanceLog::whereBetween('maintenance_date', [$periodStart, $periodEnd])
            ->count();

        $recentTransactions = Booking::with(['customer', 'equipment'])
            ->whereBetween('scheduled_start', [$periodStart, $periodEnd])
            ->latest('scheduled_start')
            ->take(10)
            ->get();

        $monthOptions = Booking::selectRaw("DATE_FORMAT(scheduled_start, '%Y-%m') as month_key")
            ->whereNotNull('scheduled_start')
            ->groupBy('month_key')
            ->orderBy('month_key', 'desc')
            ->limit(12)
            ->pluck('month_key')
            ->mapWithKeys(function ($monthKey) {
                $label = Carbon::createFromFormat('Y-m', $monthKey)->locale('th')->translatedFormat('F Y');
                return [$monthKey => $label];
            })
            ->toArray();

        if (empty($monthOptions)) {
            $monthOptions[$selectedMonth] = $periodStart->locale('th')->translatedFormat('F Y');
        } elseif (!isset($monthOptions[$selectedMonth])) {
            $monthOptions = [$selectedMonth => $periodStart->locale('th')->translatedFormat('F Y')] + $monthOptions;
        }

        return view('admin.reports.index', [
            'selectedMonth' => $selectedMonth,
            'monthOptions' => $monthOptions,
            'summary' => (object) [
                'total_revenue' => (float) $totalRevenue,
                'completed_jobs' => $completedJobs,
                'cancelled_jobs' => $cancelledJobs,
                'maintenance_jobs' => $maintenanceJobs,
                'maintenance_cost' => (float) $maintenanceCost,
            ],
            'recentTransactions' => $recentTransactions,
        ]);
    }

    public function equipmentProfit(Request $request)
    {
        $startDate = $request->input('start_date', Carbon::now()->subDays(30)->toDateString());
        $endDate = $request->input('end_date', Carbon::now()->toDateString());

        $start = Carbon::parse($startDate)->startOfDay();
        $end = Carbon::parse($endDate)->endOfDay();

        $bookingSums = Booking::selectRaw('equipment_id, SUM(total_price) as revenue')
            ->whereIn('status', ['completed', 'completed_pending_approval'])
            ->whereBetween('scheduled_start', [$start, $end])
            ->groupBy('equipment_id')
            ->pluck('revenue', 'equipment_id');

        $fuelSums = FuelLog::selectRaw('equipment_id, SUM(COALESCE(amount, 0)) as fuel_cost')
            ->whereBetween('refill_date', [$start, $end])
            ->groupBy('equipment_id')
            ->pluck('fuel_cost', 'equipment_id');

        $maintenanceSums = MaintenanceLog::selectRaw('equipment_id, SUM(COALESCE(total_cost, 0)) as maintenance_cost')
            ->whereBetween('maintenance_date', [$start, $end])
            ->groupBy('equipment_id')
            ->pluck('maintenance_cost', 'equipment_id');

        $equipments = Equipment::orderBy('name')->get();

        $rows = $equipments->map(function ($equipment) use ($bookingSums, $fuelSums, $maintenanceSums) {
            $revenue = (float) ($bookingSums[$equipment->id] ?? 0);
            $fuelCost = (float) ($fuelSums[$equipment->id] ?? 0);
            $maintenanceCost = (float) ($maintenanceSums[$equipment->id] ?? 0);
            $profit = $revenue - $fuelCost - $maintenanceCost;

            return (object) [
                'equipment' => $equipment,
                'revenue' => $revenue,
                'fuel_cost' => $fuelCost,
                'maintenance_cost' => $maintenanceCost,
                'profit' => $profit,
            ];
        })->sortByDesc('profit')->values();

        $totals = (object) [
            'revenue' => $rows->sum('revenue'),
            'fuel_cost' => $rows->sum('fuel_cost'),
            'maintenance_cost' => $rows->sum('maintenance_cost'),
            'profit' => $rows->sum('profit'),
        ];

        return view('admin.reports.equipment-profit', [
            'rows' => $rows,
            'totals' => $totals,
            'startDate' => $startDate,
            'endDate' => $endDate,
        ]);
    }
}
