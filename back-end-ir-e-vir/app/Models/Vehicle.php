<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Vehicle extends Model
{
    protected $fillable = [
        'plate',
        'type',
        'hasRegistration',
        'availableBalance'
    ];

    public function stays()
    {
        return $this->hasMany(Stay::class, 'vehicleId');
    }
}