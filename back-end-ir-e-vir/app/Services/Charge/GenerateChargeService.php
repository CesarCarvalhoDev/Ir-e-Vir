<?php

namespace App\Services\Charge;

use App\Models\Charge;
use App\Models\Stay;
use Illuminate\Support\Carbon;

class GenerateChargeService
{
    public function execute(Stay $stay, float $totalValue)
    {
        try {
            $existingCharge = Charge::where('stay_id', $stay->id)
                ->where('status', 'PENDING')
                ->first();
            
            if ($existingCharge) {
                throw new \Exception("Já existe uma cobrança pendente para esta estadia.");
            }

            $charge = new Charge();
            $charge->value = $totalValue;
            $charge->status = "PENDING";
            $charge->due_date = Carbon::now()->addMinutes(15);
            $charge->stay_id = $stay->id;
            $charge->save();

            return $charge;
        } catch (\Exception $ex) {
            throw new \Exception($ex->getMessage());
        }
    }
}
