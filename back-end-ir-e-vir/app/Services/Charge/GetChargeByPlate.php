<?php

namespace App\Services\Charge;

use App\Models\Charge;

class GetChargeByPlate
{
    public function execute(string $plate)
    {
        $charge = Charge::whereHas('stay.vehicle', function ($q) use ($plate) {
            $q->where('plate', $plate);
        })
            ->latest()
            ->first();
        return $charge;
    }
}
