<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
{
    // 1. เพิ่มฟิลด์ให้ตารางเครื่องจักร
    Schema::table('equipment', function (Blueprint $table) {
        $table->enum('tracking_type', ['hours', 'kilometers'])->default('hours')->after('type');
        $table->decimal('current_kilometers', 10, 2)->default(0)->after('current_hours');
        $table->decimal('maintenance_km_threshold', 10, 2)->nullable()->after('maintenance_hour_threshold');
    });

    // 2. เพิ่มฟิลด์ให้ตารางใบงาน (บันทึกเลขที่พนักงานจดมา)
    Schema::table('bookings', function (Blueprint $table) {
        $table->decimal('meter_reading', 10, 2)->nullable()->after('actual_end')->comment('เลขหน้าปัดตอนจบงาน');
    });
}

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        //
    }
};
