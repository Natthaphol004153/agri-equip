<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('customers', function (Blueprint $table) {
            if (!Schema::hasColumn('customers', 'work_location_address')) {
                $table->text('work_location_address')->nullable()->after('address');
            }
            if (!Schema::hasColumn('customers', 'work_latitude')) {
                $table->string('work_latitude')->nullable()->after('longitude');
            }
            if (!Schema::hasColumn('customers', 'work_longitude')) {
                $table->string('work_longitude')->nullable()->after('work_latitude');
            }
        });
    }

    public function down(): void
    {
        Schema::table('customers', function (Blueprint $table) {
            if (Schema::hasColumn('customers', 'work_longitude')) {
                $table->dropColumn('work_longitude');
            }
            if (Schema::hasColumn('customers', 'work_latitude')) {
                $table->dropColumn('work_latitude');
            }
            if (Schema::hasColumn('customers', 'work_location_address')) {
                $table->dropColumn('work_location_address');
            }
        });
    }
};
