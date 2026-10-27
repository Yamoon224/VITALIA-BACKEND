<?php

namespace App\Domains\Examination\Http\Resources;

use App\Models\ExaminationRequest;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/** @mixin ExaminationRequest */
class ExaminationRequestResource extends JsonResource
{
    /** @return array<string, mixed> */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'status' => $this->status,
            'items' => $this->items,
            'refusal_reason' => $this->refusal_reason,
            'validated_at' => $this->validated_at,
        ];
    }
}
