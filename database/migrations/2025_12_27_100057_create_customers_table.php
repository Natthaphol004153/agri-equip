<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('customers', function (Blueprint $table) {
            $table->id();
            $table->string('customer_code')->unique()->nullable();
            $table->string('name');
            $table->string('phone')->unique();
            
            // ✅ เพิ่มฟิลด์รูปโปรไฟล์
            $table->string('profile_image')->nullable(); 

            $table->string('email')->nullable();
            $table->string('tax_id')->nullable();
            
            $table->text('address')->nullable();
            $table->string('province')->nullable();
            $table->string('district')->nullable();
            $table->string('postal_code')->nullable();

            $table->decimal('latitude', 10, 8)->nullable();
            $table->decimal('longitude', 11, 8)->nullable();

            $table->enum('customer_type', ['individual', 'farm', 'company'])->default('individual');

            $table->string('password');
            $table->rememberToken();

            $table->text('notes')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('customers');
    }
};