<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('fuel_logs', function (Blueprint $table) {
            // เพิ่ม column ระบุแหล่งที่มา (ปั๊มข้างนอก หรือ ถังบริษัท)
            $table->enum('fuel_source', ['external', 'internal'])->default('external')->after('user_id');
            
            // ถ้าเติมถังบริษัท ต้องรู้ว่าตัดจากถังไหน
            $table->foreignId('fuel_tank_id')->nullable()->after('fuel_source')->constrained('fuel_tanks');
            
            // แก้ amount ให้เป็น nullable (เพราะถ้าเติมภายใน จะคำนวณทีหลัง)
            $table->decimal('amount', 10, 2)->nullable()->change();
        });
    }

    public function down()
    {
        Schema::table('fuel_logs', function (Blueprint $table) {
            $table->dropForeign(['fuel_tank_id']);
            $table->dropColumn(['fuel_source', 'fuel_tank_id']);
            $table->decimal('amount', 10, 2)->nullable(false)->change();
        });
    }
};