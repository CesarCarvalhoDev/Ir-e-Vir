<?php

namespace App\Services\Charge;

use Exception;
use App\Models\Charge;
use App\Models\Wallet;
use App\Models\Payment;
use App\Services\Wallet\ValidateBalanceService;
use App\Services\Wallet\DeductBalanceService;

class PayChargeWithWalletService
{
    public function __construct(
        private ValidateBalanceService $validateBalanceService,
        private DeductBalanceService $deductBalanceService
    ) {}

    public function execute(Charge $charge, Wallet $wallet): Payment
    {
        $hasBalance = $this->validateBalanceService->execute(
            $wallet,
            $charge->value
        );

        if (!$hasBalance) {
            throw new Exception(
                'Saldo insuficiente na carteira para pagar esta cobrança.'
            );
        }

        $this->deductBalanceService->execute($wallet, $charge->value);

        $charge->update(['status' => Charge::STATUS_PAID]);

        $payment = Payment::create([
            'paid_value' => $charge->value,
            'payment_date' => now(),
            'status' => Payment::STATUS_COMPLETED,
            'charges_id' => $charge->id,
        ]);

        return $payment;
    }
}
