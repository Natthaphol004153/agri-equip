<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Web\AuthController;
use App\Http\Controllers\Web\DashboardController;
use App\Http\Controllers\Web\JobController;
use App\Http\Controllers\Web\CustomerController;
use App\Http\Controllers\Web\EquipmentController;
use App\Http\Controllers\Web\StaffJobController;
use App\Http\Controllers\Web\FuelController;
use App\Http\Controllers\Web\FuelStockController;
use App\Http\Controllers\Web\MaintenanceController;
use App\Http\Controllers\Web\UserController;
use App\Http\Controllers\Web\StaffLoginController;
use App\Http\Controllers\Web\SettingController;
// ✅ เพิ่ม Controller สำหรับ Excel (เดี๋ยวต้องไปสร้างไฟล์นี้)
use App\Http\Controllers\Web\ExcelExportController;
use App\Http\Controllers\Web\CustomerAuthController;

/*
|--------------------------------------------------------------------------
| 1. GUEST ZONE (คนทั่วไป / หน้า Login)
|--------------------------------------------------------------------------
*/
Route::middleware('guest')->group(function () {
    // Admin Login
    Route::get('/', [AuthController::class, 'loginForm'])->name('login');
    Route::get('/login', [AuthController::class, 'loginForm']);
    Route::post('/login', [AuthController::class, 'login'])->name('login.submit');

    // Staff PIN Login
    Route::get('/staff/login', [StaffLoginController::class, 'showLoginForm'])->name('staff.login');
    Route::post('/staff/login', [StaffLoginController::class, 'login'])->name('staff.login.submit');
});
// ==============================
// 🛒 CUSTOMER ZONE
// ==============================

