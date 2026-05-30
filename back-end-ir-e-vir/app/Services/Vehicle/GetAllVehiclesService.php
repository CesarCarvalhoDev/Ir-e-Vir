<?php

namespace App\Services\Vehicle;

use App\Models\Vehicle;

class GetAllVehiclesService
{
    public function execute()
    {
        $vehicles = Vehicle::all();
        return $vehicles;
    }
}
