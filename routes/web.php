<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Web\AuthController;
use App\Http\Controllers\Web\DashboardController; // Admin Dashboard
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
use App\Http\Controllers\Web\ExcelExportController;
use App\Http\Controllers\Web\ReportController;
use App\Http\Controllers\Web\CustomerAuthController;
// ✅ เรียกใช้ PublicController ที่สร้างใหม่
use App\Http\Controllers\Web\PublicController;
// ✅ เรียกใช้ Controller ลูกค้า
use App\Http\Controllers\Web\Customer\DashboardController as CustomerDashboardController;

/*
|--------------------------------------------------------------------------
| 🌍 PUBLIC ZONE (หน้าแรกสำหรับทุกคน)
|--------------------------------------------------------------------------
*/
// ❌ พับหน้าแรกเดิมไว้ก่อน (Landing Page)
// Route::get('/', [PublicController::class, 'index'])->name('home');

// ✅ ให้หน้าแรก Redirect ไปหน้า Admin Login เลย
Route::get('/', function () {
    return redirect()->route('login');
})->name('home');

// API สำหรับดึงข้อมูลปฏิทิน (ใช้โดย FullCalendar)
Route::get('/api/public-calendar', [PublicController::class, 'getCalendarEvents'])->name('public.calendar');


/*
|--------------------------------------------------------------------------
| 1. GUEST ZONE (หน้า Login ต่างๆ)
|--------------------------------------------------------------------------
*/
Route::middleware('guest')->group(function () {

    // 👮‍♂️ Admin Login (ย้ายมาที่ /admin/login)
    // หมายเหตุ: route('login') จะวิ่งมาที่นี่
    Route::get('/admin/login', [AuthController::class, 'loginForm'])->name('login');
    Route::post('/admin/login', [AuthController::class, 'login'])->name('login.submit');

    // 👷‍♂️ Staff PIN Login
    Route::get('/staff/login', [StaffLoginController::class, 'showLoginForm'])->name('staff.login');
    Route::post('/staff/login', [StaffLoginController::class, 'login'])->name('staff.login.submit');
});

/*
|--------------------------------------------------------------------------
| 🛒 CUSTOMER ZONE (ส่วนของลูกค้า)
|--------------------------------------------------------------------------
*/
// ส่วนนี้เปิดใช้งานอยู่แล้ว เข้าผ่าน /customer/login ได้เลยครับ
Route::prefix('customer')->name('customer.')->group(function () {

    // 1. ส่วนที่ยังไม่ได้ Login
    Route::middleware('guest:customer')->group(function () {
        Route::get('login', [CustomerAuthController::class, 'showLoginForm'])->name('login');
        Route::post('login', [CustomerAuthController::class, 'login'])->name('login.submit');
    });

    // 2. ส่วนที่ Login แล้ว
    Route::middleware('auth:customer')->group(function () {
        Route::get('dashboard', [CustomerDashboardController::class, 'index'])->name('dashboard');
        Route::get('api/check-schedule', [CustomerDashboardController::class, 'apiCheckSchedule'])
            ->name('booking.check_schedule');
        Route::get('booking/create', [CustomerDashboardController::class, 'create'])->name('booking.create');
        Route::post('booking', [CustomerDashboardController::class, 'store'])->name('booking.store');
        Route::post('bookings', [CustomerDashboardController::class, 'store'])->name('bookings.store');
        Route::get('booking/{id}', [CustomerDashboardController::class, 'show'])->name('booking.show');
        Route::get('bookings/{id}', [CustomerDashboardController::class, 'show'])->name('bookings.show');
        Route::get('booking/{id}/payment', [CustomerDashboardController::class, 'payment'])->name('booking.payment');
        Route::get('bookings/{id}/payment', [CustomerDashboardController::class, 'payment'])->name('bookings.payment');
        Route::post('booking/{id}/payment', [CustomerDashboardController::class, 'uploadSlip'])->name('booking.upload_slip');
        Route::post('bookings/{id}/payment', [CustomerDashboardController::class, 'uploadSlip'])->name('bookings.upload_slip');
        Route::post('logout', [CustomerAuthController::class, 'logout'])->name('logout');
    });
});

