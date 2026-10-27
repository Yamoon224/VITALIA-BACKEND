<?php

namespace App\Domains\HealthRecord\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateHealthProfileRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    /** @return array<string, mixed> */
    public function rules(): array
    {
        return [
            'birth_year' => ['nullable', 'integer', 'min:1900', 'max:'.date('Y')],
            'sex' => ['nullable', 'string', 'in:female,male,other'],
            'height_cm' => ['nullable', 'integer', 'min:30', 'max:260'],
            'weight_kg' => ['nullable', 'integer', 'min:1', 'max:400'],
            'allergies' => ['nullable', 'array'],
            'allergies.*' => ['string', 'max:255'],
            'chronic_conditions' => ['nullable', 'array'],
            'chronic_conditions.*' => ['string', 'max:255'],
            'current_medications' => ['nullable', 'array'],
            'current_medications.*' => ['string', 'max:255'],
            'is_pregnant' => ['nullable', 'boolean'],
            'lifestyle_notes' => ['nullable', 'string', 'max:2000'],
        ];
    }
}
