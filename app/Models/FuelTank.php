<?php

namespace App\Models;
use App\Models\FuelPurchase;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FuelTank extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'capacity',
        'current_balance',
        'average_price',
    ];

    // ประวัติการเติมเข้ารถ
    public function logs()
    {
        return $this->hasMany(FuelLog::class);
    }
    
    // ประวัติการซื้อน้ำมันเข้าถัง
    public function purchases()
    {
        return $this->hasMany(FuelPurchase::class);
    }
}