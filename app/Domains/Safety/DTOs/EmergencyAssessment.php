<?php

namespace App\Domains\Safety\DTOs;

use App\Domains\Safety\Enums\UrgencyLevel;

/**
 * Résultat d'une détection d'urgence (section 3, 5.4).
 *
 * Immuable et sérialisable tel quel dans le journal d'audit (section 3.1,
 * « traçabilité totale ») : `matchedSignals` explique *pourquoi* le niveau a
 * été retenu, indispensable pour la revue du référent clinique.
 */
final readonly class EmergencyAssessment
{
    /** @param  list<string>  $matchedSignals */
    public function __construct(
        public UrgencyLevel $level,
        public array $matchedSignals = [],
        public ?string $mentalHealthProtocol = null,
    ) {}

    public static function none(): self
    {
        return new self(UrgencyLevel::SelfCareWithMonitoring);
    }

    public function isEmergency(): bool
    {
        return $this->level->requiresImmediateEscalation();
    }
}
