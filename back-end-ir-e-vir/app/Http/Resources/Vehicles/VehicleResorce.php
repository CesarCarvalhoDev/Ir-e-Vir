<?php

namespace App\Http\Resources\Vehicles;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class VehicleResorce extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,

            'plate' => $this->plate,
            'type' => $this->type,

            'has_registration' => (bool) $this->hasRegistration,
            'available_balance' => (float) $this->availableBalance,

            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
