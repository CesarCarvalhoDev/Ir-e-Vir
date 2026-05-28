<?php

namespace App\Services\Wallet;

use App\Models\Wallet;

class ValidateBalanceService
{
    public function execute(
        Wallet $wallet,
        float $amount
    ): bool {

        if ($wallet->status === Wallet::STATUS_BLOCKED) {
            return false;
        }

        return $wallet->balance >= $amount;
    }
}
