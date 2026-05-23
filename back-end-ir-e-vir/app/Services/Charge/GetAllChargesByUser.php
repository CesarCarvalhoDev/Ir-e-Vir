<?php

namespace App\Services\Charge;

use App\Models\Charge;

class GetAllChargesByVehicle
{
    public function execute(User $user)
    {
        return DB::transaction(function () use ($user) {
            $charges = Charge::where("user_id", $user->id);
        }
        );
    }
}