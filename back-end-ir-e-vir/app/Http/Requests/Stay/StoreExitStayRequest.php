<?php

namespace App\Http\Requests\Stay;

use Illuminate\Foundation\Http\FormRequest;

class StoreExitStayRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'exit' => ['required', 'date'],
            'plate' => ['required', 'string', 'exists:vehicles,plate'],
        ];
    }
}