// 1. ส่วนที่ยังไม่ได้ Login (Guest)
Route::prefix('customer')->name('customer.')->group(function () {
    Route::middleware('guest:customer')->group(function () {
        Route::get('login', [CustomerAuthController::class, 'showLoginForm'])->name('login');
        Route::post('login', [CustomerAuthController::class, 'login'])->name('login.submit');
    });

    // 2. ส่วนที่ Login แล้ว (Auth)
    Route::middleware('auth:customer')->group(function () {
        Route::get('dashboard', function () {
            return view('customer.dashboard');
        })->name('dashboard');

        Route::post('logout', [CustomerAuthController::class, 'logout'])->name('logout');
    });
});
/*
|--------------------------------------------------------------------------
| 2. AUTHENTICATED ZONE (ต้องล็อกอินก่อน)
|--------------------------------------------------------------------------
*/
Route::middleware(['auth'])->group(function () {

    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

    /*
    |--------------------------------------------------------------------------
    | 3. 👮‍♂️ ADMIN ZONE (เฉพาะ Admin)
    |--------------------------------------------------------------------------
    */
    Route::middleware(['is_admin'])->prefix('admin')->name('admin.')->group(function () {

        // --- Dashboard & Menus ---
        Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
        Route::get('/dashboard/financial-data', [DashboardController::class, 'getFinancialData'])->name('dashboard.financial');
        Route::get('/menus', function () {
            return view('admin.menus'); })->name('all-menus');

        // --- Main Resources ---
        Route::resource('customers', CustomerController::class);
        Route::resource('equipments', EquipmentController::class);
        Route::resource('users', UserController::class);

        // --- Job Management ---
        Route::prefix('jobs')->name('jobs.')->group(function () {
            Route::get('/', [JobController::class, 'index'])->name('index');
            Route::get('/create', [JobController::class, 'create'])->name('create');
            Route::post('/', [JobController::class, 'store'])->name('store');
            Route::get('/{id}', [JobController::class, 'show'])->name('show');
            Route::get('/{id}/edit', [JobController::class, 'edit'])->name('edit');
            Route::put('/{id}', [JobController::class, 'update'])->name('update');

            // Workflow & Actions
            Route::get('/{id}/review', [JobController::class, 'review'])->name('review');
            Route::post('/{id}/approve', [JobController::class, 'approve'])->name('approve');
            Route::post('/{id}/cancel', [JobController::class, 'cancel'])->name('cancel');
            Route::post('/{id}/update-driver', [JobController::class, 'updateDriver'])->name('update_driver');
            Route::get('/{id}/receipt', [JobController::class, 'receipt'])->name('receipt');

            // API Helper inside Admin
            Route::get('/api/get-bookings', [JobController::class, 'getBookingsByDate'])->name('get_bookings');
        });

        // --- Maintenance Management ---
        Route::prefix('maintenance')->name('maintenance.')->controller(MaintenanceController::class)->group(function () {
            Route::get('/', 'index')->name('index');
            Route::get('/create', 'create')->name('create');
            Route::post('/store', 'store')->name('store');
            Route::get('/log/{id}/accept', 'showAcceptForm')->name('accept_form');
            Route::post('/log/{id}/accept', 'accept')->name('accept_submit');
            Route::post('/log/{id}/finish', 'finish')->name('finish');
            Route::post('/{id}/start', 'start')->name('start');
        });

        // --- ⛽ Fuel Management (Stock In / Inventory) ---
        Route::prefix('fuel-stocks')->name('fuel.')->controller(FuelStockController::class)->group(function () {
            Route::get('/', 'index')->name('index');           // ดูสต็อก
            Route::get('/purchase', 'createPurchase')->name('purchase'); // ฟอร์มซื้อ
            Route::post('/purchase', 'storePurchase')->name('store_purchase'); // บันทึกซื้อ

            // เพิ่ม/ลบ ถังน้ำมัน (Admin Only)
            Route::post('/tank', 'storeTank')->name('tank.store');
            Route::delete('/tank/{id}', 'destroyTank')->name('tank.destroy');
        });

        // --- Reports ---
        Route::get('/reports', function () {
            return view('admin.reports.index'); })->name('reports.index');

        // ✅ --- EXCEL EXPORT ZONE (เพิ่มใหม่) ---
        // ตัวอย่าง: เรียกใช้ผ่าน route('admin.export.jobs')
        Route::prefix('export')->name('export.')->controller(ExcelExportController::class)->group(function () {
            Route::get('/jobs', 'exportJobs')->name('jobs');         // โหลดรายงานงานทั้งหมด
            Route::get('/customers', 'exportCustomers')->name('customers'); // โหลดรายชื่อลูกค้า
            Route::get('/maintenance', 'exportMaintenance')->name('maintenance'); // โหลดประวัติซ่อมบำรุง
        });

        // --- Profile ---
        Route::get('/profile', [UserController::class, 'profileForm'])->name('profile');
        Route::post('/profile', [UserController::class, 'updateProfile'])->name('profile.update');

        // --- ⚙️ Settings (ตั้งค่าระบบ) ---
        Route::get('/settings', [SettingController::class, 'index'])->name('settings.index');
        Route::post('/settings', [SettingController::class, 'update'])->name('settings.update');
    });

    /*
    |--------------------------------------------------------------------------
    | 4. 👷‍♂️ STAFF ZONE (พนักงานทั่วไป)
    |--------------------------------------------------------------------------
    */
    Route::prefix('staff')->name('staff.')->group(function () {

        Route::get('/dashboard', [StaffJobController::class, 'dashboard'])->name('dashboard');

        // --- Jobs (Staff View) ---
        Route::prefix('jobs')->name('jobs.')->group(function () {
            Route::get('/', [StaffJobController::class, 'index'])->name('index');
            Route::get('/{id}', [StaffJobController::class, 'show'])->name('show');
            Route::post('/{id}/start', [StaffJobController::class, 'startWork'])->name('start');
            Route::post('/{id}/finish', [StaffJobController::class, 'finishWork'])->name('finish');
            Route::post('/{id}/report-issue', [StaffJobController::class, 'reportIssue'])->name('report_issue');
        });

        // --- Maintenance (Report Only) ---
        Route::prefix('maintenance')->name('maintenance.')->group(function () {
            Route::get('/', [StaffJobController::class, 'maintenanceIndex'])->name('index');
            Route::get('/create', [StaffJobController::class, 'createReport'])->name('create');
            Route::post('/store', [StaffJobController::class, 'storeReport'])->name('store');
        });

        // --- ⛽ Fuel Usage (เบิกใช้น้ำมัน) ---
        Route::get('/fuel/create', [FuelController::class, 'create'])->name('fuel.create');
        Route::post('/fuel/store', [FuelController::class, 'store'])->name('fuel.store');

        // --- General Report ---
        Route::post('/report-general', [StaffJobController::class, 'reportGeneral'])->name('report_general');

        // --- History ---
        Route::get('/jobs-history', [StaffJobController::class, 'history'])->name('jobs.history');
    });

});