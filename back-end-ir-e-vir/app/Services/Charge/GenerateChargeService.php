<?php

namespace App\Services\Charge;

use App\Models\Charge;
use App\Models\Stay;
use Illuminate\Support\Carbon;

class GenerateChargeService
{
    public function execute(Stay $stay, float $totalValue)
    {
        $charge = new Charge();
        $charge->value = $totalValue;
        $charge->status = "PENDING";
        $charge->due_date = Carbon::now()->addMinutes(15);
        $charge->stay_id = $stay->id;
        $charge->save();

        return $charge;
    }
}
