<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FuelPurchase extends Model
{
    use HasFactory;

    protected $fillable = [
        'fuel_tank_id',
        'liters',
        'price_per_liter',
        'total_cost',
        'purchase_date',
        'supplier',
        'note',
    ];

    public function tank()
    {
        return $this->belongsTo(FuelTank::class, 'fuel_tank_id');
    }
}