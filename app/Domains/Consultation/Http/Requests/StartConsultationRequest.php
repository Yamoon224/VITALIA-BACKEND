<?php

namespace App\Domains\Consultation\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StartConsultationRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    /** @return array<string, mixed> */
    public function rules(): array
    {
        return [
            'motif' => ['nullable', 'string', 'max:255'],
        ];
    }
}
