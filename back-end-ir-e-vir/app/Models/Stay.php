<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Stay extends Model
{
    use HasFactory;

    protected $fillable = [
        'entry',
        'exit',
        'vehicle_id',
        'zone_id',
        'total_time',
        'status',
    ];

    protected $hidden = [];

    protected function casts(): array
    {
        return [
            'entry' => 'datetime',
            'exit' => 'datetime',

        ];
    }

    public const STATUS_ACTIVE = 'active';
    public const STATUS_FINISHED = 'finished';
    public const STATUS_IRREGULAR = 'irregular';

    public function charges()
    {
        return $this->hasMany(Charge::class);
    }

    public function vehicle()
    {
        return $this->belongsTo(Vehicle::class);
    }
}
