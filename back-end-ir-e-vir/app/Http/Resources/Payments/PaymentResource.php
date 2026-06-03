<?php

namespace App\Http\Resources\Payments;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class PaymentResource extends JsonResource
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
            'paid_value' => (float) $this->paid_value,
            'payment_date' => $this->payment_date,
            'status' => $this->status,
            'charges_id' => $this->charges_id,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
