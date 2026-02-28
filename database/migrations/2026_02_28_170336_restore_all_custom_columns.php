<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // 1. จัดการตาราง customers (เพิ่มที่ฟ้องว่าหายไป)
        Schema::table('customers', function (Blueprint $table) {
            if (!Schema::hasColumn('customers', 'customer_type')) $table->string('customer_type')->default('individual')->after('phone');
            if (!Schema::hasColumn('customers', 'tax_id')) $table->string('tax_id')->nullable()->after('customer_type');
            if (!Schema::hasColumn('customers', 'password')) $table->string('password')->nullable()->after('customer_code');
            if (!Schema::hasColumn('customers', 'province')) $table->string('province')->nullable();
            if (!Schema::hasColumn('customers', 'district')) $table->string('district')->nullable();
            if (!Schema::hasColumn('customers', 'postal_code')) $table->string('postal_code')->nullable();
            if (!Schema::hasColumn('customers', 'farm_area')) $table->decimal('farm_area', 8, 2)->nullable();
            if (!Schema::hasColumn('customers', 'latitude')) $table->string('latitude')->nullable();
            if (!Schema::hasColumn('customers', 'longitude')) $table->string('longitude')->nullable();
            if (!Schema::hasColumn('customers', 'profile_image')) $table->string('profile_image')->nullable();
        });

        // 2. จัดการตาราง equipment (คืนชีพเรทราคาต่อไร่)
        Schema::table('equipment', function (Blueprint $table) {
            if (!Schema::hasColumn('equipment', 'price_per_rai')) {
                $table->decimal('price_per_rai', 10, 2)->nullable()->comment('เรทราคาต่อไร่');
            }
        });

        // 3. จัดการตาราง bookings (คืนชีพพื้นที่ประเมิน/ทำจริง)
        Schema::table('bookings', function (Blueprint $table) {
            if (!Schema::hasColumn('bookings', 'estimated_area')) $table->decimal('estimated_area', 8, 2)->nullable();
            if (!Schema::hasColumn('bookings', 'actual_area')) $table->decimal('actual_area', 8, 2)->nullable();
            if (!Schema::hasColumn('bookings', 'price_per_rai_at_booking')) $table->decimal('price_per_rai_at_booking', 10, 2)->nullable();
        });
    }

    public function down(): void { }
};