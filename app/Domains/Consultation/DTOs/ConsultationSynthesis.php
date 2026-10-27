<?php

namespace App\Domains\Consultation\DTOs;

use App\Domains\Safety\Enums\UrgencyLevel;

/**
 * Synthèse structurée en fin d'entretien (section 5.3) : résumé clinique,
 * hypothèses plausibles avec niveau d'incertitude, signes d'alerte,
 * niveau d'orientation. Produite par le modèle puis validée côté serveur
 * contre ce schéma avant toute persistance (section 6, « Sorties
 * structurées »).
 */
final readonly class ConsultationSynthesis
{
    /**
     * @param  list<array{condition: string, confidence: float}>  $hypotheses
     * @param  list<string>  $redFlags
     */
    public function __construct(
        public string $summary,
        public array $hypotheses,
        public UrgencyLevel $orientationLevel,
        public array $redFlags,
    ) {}
}
