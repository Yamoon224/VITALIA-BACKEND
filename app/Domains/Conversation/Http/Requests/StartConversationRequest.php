<?php

namespace App\Domains\Conversation\Http\Requests;

use App\Domains\Conversation\Enums\ConversationTopic;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rules\Enum;

class StartConversationRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    /** @return array<string, mixed> */
    public function rules(): array
    {
        return [
            'topic' => ['required', new Enum(ConversationTopic::class)],
        ];
    }
}
