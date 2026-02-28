<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('customers', function (Blueprint $table) {
            // เพิ่มประเภทลูกค้าและเลขผู้เสียภาษี
            if (!Schema::hasColumn('customers', 'customer_type')) {
                $table->string('customer_type')->default('individual')->after('phone');
            }
            if (!Schema::hasColumn('customers', 'tax_id')) {
                $table->string('tax_id')->nullable()->after('customer_type');
            }
            if (!Schema::hasColumn('customers', 'email')) {
                $table->string('email')->nullable()->after('tax_id');
            }

            // เพิ่มข้อมูลที่อยู่เชิงลึก (สำหรับระบบ Auto Complete)
            if (!Schema::hasColumn('customers', 'province')) {
                $table->string('province')->nullable()->after('address');
            }
            if (!Schema::hasColumn('customers', 'district')) {
                $table->string('district')->nullable()->after('province');
            }
            if (!Schema::hasColumn('customers', 'postal_code')) {
                $table->string('postal_code')->nullable()->after('district');
            }

            // เพิ่มพื้นที่ไร่ และรูปโปรไฟล์
            if (!Schema::hasColumn('customers', 'farm_area')) {
                $table->decimal('farm_area', 8, 2)->nullable()->after('postal_code');
            }
            if (!Schema::hasColumn('customers', 'profile_image')) {
                $table->string('profile_image')->nullable()->after('farm_area');
            }
            
            // ตรวจสอบพิกัด (บางเครื่องอาจใช้ string ในการเก็บพิกัดเพื่อความง่าย)
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
            $table->dropColumn([
                'customer_type', 'tax_id', 'email', 'province', 
                'district', 'postal_code', 'farm_area', 'profile_image'
            ]);
        });
    }
};