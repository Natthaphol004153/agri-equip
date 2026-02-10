<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('bookings', function (Blueprint $table) {
            $table->id();
            $table->string('job_number')->unique(); // เลขที่ใบงาน

            // Foreign Keys
            $table->foreignId('customer_id')->constrained('customers')->onDelete('cascade');
            $table->foreignId('equipment_id')->constrained('equipment')->onDelete('cascade');
            $table->foreignId('assigned_staff_id')->nullable()->constrained('users')->onDelete('set null');

            // วันเวลา
            $table->dateTime('scheduled_start');
            $table->dateTime('scheduled_end');
            $table->dateTime('actual_start')->nullable();
            $table->dateTime('actual_end')->nullable();

            // --- 💰 ส่วนการเงิน (Money & Payment) ---
            $table->decimal('total_price', 10, 2);
            $table->decimal('deposit_amount', 10, 2)->default(0); // ยอดที่จ่ายมาแล้ว
            
            // สถานะการจ่ายเงิน
            $table->enum('payment_status', ['pending', 'deposit_paid', 'paid', 'cancelled'])->default('pending');
            
            // ช่องทางชำระเงิน (รองรับ Cash แล้ว!)
            $table->enum('payment_method', ['transfer', 'cash'])->nullable();
            
            // หลักฐาน
            $table->string('payment_proof')->nullable(); // รูปสลิป
            $table->string('payment_trans_ref')->nullable(); // เลขอ้างอิงธนาคาร

            // --- 🚜 ส่วนหน้างาน (Operation) ---
            $table->string('image_path')->nullable(); // รูปผลงาน (job_image)
            $table->text('note')->nullable();         // หมายเหตุ

            // สถานะงาน
            $table->enum('status', [
                'scheduled', 
                'in_progress', 
                'pending_approval',
                'completed_pending_approval', 
                'completed', 
                'cancelled'
            ])->default('scheduled');

            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('bookings');
    }
};