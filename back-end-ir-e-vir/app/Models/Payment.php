<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Payment extends Model
{
    use HasFactory;

    protected $fillable = [
        'paid_value',
        'payment_date',
        'status',
        'charges_id',
    ];

    protected $casts = [
        'paid_value' => 'decimal:2',
        'payment_date' => 'datetime',
    ];

    public const STATUS_COMPLETED = 'completed';
    public const STATUS_FAILED = 'failed';
    public const STATUS_PENDING = 'pending';

    public function charge(): BelongsTo
    {
        return $this->belongsTo(Charge::class, 'charges_id');
    }
}
