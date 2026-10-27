<?php

namespace App\Domains\Prescription\Http\Resources;

use App\Models\Prescription;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/** @mixin Prescription */
class PrescriptionResource extends JsonResource
{
    /** @return array<string, mixed> */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'status' => $this->status,
            'items' => $this->items,
            'safety_alerts' => $this->safety_alerts,
            'refusal_reason' => $this->refusal_reason,
            'signed_at' => $this->signed_at,
            'transmitted_at' => $this->transmitted_at,
        ];
    }
}
