<?php

namespace App\Domains\Examination\Http\Resources;

use App\Models\ExaminationResult;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/** @mixin ExaminationResult */
class ExaminationResultResource extends JsonResource
{
    /** @return array<string, mixed> */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'parameter' => $this->parameter,
            'value' => $this->value,
            'unit' => $this->unit,
            'reference_low' => $this->reference_low,
            'reference_high' => $this->reference_high,
            'is_abnormal' => $this->is_abnormal,
            'recorded_at' => $this->recorded_at,
        ];
    }
}
