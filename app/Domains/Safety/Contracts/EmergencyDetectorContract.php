<?php

namespace App\Domains\Safety\Contracts;

use App\Domains\Safety\DTOs\EmergencyAssessment;

/**
 * Détection indépendante du modèle (section 3, 5.4, 14).
 *
 * Implémentée en code déterministe, jamais en s'appuyant uniquement sur le
 * prompt du LLM (« défense en profondeur », section 3.1). Appelée avant et
 * après chaque réponse de l'agent par `SafetyPipeline`.
 */
interface EmergencyDetectorContract
{
    public function assess(string $message): EmergencyAssessment;
}
