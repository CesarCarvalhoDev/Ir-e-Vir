<?php

namespace App\Services\Charge;

use App\Models\Charge;
use App\Models\User;
use Illuminate\Support\Facades\DB;

class GetAllChargesByUser
{
    public function execute(User $user)
    {
        return DB::transaction(function () use ($user) {
            $charges = Charge::where("user_id", $user->id);
        }
        );
    }
}
