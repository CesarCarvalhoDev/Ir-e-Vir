<?php

namespace App\Services\Tariff;

use App\Models\Tariff;
use Illuminate\Support\Facades\DB;

class CreateTariffService
{
    public function execute(array $data): Tariff
    {
        return DB::transaction(function () use ($data) {
            return Tariff::create([
                'hourly_rate' => $data['hourly_rate'],
                'start_date' => $data['start_date'],
                'end_date' => $data['end_date'],
                'active' => $data['active'],
                'zone_id' => $data['zone_id'],
            ])->load('zone');
        });
    }
}
