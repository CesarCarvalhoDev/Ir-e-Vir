<?php

namespace App\Services\Tariff;

use App\Models\Stay;
use App\Models\Tariff;

class CalculateTariffValue
{
    public function execute(Stay $stay)
    {
        $tariff = Tariff::where('zone_id', $stay->zone_id)
            ->where('start_date', '<=', $stay->entry)
            ->where('end_date', '>=', $stay->entry)
            ->where('active', true)
            ->first();

        if (!$tariff) {
            throw new \Exception('Tarifa não encontrada');
        }

        $totalTime = $stay->total_time;

        $totalValue = round(($totalTime / 60) * $tariff->hourly_rate, 2);

        return $totalValue;
    }
}
