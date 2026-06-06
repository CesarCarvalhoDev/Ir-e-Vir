<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Charge extends Model
{
    use HasFactory;

    protected $fillable = [
        'value',
        'status',
        'due_date',
        'stay_id',
        'user_id',
    ];

    protected $hidden = [
        'Stay_id'
    ];

    protected $casts = [
        'value' => 'decimal:2',
        'due_date' => 'datetime',
    ];

    public const STATUS_PENDING = 'PENDING';
    public const STATUS_PAID = 'PAID';
    public const STATUS_OVERDUE = 'OVERDUE';

    public function stay(): BelongsTo
    {
        return $this->belongsTo(Stay::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function payments(): HasMany
    {
        return $this->hasMany(Payment::class, 'charges_id');
    }
}
