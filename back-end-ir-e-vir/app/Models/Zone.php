<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Zone extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'maximum_time',
    ];

    protected $hidden = [

    ];

    protected function casts() : array {
        return [

        ];
    }

    public function tariffs()
    {
        return $this->hasMany(Tariff::class);
    }

    public function stays()
    {
        return $this->hasMany(Stay::class);
    }
}
