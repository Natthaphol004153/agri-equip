<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('customers', function (Blueprint $table) {
            // เพิ่มระบบล็อกอิน
            if (!Schema::hasColumn('customers', 'password')) {
                $table->string('password')->after('customer_code')->nullable();
            }
            if (!Schema::hasColumn('customers', 'email')) {
                $table->string('email')->after('name')->nullable();
            }
            if (!Schema::hasColumn('customers', 'remember_token')) {
                $table->rememberToken();
            }

            // เพิ่มประเภทและเลขภาษี
            if (!Schema::hasColumn('customers', 'customer_type')) {
                $table->string('customer_type')->default('individual')->after('phone');
            }
            if (!Schema::hasColumn('customers', 'tax_id')) {
                $table->string('tax_id')->nullable()->after('customer_type');
            }

            // เพิ่มข้อมูลที่อยู่และพื้นที่
            if (!Schema::hasColumn('customers', 'province')) {
                $table->string('province')->nullable();
            }
            if (!Schema::hasColumn('customers', 'district')) {
                $table->string('district')->nullable();
            }
            if (!Schema::hasColumn('customers', 'postal_code')) {
                $table->string('postal_code')->nullable();
            }
            if (!Schema::hasColumn('customers', 'farm_area')) {
                $table->decimal('farm_area', 8, 2)->nullable();
            }
            if (!Schema::hasColumn('customers', 'profile_image')) {
                $table->string('profile_image')->nullable();
            }

            // ตรวจสอบพิกัดแผนที่ (เผื่อยังไม่มี)
            if (!Schema::hasColumn('customers', 'latitude')) {
                $table->string('latitude')->nullable();
            }
            if (!Schema::hasColumn('customers', 'longitude')) {
                $table->string('longitude')->nullable();
            }
        });
    }

    public function down(): void
    {
        Schema::table('customers', function (Blueprint $table) {
            $table->dropColumn(['password', 'email', 'customer_type', 'tax_id', 'province', 'district', 'postal_code', 'farm_area', 'profile_image', 'latitude', 'longitude']);
        });
    }
};