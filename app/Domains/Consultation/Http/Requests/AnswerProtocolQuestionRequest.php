<?php

namespace App\Domains\Consultation\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class AnswerProtocolQuestionRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    /** @return array<string, mixed> */
    public function rules(): array
    {
        return [
            'question_code' => ['required', 'string', 'max:100'],
            'answer' => ['required', 'string', 'max:2000'],
        ];
    }
}
