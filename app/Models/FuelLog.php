<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FuelLog extends Model
{
    use HasFactory;

    protected $fillable = [
        'equipment_id',
        'user_id',
        'fuel_source', // เพิ่ม
        'fuel_tank_id', // เพิ่ม
        'amount',
        'liters',
        'mileage',
        'image_path',
        'note',
        'refill_date',
    ];

    public function equipment()
    {
        return $this->belongsTo(Equipment::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function tank()
    {
        return $this->belongsTo(FuelTank::class, 'fuel_tank_id');
    }
}