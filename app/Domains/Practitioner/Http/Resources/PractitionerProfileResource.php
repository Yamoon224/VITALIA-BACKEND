<?php

namespace App\Domains\Practitioner\Http\Resources;

use App\Models\PractitionerProfile;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/** @mixin PractitionerProfile */
class PractitionerProfileResource extends JsonResource
{
    /** @return array<string, mixed> */
    public function toArray(Request $request): array
    {
        return [
            'user_id' => $this->user_id,
            'name' => $this->user->name,
            'specialty' => $this->specialty,
            'bio' => $this->bio,
            'languages' => $this->languages ?? [],
            'consultation_fee_cents' => $this->consultation_fee_cents,
            'verified' => $this->isVerified(),
        ];
    }
}
