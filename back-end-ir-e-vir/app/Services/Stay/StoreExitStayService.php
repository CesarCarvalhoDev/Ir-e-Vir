<?php

namespace App\Services\Stay;

use Exception;

use App\Models\Stay;
use App\Models\User;
use App\Models\Vehicle;

use App\Services\Charge\GenerateChargeService;
use App\Services\Charge\PayChargeWithWalletService;
use App\Services\Fine\GenerateFineService;

class StoreExitStayService
{
    public function __construct(
        private CalculateStayAmountService $calculateStayAmountService,

        private GenerateChargeService $generateChargeService,

        private PayChargeWithWalletService $payChargeWithWalletService,

        private GenerateFineService $generateFineService
    ) {}

    public function execute(array $data)
    {
        $vehicle = Vehicle::where('plate', $data['plate'])->firstOrFail();

        $user = $vehicle->users()->first();

        if (!$user) {
            throw new Exception(
                'Veículo não está vinculado a nenhum usuário.'
            );
        }

        $wallet = $user->wallet;

        if (!$wallet) {
            throw new Exception(
                'Usuário não possui carteira.'
            );
        }

        $stay = Stay::where('vehicle_id', $vehicle->id)
            ->where('status', Stay::STATUS_ACTIVE)
            ->first();

        if (!$stay) {
            throw new Exception(
                'Nenhuma permanência ativa encontrada.'
            );
        }

        $amount = $this
            ->calculateStayAmountService
            ->execute($stay);

        $charge = $this->generateChargeService->execute(
            $stay,
            $amount
        );

        try {
            $payment = $this->payChargeWithWalletService->execute(
                $charge,
                $wallet
            );

            $stay->update([
                'status' => Stay::STATUS_FINISHED,
                'exit_time' => now(),
            ]);

            return [
                'message' => 'Saída registrada com sucesso.',
                'amount_paid' => $amount,
                'charge_id' => $charge->id,
                'payment_id' => $payment->id,
                'remaining_balance' => $wallet->fresh()->balance,
            ];
        } catch (Exception $ex) {
            $this->generateFineService->execute(
                $user,
                $stay,
                $amount
            );

            $stay->update([
                'status' => Stay::STATUS_IRREGULAR,
                'exit_time' => now(),
            ]);

            throw new Exception(
                'Saldo insuficiente. Multa gerada.'
            );
        }
    }
}
