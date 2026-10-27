<?php

namespace App\Domains\Notification\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateNotificationPreferenceRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    /** @return array<string, mixed> */
    public function rules(): array
    {
        return [
            'push_enabled' => ['sometimes', 'boolean'],
            'sms_enabled' => ['sometimes', 'boolean'],
            'whatsapp_enabled' => ['sometimes', 'boolean'],
            'email_enabled' => ['sometimes', 'boolean'],
            'quiet_hours_start' => ['nullable', 'integer', 'min:0', 'max:23'],
            'quiet_hours_end' => ['nullable', 'integer', 'min:0', 'max:23'],
        ];
    }
}
