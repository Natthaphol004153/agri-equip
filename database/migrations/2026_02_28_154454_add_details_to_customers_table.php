<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('customers', function (Blueprint $table) {
            // เพิ่มประเภทลูกค้า (เช่น individual, farm, company)
            if (!Schema::hasColumn('customers', 'customer_type')) {
                $table->string('customer_type')->default('individual')->after('phone');
            }
            
            // เพิ่มเลขผู้เสียภาษี
            if (!Schema::hasColumn('customers', 'tax_id')) {
                $table->string('tax_id')->nullable()->after('customer_type');
            }

            // เผื่อว่าก่อนหน้านี้ยังไม่ได้เพิ่ม farm_area
            if (!Schema::hasColumn('customers', 'farm_area')) {
                $table->decimal('farm_area', 8, 2)->nullable()->after('tax_id')->comment('พื้นที่เพาะปลูกรวม (ไร่)');
            }
        });
    }

    public function down(): void
    {
        Schema::table('customers', function (Blueprint $table) {
            $table->dropColumn(['customer_type', 'tax_id', 'farm_area']);
        });
    }
};