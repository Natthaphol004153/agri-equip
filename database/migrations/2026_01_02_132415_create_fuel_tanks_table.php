<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('fuel_tanks', function (Blueprint $table) {
            $table->id();
            $table->string('name'); // เช่น ถังใหญ่ 1, ถังสำรอง
            $table->decimal('capacity', 10, 2); // ความจุถัง (ลิตร)
            $table->decimal('current_balance', 10, 2)->default(0); // น้ำมันคงเหลือ (ลิตร)
            $table->decimal('average_price', 10, 4)->default(0); // ราคาเฉลี่ยต่อลิตร (บาท)
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('fuel_tanks');
    }
};