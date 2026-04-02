<?php

namespace App\Services\Charge;

use App\Models\Charge;

class GetAllChargesService
{
    public function execute()
    {
        $charges = Charge::all();
        return $charges;
    }
}
