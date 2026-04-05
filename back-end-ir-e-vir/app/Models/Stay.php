<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Stay extends Model
{
    protected $fillable = [
        'entry',
        'exit',
        'totalTime',
        'status',
        'vehicleId',
        'zoneId'
    ];

    protected $casts = [
        'entry' => 'datetime',
        'exit' => 'datetime',
    ];

    public function vehicle()
    {
        return $this->belongsTo(Vehicle::class, 'vehicleId');
    }
}