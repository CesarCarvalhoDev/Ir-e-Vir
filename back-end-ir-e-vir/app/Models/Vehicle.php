<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Vehicle extends Model
{
    use HasFactory;

    protected $hidden = [];

    protected $fillable = [
        'plate',
        'type'
    ];

    protected function casts(): array
    {
        return [];
    }

    public function users()
    {
        return $this->belongsToMany(User::class);
    }
}
