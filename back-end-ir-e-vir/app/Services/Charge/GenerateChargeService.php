<?php

namespace App\Services\Charge;

use App\Models\Charge;
use App\Models\Stay;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Carbon;

class GenerateChargeService
{
    public function execute(Stay $stay, float $totalValue, ?User $user = null): Charge
    {
        return DB::transaction(function () use ($stay, $totalValue, $user) {
            $responsibleUser = $user ?? $stay->vehicle?->users()->first();

            $charge = Charge::where('stay_id', $stay->id)
                ->lockForUpdate()
                ->first();

            if ($charge) {
                if (!$charge->user_id && $responsibleUser) {
                    $charge->update(['user_id' => $responsibleUser->id]);
                }

                return $charge;
            }

            $charge = new Charge();
            $charge->value = $totalValue;
            $charge->status = Charge::STATUS_PENDING;
            $charge->due_date = Carbon::now()->addMinutes(15);
            $charge->stay_id = $stay->id;
            $charge->user_id = $responsibleUser?->id;
            $charge->save();

            return $charge;
        });
    }
}
