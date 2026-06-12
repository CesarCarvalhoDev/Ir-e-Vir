<?php

namespace App\Services\Tariff;

use App\Models\Tariff;

class GetAllTariffsService
{
    public function execute()
    {
        return Tariff::with('zone')
            ->orderByDesc('start_date')
            ->get();
    }
}
