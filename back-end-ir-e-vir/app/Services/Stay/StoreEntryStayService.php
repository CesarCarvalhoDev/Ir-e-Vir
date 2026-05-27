<?php

namespace App\Services\Stay;

use App\Models\Stay;
use Exception;

class StoreEntryStayService
{
    public function execute(array $data)
    {
        $stayOpen = Stay::where('vehicle_id', $data['vehicle_id'])
            ->whereNull('exit')
            ->where('status', 'OPEN')
            ->first();

        if ($stayOpen) {
            throw new Exception('Veículo já possui permanência aberta.');
        }

        $stay = Stay::create([
            'entry' => $data['entry'],
            'vehicle_id' => $data['vehicle_id'],
            'zone_id' => $data['zone_id'],
            'status' => 'OPEN',
        ]);

        return $stay;
    }
}
