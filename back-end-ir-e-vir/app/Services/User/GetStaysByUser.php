<?php

namespace App\Services\User;

use App\Models\Stay;
use App\Models\User;

class GetStaysByUser
{
    public function execute(User $user)
    {
        return Stay::whereHas('vehicle.users', function ($query) use ($user) {
            $query->where('users.id', $user->id);
        })->get();
    }
}
