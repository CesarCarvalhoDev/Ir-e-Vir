<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Fine extends Model
{
    protected $fillable = [
        'user_id',
        'stay_id',
        'amount',
        'reason',
        'status',
        'started_at',
        'resolved_at',
        'paid_at',
    ];

    protected $casts = [
        'amount' => 'decimal:2',

        'started_at' => 'datetime',
        'resolved_at' => 'datetime',
        'paid_at' => 'datetime',
    ];

    public const STATUS_ACTIVE = 'active';
    public const STATUS_PAID = 'paid';
    public const STATUS_RESOLVED = 'resolved';
    public const STATUS_CANCELED = 'canceled';


    public const REASON_INSUFFICIENT_BALANCE = 'insufficient_balance';
    public const REASON_TIME_LIMIT_EXCEEDED = 'time_limit_exceeded';

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function stay(): BelongsTo
    {
        return $this->belongsTo(Stay::class);
    }
}
