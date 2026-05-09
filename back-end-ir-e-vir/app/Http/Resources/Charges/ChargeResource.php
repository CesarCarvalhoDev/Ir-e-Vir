<?php

namespace App\Http\Resources\Charges;

use App\Http\Resources\Stays\StayResource;
use App\Http\Resources\Vehicles\VehicleResorce;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ChargeResource extends JsonResource
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
            'value' => (float) $this->value,
            'status' => $this->status,
            'due_date' => $this->due_date,

            'stay_id' => $this->stay_id,

            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,


            'stay' => new StayResource($this->whenLoaded('stay')),

            'vehicle' => new VehicleResorce($this->whenLoaded('stay.vehicle'))
        ];
    }
}
