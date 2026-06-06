<?php

namespace App\Services\Stay;

use Exception;

use App\Models\Stay;
use App\Models\Vehicle;
use App\Models\Fine;
use Carbon\Carbon;

use App\Services\Charge\GenerateChargeService;
use App\Services\Charge\PayChargeWithWalletService;
use App\Services\Fine\GenerateFineService;
use App\Services\Tariff\CalculateTariffValue;
use Illuminate\Support\Facades\DB;

class StoreExitStayService
{
    public function __construct(
        private CalculateTariffValue $calculateTariffValue,
        private GenerateChargeService $generateChargeService,
        private PayChargeWithWalletService $payChargeWithWalletService,
        private GenerateFineService $generateFineService
    ) {}

    public function execute(array $data)
    {
        $result = DB::transaction(function () use ($data) {
            
            $vehicle = Vehicle::where('plate', $data['plate'])
                ->lockForUpdate()
                ->firstOrFail();

            $user = $vehicle->users()->first();

            if (!$user) {
                throw new Exception('Veículo não está vinculado a nenhum usuário.');
            }

            $wallet = $user->wallet;

            if (!$wallet) {
                throw new Exception('Usuário não possui carteira.');
            }

            $stay = Stay::with('zone')
                ->where('vehicle_id', $vehicle->id)
                ->where('status', Stay::STATUS_ACTIVE)
                ->lockForUpdate()
                ->first();

            if (!$stay) {
                throw new Exception('Nenhuma permanência ativa encontrada.');
            }

            $exit = Carbon::parse($data['exit']);

            if ($exit->lt($stay->entry)) {
                throw new Exception('A saída não pode ser anterior à entrada.');
            }

            $stay->exit = $exit;
            $stay->total_time = $stay->entry->diffInMinutes($exit);

            $amount = $this->calculateTariffValue->execute($stay);

            $charge = $this->generateChargeService->execute(
                $stay,
                $amount,
                $user
            );

            $timeLimitFine = null;

            if ($stay->zone && $stay->total_time > $stay->zone->maximum_time) {
                $timeLimitFine = $this->generateFineService->execute(
                    $user,
                    $stay,
                    $amount,
                    Fine::REASON_TIME_LIMIT_EXCEEDED
                );
            }

            try {
                $payment = $this->payChargeWithWalletService->execute(
                    $charge,
                    $wallet
                );

                $stay->update([
                    'status' => Stay::STATUS_FINISHED,
                    'exit' => $exit,
                    'total_time' => $stay->total_time,
                ]);

                return [
                    'success' => true,
                    'message' => 'Saída registrada com sucesso.',
                    'amount_paid' => $amount,
                    'charge_id' => $charge->id,
                    'payment_id' => $payment->id,
                    'time_limit_fine_id' => $timeLimitFine?->id,
                    'remaining_balance' => $wallet->fresh()->balance,
                ];
            } catch (Exception $ex) {
                if (!str_contains($ex->getMessage(), 'Saldo insuficiente')) {
                    throw $ex;
                }

                $balanceFine = $this->generateFineService->execute(
                    $user,
                    $stay,
                    $amount,
                    Fine::REASON_INSUFFICIENT_BALANCE
                );

                $stay->update([
                    'status' => Stay::STATUS_IRREGULAR,
                    'exit' => $exit,
                    'total_time' => $stay->total_time,
                ]);

                return [
                    'success' => false,
                    'message' => 'Saldo insuficiente. Multa gerada.',
                    'charge_id' => $charge->id,
                    'balance_fine_id' => $balanceFine->id,
                    'time_limit_fine_id' => $timeLimitFine?->id,
                ];
            }
        });

        if (!$result['success']) {
            throw new Exception($result['message']);
        }

        unset($result['success']);

        return $result;
    }
}
