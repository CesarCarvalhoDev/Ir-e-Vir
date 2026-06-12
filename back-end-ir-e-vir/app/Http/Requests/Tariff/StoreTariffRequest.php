<?php

namespace App\Http\Requests\Tariff;

use Illuminate\Foundation\Http\FormRequest;

class StoreTariffRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'hourly_rate' => ['required', 'numeric', 'min:0.01'],
            'start_date' => ['required', 'date'],
            'end_date' => ['required', 'date', 'after_or_equal:start_date'],
            'active' => ['required', 'boolean'],
            'zone_id' => ['required', 'integer', 'exists:zones,id'],
        ];
    }
}
