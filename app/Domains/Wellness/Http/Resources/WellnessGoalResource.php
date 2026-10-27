<?php

namespace App\Domains\Wellness\Http\Resources;

use App\Models\WellnessGoal;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/** @mixin WellnessGoal */
class WellnessGoalResource extends JsonResource
{
    /** @return array<string, mixed> */
    public function toArray(Request $request): array
    {
        return [
            'type' => $this->type,
            'target_value' => $this->target_value,
            'current_value' => $this->current_value,
        ];
    }
}
