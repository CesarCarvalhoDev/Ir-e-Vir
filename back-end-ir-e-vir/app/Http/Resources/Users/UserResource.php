<?php

namespace App\Http\Resources\Users;

use App\Http\Resources\Vehicles\VehicleResorce;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class UserResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'email' => $this->email,
            'role' => $this->role,
            'available_balance' => (float) $this->available_balance,
            'wallet' => $this->whenLoaded('wallet', fn () => [
                'id' => $this->wallet?->id,
                'balance' => (float) ($this->wallet?->balance ?? 0),
                'status' => $this->wallet?->status,
            ]),
            'vehicles' => VehicleResorce::collection($this->whenLoaded('vehicles')),
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
