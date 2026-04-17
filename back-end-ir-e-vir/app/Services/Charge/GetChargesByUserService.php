<?php

namespace App\Services\Charge;

use App\Models\Charge;
use App\Models\User;
use Illuminate\Database\Eloquent\Collection;

class GetChargesByUserService
{
    public function execute(User $user): Collection
    {
        return Charge::query()
            ->whereIn('stay_id', $user->vehicles()->with('stays')->get()->pluck('stays.*.id')->flatten())
            ->get();
    }
}
