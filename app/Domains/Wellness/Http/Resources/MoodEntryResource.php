<?php

namespace App\Domains\Wellness\Http\Resources;

use App\Models\MoodEntry;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/** @mixin MoodEntry */
class MoodEntryResource extends JsonResource
{
    /** @return array<string, mixed> */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'mood_level' => $this->mood_level,
            'note' => $this->note,
            'recorded_at' => $this->recorded_at,
        ];
    }
}
