<?php

namespace App\Domains\Examination\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class RecordExaminationResultRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    /** @return array<string, mixed> */
    public function rules(): array
    {
        return [
            'parameter' => ['required', 'string', 'max:255'],
            'value' => ['required', 'numeric'],
            'unit' => ['required', 'string', 'max:50'],
            'reference_low' => ['nullable', 'numeric'],
            'reference_high' => ['nullable', 'numeric'],
            'examination_request_id' => ['nullable', 'integer', 'exists:examination_requests,id'],
        ];
    }
}
