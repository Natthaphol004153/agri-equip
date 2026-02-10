<?php

namespace App\Models;

// 1. ✅ เปลี่ยนการเรียกใช้ Class แม่
use Illuminate\Foundation\Auth\User as Authenticatable; 
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens; // ถ้าจะทำ API Login ต้องมีตัวนี้

// 2. ✅ เปลี่ยน extends จาก Model เป็น Authenticatable
class Customer extends Authenticatable
{
    use HasApiTokens, HasFactory, SoftDeletes, Notifiable;
    
    protected $fillable = [
        'customer_code', 
        'name', 
        'phone', 
        'address', 
        'latitude', 
        'longitude', 
        'customer_type', 
        'notes',
        'password', // ✅ ต้องเพิ่ม password เข้าไปใน fillable ด้วย
        'tax_id',   // ถ้ามีใน DB ก็เพิ่มไปครับ
        'email',    // ถ้ามีใน DB ก็เพิ่มไปครับ
        'district',
        'province',
        'postal_code'
    ];

    // ✅ ซ่อนรหัสผ่านไม่ให้แสดงตอนดึงข้อมูล
    protected $hidden = [
        'password',
        'remember_token',
    ];

    // ✅ แปลงประเภทข้อมูล (Optional)
    protected $casts = [
        'password' => 'hashed',
    ];
}