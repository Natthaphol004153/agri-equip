<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\JobsExport; // ต้องไปสร้างไฟล์ Export นี้ด้วย
use Illuminate\Http\Request;

class ExcelExportController extends Controller
{
    public function exportJobs()
    {
        return Excel::download(new JobsExport, 'jobs_report.xlsx');
    }

    public function exportCustomers()
    {
        // return Excel::download(new CustomersExport, 'customers.xlsx');
        return "Coming Soon"; // ใส่ไว้กัน Error ก่อน
    }

    public function exportMaintenance()
    {
        return "Coming Soon";
    }
}