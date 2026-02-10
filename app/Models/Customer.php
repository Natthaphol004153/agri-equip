<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable; 
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

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
        'password',
        'tax_id',
        'email',
        'district',
        'province',
        'postal_code',
        'profile_image' // ✅ เพิ่มบรรทัดนี้
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'password' => 'hashed',
    ];
}