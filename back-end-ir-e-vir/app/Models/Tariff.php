<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Tariff extends Model
{
    /** @use HasFactory<\Database\Factories\TariffFactory> */
    use HasFactory;

    protected $fillable = [
        'hourly_rate',
        'start_date',
        'end_date',
        'active',
        'zone_id',
    ];

    protected function casts(): array
    {
        return [
            'hourly_rate' => 'decimal:2',
            'active' => 'boolean',
            'start_date' => 'datetime',
            'end_date' => 'datetime',
        ];
    }

    public function zone()
    {
        return $this->belongsTo(Zone::class);
    }
}
