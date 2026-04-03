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
        Schema::table('maintenance_logs', function (Blueprint $table) {
            $table->decimal('service_meter_reading', 10, 2)
                ->nullable()
                ->after('reset_counter')
                ->comment('ค่าหน้าปัด ณ ตอนปิดงานซ่อมใหญ่');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('maintenance_logs', function (Blueprint $table) {
            $table->dropColumn('service_meter_reading');
        });
    }
};
