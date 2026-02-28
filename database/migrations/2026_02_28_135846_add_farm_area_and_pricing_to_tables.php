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
        // 1. ตาราง customers
        Schema::table('customers', function (Blueprint $table) {
            if (!Schema::hasColumn('customers', 'farm_area')) {
                $table->decimal('farm_area', 8, 2)->nullable()->comment('พื้นที่เพาะปลูกรวมของลูกค้า (ไร่)');
            }
        });

        // 2. ตาราง equipment
        Schema::table('equipment', function (Blueprint $table) {
            if (!Schema::hasColumn('equipment', 'price_per_rai')) {
                $table->decimal('price_per_rai', 10, 2)->nullable()->comment('เรทราคาต่อไร่');
            }
        });

        // 3. ตาราง bookings
        Schema::table('bookings', function (Blueprint $table) {
            if (!Schema::hasColumn('bookings', 'estimated_area')) {
                $table->decimal('estimated_area', 8, 2)->nullable()->comment('จำนวนไร่ประเมินเบื้องต้น');
            }
            if (!Schema::hasColumn('bookings', 'actual_area')) {
                $table->decimal('actual_area', 8, 2)->nullable()->comment('จำนวนไร่ที่ทำจริง');
            }
            if (!Schema::hasColumn('bookings', 'price_per_rai_at_booking')) {
                $table->decimal('price_per_rai_at_booking', 10, 2)->nullable()->comment('ราคาต่อไร่ของเครื่องจักร');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('customers', function (Blueprint $table) {
            $table->dropColumn('farm_area');
        });

        Schema::table('equipment', function (Blueprint $table) {
            $table->dropColumn('price_per_rai');
        });

        Schema::table('bookings', function (Blueprint $table) {
            $table->dropColumn([
                'estimated_area', 
                'actual_area', 
                'price_per_rai_at_booking'
            ]);
        });
    }
};