/*
|--------------------------------------------------------------------------
| 2. AUTHENTICATED ZONE (Admin & Staff)
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
            return view('admin.menus');
        })->name('all-menus');

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

            Route::get('/{id}/review', [JobController::class, 'review'])->name('review');
            Route::post('/{id}/approve', [JobController::class, 'approve'])->name('approve');
            Route::post('/{id}/cancel', [JobController::class, 'cancel'])->name('cancel');
            Route::post('/{id}/update-driver', [JobController::class, 'updateDriver'])->name('update_driver');
            Route::get('/{id}/receipt', [JobController::class, 'receipt'])->name('receipt');

            Route::get('/api/get-bookings', [JobController::class, 'getBookingsByDate'])->name('get_bookings');
        });

        // --- Maintenance Management ---
        // --- Maintenance Management ---
        Route::prefix('maintenance')->name('maintenance.')->controller(MaintenanceController::class)->group(function () {
            Route::get('/', 'index')->name('index');
            Route::get('/create', 'create')->name('create');
            Route::post('/store', 'store')->name('store');
            // ❌ ลบบรรทัด showReport ออกจากตรงนี้ครับ
            Route::get('/log/{id}/accept', 'showAcceptForm')->name('accept_form');
            Route::post('/log/{id}/accept', 'accept')->name('accept_submit');
            Route::post('/log/{id}/finish', 'finish')->name('finish');
            Route::post('/{id}/start', 'start')->name('start');
        });

        // --- Fuel Management ---
        Route::prefix('fuel-stocks')->name('fuel.')->controller(FuelStockController::class)->group(function () {
            Route::get('/', 'index')->name('index');
            Route::get('/purchase', 'createPurchase')->name('purchase');
            Route::post('/purchase', 'storePurchase')->name('store_purchase');
            Route::post('/withdraw', 'storeWithdraw')->name('withdraw');
            Route::post('/tank', 'storeTank')->name('tank.store');
            Route::delete('/tank/{id}', 'destroyTank')->name('tank.destroy');
        });

        // --- Reports & Exports ---
        Route::get('/reports', function () {
            return view('admin.reports.index');
        })->name('reports.index');
        Route::get('/reports/equipment-profit', [ReportController::class, 'equipmentProfit'])
            ->name('reports.equipment_profit');

        Route::prefix('export')->name('export.')->controller(ExcelExportController::class)->group(function () {
            Route::get('/jobs', 'exportJobs')->name('jobs');
            Route::get('/customers', 'exportCustomers')->name('customers');
            Route::get('/maintenance', 'exportMaintenance')->name('maintenance');
        });

        // --- Profile & Settings ---
        Route::get('/profile', [UserController::class, 'profileForm'])->name('profile');
        Route::post('/profile', [UserController::class, 'updateProfile'])->name('profile.update');
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

        Route::prefix('jobs')->name('jobs.')->group(function () {
            Route::get('/', [StaffJobController::class, 'index'])->name('index');
            Route::get('/{id}', [StaffJobController::class, 'show'])->name('show');
            Route::post('/{id}/start', [StaffJobController::class, 'startWork'])->name('start');
            Route::post('/{id}/finish', [StaffJobController::class, 'finishWork'])->name('finish');
            Route::post('/{id}/report-issue', [StaffJobController::class, 'reportIssue'])->name('report_issue');
        });

        Route::prefix('maintenance')->name('maintenance.')->group(function () {
            Route::get('/', [StaffJobController::class, 'maintenanceIndex'])->name('index');
            Route::get('/create', [StaffJobController::class, 'createReport'])->name('create');
            Route::post('/store', [StaffJobController::class, 'storeReport'])->name('store');
            Route::get('/{id}', [StaffJobController::class, 'showReport'])->name('show');
        });

        Route::get('/fuel/create', [FuelController::class, 'create'])->name('fuel.create');
        Route::post('/fuel/store', [FuelController::class, 'store'])->name('fuel.store');
        Route::post('/report-general', [StaffJobController::class, 'reportGeneral'])->name('report_general');
        Route::get('/jobs-history', [StaffJobController::class, 'history'])->name('jobs.history');
    });

});