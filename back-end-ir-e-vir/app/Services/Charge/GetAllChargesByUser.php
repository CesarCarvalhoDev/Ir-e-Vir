<?php

namespace App\Services\Charge;

use App\Models\Charge;
use App\Models\User;

class GetAllChargesByUser
{
    public function execute(User $user)
    {
        return Charge::with(['stay.vehicle', 'payments', 'user'])
            ->where(function ($query) use ($user) {
                $query->where('user_id', $user->id)
                    ->orWhereHas('stay.vehicle.users', function ($subQuery) use ($user) {
                        $subQuery->where('users.id', $user->id);
                    });
            })
            ->get();
    }
}
