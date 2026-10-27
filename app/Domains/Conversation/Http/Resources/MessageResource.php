<?php

namespace App\Domains\Conversation\Http\Resources;

use App\Models\Message;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/** @mixin Message */
class MessageResource extends JsonResource
{
    /** @return array<string, mixed> */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'sender_type' => $this->sender_type,
            'content' => $this->content,
            'feedback' => $this->feedback,
            'created_at' => $this->created_at,
        ];
    }
}
