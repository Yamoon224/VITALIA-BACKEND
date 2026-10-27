<?php

namespace App\Domains\Prescription\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class RefusePrescriptionRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    /** @return array<string, mixed> */
    public function rules(): array
    {
        return [
            'reason' => ['required', 'string', 'max:1000'],
        ];
    }
}
