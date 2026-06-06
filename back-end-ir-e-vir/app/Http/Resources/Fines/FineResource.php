<?php

namespace App\Http\Resources\Fines;

use App\Http\Resources\Stays\StayResource;
use App\Http\Resources\Vehicles\VehicleResorce;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class FineResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'user_id' => $this->user_id,
            'stay_id' => $this->stay_id,
            'amount' => (float) $this->amount,
            'reason' => $this->reason,
            'status' => $this->status,
            'started_at' => $this->started_at,
            'resolved_at' => $this->resolved_at,
            'paid_at' => $this->paid_at,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
            'user' => $this->when($this->relationLoaded('user'), fn () => [
                'id' => $this->user?->id,
                'name' => $this->user?->name,
                'email' => $this->user?->email,
                'role' => $this->user?->role,
            ]),
            'stay' => $this->when(
                $this->relationLoaded('stay'),
                fn () => new StayResource($this->stay)
            ),
            'vehicle' => $this->when(
                $this->relationLoaded('stay') && $this->stay?->relationLoaded('vehicle'),
                fn () => new VehicleResorce($this->stay->vehicle)
            ),
        ];
    }
}
