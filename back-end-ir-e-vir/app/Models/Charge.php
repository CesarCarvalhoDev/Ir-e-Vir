<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Charge extends Model
{
    use HasFactory;

    protected $fillable = [
        'value',
        'status',
        'due_date',
        'stay_id'
    ];

    protected $hidden = [
        'Stay_id'
    ];

    protected function casts(): array
    {
        return [
            'due_date' => 'datetime',
        ];
    }

    public function stay()
    {
        return $this->belongsTo(Stay::class);
    }
}
