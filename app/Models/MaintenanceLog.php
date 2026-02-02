<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MaintenanceLog extends Model
{
    protected $fillable = [
        'equipment_id',
        'booking_id',
        'maintenance_type',
        'description',
        'status',
        'cost',
        'total_cost',
        'service_provider',
        'reset_counter',
        'image_url',
        'technician_name',
        'maintenance_date',
        'completion_date',
        
    ];

    public function equipment()
    {
        return $this->belongsTo(Equipment::class);
    }
}