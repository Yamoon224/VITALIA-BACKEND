<?php

namespace App\Domains\Conversation\Http\Resources;

use App\Models\Conversation;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/** @mixin Conversation */
class ConversationResource extends JsonResource
{
    /** @return array<string, mixed> */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'topic' => $this->topic,
            'status' => $this->status,
            'created_at' => $this->created_at,
            'messages' => MessageResource::collection($this->whenLoaded('messages')),
        ];
    }
}
