<?php

namespace App\Services\Stay;

use App\Models\Stay;
use App\Models\Vehicle;
use Exception;

class StoreEntryStayService
{
    public function execute(array $data)
    {
        $currentVehicle = Vehicle::where('plate', $data['plate'])->first();

        $stayOpen = Stay::where('vehicle_id', $currentVehicle->id)
            ->whereNull('exit')
            ->where('status', 'OPEN')
            ->first();

        if ($stayOpen) {
            throw new Exception('Veículo já possui permanência aberta.');
        }

        $stay = Stay::create([
            'entry' => $data['entry'],
            'vehicle_id' => $currentVehicle->id,
            'zone_id' => $data['zone_id']
        ]);

        return $stay;
    }
}
