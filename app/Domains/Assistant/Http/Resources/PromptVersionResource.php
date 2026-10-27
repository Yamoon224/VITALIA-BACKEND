<?php

namespace App\Domains\Assistant\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/** @mixin \App\Models\PromptVersion */
class PromptVersionResource extends JsonResource
{
    /** @return array<string, mixed> */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'block_key' => $this->block_key,
            'version' => $this->version,
            'content' => $this->content,
            'status' => $this->status,
            'published_at' => $this->published_at,
            'created_at' => $this->created_at,
        ];
    }
}
