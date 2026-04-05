<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Camera extends Model
{
    protected $fillable = [
        'location',
        'zoneId'
    ];

    public function zone()
    {
        return $this->belongsTo(Zone::class, 'zoneId');
    }
}