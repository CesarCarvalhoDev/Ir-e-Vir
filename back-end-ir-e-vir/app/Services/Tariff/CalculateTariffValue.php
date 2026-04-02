<?php

namespace App\Services\Tariff;

use App\Models\Stay;
use App\Models\Tariff;

class CalculateTariffValue
{
    public function execute(Stay $stay)
    {
        $totalTime = $stay->totalTime;

        $tariff = Tariff::where('zoneId', $stay->zoneId)->where('startDate', '<=', $stay->entry)->where('endDate', '>=', $stay->entry)->where('active', true)->first();
        $hourlyRate = $tariff->hourlyRate;

        $totalValue = ($totalTime / 60) * $hourlyRate;

        return $totalValue;
    }
}
