<?php

namespace App\Domains\Wellness\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class LogMoodRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    /** @return array<string, mixed> */
    public function rules(): array
    {
        return [
            'mood_level' => ['required', 'integer', 'min:1', 'max:5'],
            'note' => ['nullable', 'string', 'max:1000'],
        ];
    }
}
