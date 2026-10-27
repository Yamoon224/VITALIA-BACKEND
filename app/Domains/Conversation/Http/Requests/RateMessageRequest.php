<?php

namespace App\Domains\Conversation\Http\Requests;

use App\Domains\Conversation\Enums\MessageFeedback;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rules\Enum;

class RateMessageRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    /** @return array<string, mixed> */
    public function rules(): array
    {
        return [
            'feedback' => ['required', new Enum(MessageFeedback::class)],
        ];
    }
}
