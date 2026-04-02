<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Stay extends Model
{
    use HasFactory;

    protected $fillable = [];

    protected $hidden = [];

    protected function casts(): array
    {
        return [];
    }

    public function charges()
    {
        return $this->hasMany(Charge::class);
    }

    public function vehicle()
    {
        return $this->belongsTo(Vehicle::class);
    }
}
