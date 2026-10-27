<?php

namespace App\Domains\Wellness\Http\Requests;

use App\Domains\Wellness\Enums\WellnessGoalType;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rules\Enum;

class SetWellnessGoalRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    /** @return array<string, mixed> */
    public function rules(): array
    {
        return [
            'type' => ['required', new Enum(WellnessGoalType::class)],
            'target_value' => ['required', 'numeric', 'min:0'],
        ];
    }
}
