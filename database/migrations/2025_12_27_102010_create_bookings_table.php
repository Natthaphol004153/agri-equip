<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('bookings', function (Blueprint $table) {
            $table->id();
            $table->string('job_number')->unique(); // เลขที่ใบงาน เช่น JOB-20251227-001

            // เชื่อมโยง Foreign Keys
            $table->foreignId('customer_id')->constrained()->onDelete('restrict'); // ห้ามลบลูกค้าถ้ามีงานค้าง
            $table->foreignId('equipment_id')->constrained('equipment')->onDelete('restrict'); // ระบุชื่อตาราง equipment ให้ชัด
            $table->foreignId('assigned_staff_id')->nullable()->constrained('users'); // พนักงานรับผิดชอบ (nullable เผื่อยังไม่ระบุ)

            // ช่วงเวลาตามแผน (จอง)
            $table->dateTime('scheduled_start');
            $table->dateTime('scheduled_end');

            // ช่วงเวลาทำจริง (เก็บไว้ก่อน ยังไม่ได้ใช้ตอนจอง)
            $table->dateTime('actual_start')->nullable();
            $table->dateTime('actual_end')->nullable();

            // สถานะงาน
            $table->enum('status', [
                'scheduled',
                'in_progress',
                'completed_pending_approval',
                'completed',
                'cancelled',
                'paused'
            ])->default('scheduled');

            $table->decimal('total_price', 12, 2)->default(0); // ราคาประเมิน
            $table->enum('payment_status', ['pending', 'deposit_paid', 'paid', 'partial'])->default('pending');

            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('bookings');
    }
};
