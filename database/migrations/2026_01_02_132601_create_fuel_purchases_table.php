<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('fuel_purchases', function (Blueprint $table) {
            $table->id();
            $table->foreignId('fuel_tank_id')->constrained('fuel_tanks')->onDelete('cascade');
            $table->decimal('liters', 10, 2); // จำนวนลิตรที่ซื้อ
            $table->decimal('price_per_liter', 10, 2); // ราคาต่อลิตร
            $table->decimal('total_cost', 12, 2); // ราคารวม (liters * price)
            $table->date('purchase_date'); // วันที่ซื้อ
            $table->string('supplier')->nullable(); // ซื้อจากที่ไหน (Optional)
            $table->text('note')->nullable();
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('fuel_purchases');
    }
};