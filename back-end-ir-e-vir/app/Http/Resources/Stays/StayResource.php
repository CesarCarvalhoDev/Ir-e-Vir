<?php

namespace App\Http\Resources\Stays;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use App\Http\Resources\Vehicles\VehicleResorce;

class StayResource extends JsonResource
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

            'entry' => $this->entry,
            'exit' => $this->exit,
            'total_time' => $this->total_time,

            'status' => $this->status,

            'vehicle_id' => $this->vehicle_id,
            'zone_id' => $this->zone_id,

            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at
        ];
    }
}
