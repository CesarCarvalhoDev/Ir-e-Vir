<?php

namespace App\Http\Resources\Tariffs;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class TariffResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'hourly_rate' => (float) $this->hourly_rate,
            'start_date' => $this->start_date,
            'end_date' => $this->end_date,
            'active' => (bool) $this->active,
            'zone_id' => $this->zone_id,
            'zone' => $this->when($this->relationLoaded('zone'), fn () => [
                'id' => $this->zone?->id,
                'name' => $this->zone?->name,
                'maximum_time' => $this->zone?->maximum_time,
            ]),
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
