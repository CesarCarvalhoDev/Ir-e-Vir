<?php

namespace App\Services\Wallet;

use App\Models\Wallet;

class DeductBalanceService
{
    public function execute(
        Wallet $wallet,
        float $amount
    ): void {

        $wallet->decrement('balance', $amount);
    }
}
