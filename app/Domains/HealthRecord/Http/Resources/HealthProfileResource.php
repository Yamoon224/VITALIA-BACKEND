<?php

namespace App\Domains\HealthRecord\Http\Resources;

use App\Models\HealthProfile;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/** @mixin HealthProfile */
class HealthProfileResource extends JsonResource
{
    /** @return array<string, mixed> */
    public function toArray(Request $request): array
    {
        return [
            'birth_year' => $this->birth_year,
            'sex' => $this->sex,
            'height_cm' => $this->height_cm,
            'weight_kg' => $this->weight_kg,
            'allergies' => $this->allergies ?? [],
            'chronic_conditions' => $this->chronic_conditions ?? [],
            'current_medications' => $this->current_medications ?? [],
            'is_pregnant' => $this->is_pregnant,
            'lifestyle_notes' => $this->lifestyle_notes,
            'updated_at' => $this->updated_at,
        ];
    }
}
