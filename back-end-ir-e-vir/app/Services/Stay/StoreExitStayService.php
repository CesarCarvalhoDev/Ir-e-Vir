<?php

namespace App\Services\Stay;

use Exception;

use App\Models\Stay;
use App\Models\User;
use App\Models\Vehicle;

use App\Services\Fine\GenerateFineService;

use App\Services\Wallet\DeductBalanceService;
use App\Services\Wallet\ValidateBalanceService;

class StoreExitStayService
{
    public function __construct(
        private CalculateStayAmountService $calculateStayAmountService,

        private ValidateBalanceService $validateBalanceService,

        private DeductBalanceService $deductBalanceService,

        private GenerateFineService $generateFineService
    ) {}

    public function execute(array $data)
    {
        $vehicle = Vehicle::where(
            'plate',
            $data['plate']
        )->firstOrFail();

        $user = User::findOrFail(
            $vehicle->user_id
        );

        $wallet = $user->wallet;

        if (!$wallet) {
            throw new Exception(
                'Usuário não possui carteira.'
            );
        }

        $stay = Stay::where(
            'vehicle_id',
            $vehicle->id
        )
        ->where(
            'status',
            Stay::STATUS_ACTIVE
        )
        ->first();

        if (!$stay) {
            throw new Exception(
                'Nenhuma permanência ativa encontrada.'
            );
        }

        $amount = $this
            ->calculateStayAmountService
            ->execute($stay);

        $hasBalance = $this
            ->validateBalanceService
            ->execute($wallet, $amount);

        if (!$hasBalance) {

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

        $this->deductBalanceService
            ->execute($wallet, $amount);

        $stay->update([
            'status' => Stay::STATUS_FINISHED,
            'exit_time' => now(),
        ]);

        return [
            'message' => 'Saída registrada com sucesso.',
            'amount_paid' => $amount,
            'remaining_balance' => $wallet
                ->fresh()
                ->balance,
        ];
    }
}
