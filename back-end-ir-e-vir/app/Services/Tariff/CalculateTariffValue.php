<?php

namespace App\Services\Tariff;

use App\Models\Stay;
use App\Models\Tariff;

class CalculateTariffValue
{
    public function execute(Stay $stay): float
    {
        if ($stay->total_time === null) {
            throw new \Exception('Tempo total da permanência não foi calculado.');
        }

        $tariff = Tariff::where('zone_id', $stay->zone_id)
            ->where('start_date', '<=', $stay->entry)
            ->where(function ($query) use ($stay) {
                $query->whereNull('end_date')
                    ->orWhere('end_date', '>=', $stay->entry);
            })
            ->where('active', true)
            ->orderByDesc('start_date')
            ->first();

        if (!$tariff) {
            throw new \Exception('Tarifa não encontrada');
        }

        return round(($stay->total_time / 60) * (float) $tariff->hourly_rate, 2);
    }
}
