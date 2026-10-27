<?php

namespace App\Domains\Consultation\Http\Resources;

use App\Models\ConsultationSession;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/** @mixin ConsultationSession */
class ConsultationSessionResource extends JsonResource
{
    /** @return array<string, mixed> */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'conversation_id' => $this->conversation_id,
            'motif' => $this->motif,
            'protocol_code' => $this->protocol_code,
            'answers' => $this->answers ?? [],
            'status' => $this->status,
            'summary' => $this->summary,
            'hypotheses' => $this->hypotheses,
            // Niveaux explicites (section 5.3) : urgence immédiate,
            // consultation rapide, consultation programmée, auto-soins.
            'orientation_level' => $this->orientation_level,
            'red_flags' => $this->red_flags,
            'completed_at' => $this->completed_at,
        ];
    }
}
