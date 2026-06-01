<?php

namespace App\Http\Requests\Stay;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class StoreEntryStayRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'entry' => ['required', 'date'],
            'plate' => ['required', 'string', 'exists:vehicles,plate'],
            'zone_id' => ['required', 'integer', 'exists:zones,id'],
        ];
    }
}
