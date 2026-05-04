<?php

namespace App\Services\User;

use App\Models\User;
use App\Models\Vehicle;
use Illuminate\Support\Facades\DB;

class LinkVehicleToUser
{
    public function execute(string $plate, string $type, User $user)
    {
        return DB::transaction( function () use($plate, $type, $user) {            
            $vehicle = Vehicle::where('plate', $plate)->first();

            if(!$vehicle){
                $vehicle = new Vehicle();
                $vehicle->plate = $plate;
                $vehicle->type = $type;
                $vehicle->has_registration = true;
              
                $vehicle->save();

            }
            $user->vehicles()->syncWithoutDetaching([$vehicle->id]);

            return $vehicle;
        });
    }
}