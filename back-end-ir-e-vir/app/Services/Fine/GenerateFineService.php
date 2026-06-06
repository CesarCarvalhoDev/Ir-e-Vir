<?php

namespace App\Services\Fine;

use App\Models\Fine;
use App\Models\Stay;
use App\Models\User;

class GenerateFineService
{
    public function execute(
        User $user,
        Stay $stay,
        float $amount,
        string $reason
    ): Fine {

        $existingFine = Fine::where('stay_id', $stay->id)
            ->where('reason', $reason)
            ->where('status', Fine::STATUS_ACTIVE)
            ->first();

        if ($existingFine) {
            return $existingFine;
        }

        return Fine::create([
            'user_id' => $user->id,
            'stay_id' => $stay->id,

            'amount' => $amount,

            'reason' => $reason,

            'status' => Fine::STATUS_ACTIVE,

            'started_at' => now(),
        ]);
    }
}
