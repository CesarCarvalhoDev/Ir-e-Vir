<?php

namespace App\Services\Zone;

use App\Models\Zone;

class GetAllZonesService
{
    public function execute()
    {
        $zones = Zone::all();
        return $zones;
    }
}
