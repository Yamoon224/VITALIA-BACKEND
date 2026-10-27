<?php

namespace App\Domains\Assistant\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rules\In;

class DraftPromptVersionRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    /** @return array<string, mixed> */
    public function rules(): array
    {
        return [
            'block_key' => ['required', 'string', new In(['identity_and_scope', 'safety_rules', 'tone'])],
            'content' => ['required', 'string', 'max:5000'],
        ];
    }
}
