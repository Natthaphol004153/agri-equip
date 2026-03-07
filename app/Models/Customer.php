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
    'customer_code', 'name', 'phone', 'address', 'work_location_address', 'work_map_url', 'province', 'district', 'postal_code',
    'farm_area', 'latitude', 'longitude', 'work_latitude', 'work_longitude', 'customer_type', 'tax_id', 'password', 'profile_image'
];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'password' => 'hashed',
    ];
}