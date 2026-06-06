<?php

namespace App\Services\Charge;

use Exception;
use App\Models\Charge;
use App\Models\Wallet;
use App\Models\Payment;
use App\Services\Wallet\ValidateBalanceService;
use App\Services\Wallet\DeductBalanceService;
use Illuminate\Support\Facades\DB;

class PayChargeWithWalletService
{
    public function __construct(
        private ValidateBalanceService $validateBalanceService,
        private DeductBalanceService $deductBalanceService
    ) {}

    public function execute(Charge $charge, Wallet $wallet): Payment
    {
        return DB::transaction(function () use ($charge, $wallet) {
            $lockedCharge = Charge::whereKey($charge->id)
                ->lockForUpdate()
                ->firstOrFail();

            if ($lockedCharge->status === Charge::STATUS_PAID) {
                $payment = $lockedCharge->payments()
                    ->where('status', Payment::STATUS_COMPLETED)
                    ->latest()
                    ->first();

                if ($payment) {
                    return $payment;
                }

                throw new Exception('Cobrança já está paga.');
            }

            $lockedWallet = Wallet::whereKey($wallet->id)
                ->lockForUpdate()
                ->firstOrFail();

            $hasBalance = $this->validateBalanceService->execute(
                $lockedWallet,
                $lockedCharge->value
            );

            if (!$hasBalance) {
                throw new Exception(
                    'Saldo insuficiente na carteira para pagar esta cobrança.'
                );
            }

            $this->deductBalanceService->execute($lockedWallet, $lockedCharge->value);

            $lockedCharge->update(['status' => Charge::STATUS_PAID]);

            return Payment::create([
                'paid_value' => $lockedCharge->value,
                'payment_date' => now(),
                'status' => Payment::STATUS_COMPLETED,
                'charges_id' => $lockedCharge->id,
            ]);
        });
    }
}
