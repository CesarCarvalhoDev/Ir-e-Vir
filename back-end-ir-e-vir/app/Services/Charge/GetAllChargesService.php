<?php

namespace App\Services\Charge;

use App\Models\Charge;

class GetAllChargesService
{
    public function execute()
    {
        $charges = Charge::with(['stay.vehicle', 'payments', 'user'])->get();
        return $charges;
    }
}
