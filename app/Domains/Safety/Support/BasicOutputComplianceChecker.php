<?php

namespace App\Domains\Safety\Support;

use App\Domains\Safety\Contracts\OutputComplianceCheckerContract;
use App\Domains\Safety\DTOs\ComplianceResult;
use Illuminate\Support\Str;

/**
 * Vérification minimale de conformité (section 6).
 *
 * Point de départ volontairement simple : refuse toute formulation de
 * diagnostic définitif. Les règles complètes (posologie hors circuit,
 * présence des avertissements) sont étendues avec le module `Prescription`
 * et le comité clinique (planning, phase 4).
 */
class BasicOutputComplianceChecker implements OutputComplianceCheckerContract
{
    /** @var list<string> */
    private const array FORBIDDEN_DEFINITIVE_PHRASES = [
        'vous avez', 'je diagnostique', 'c\'est certainement',
        'votre diagnostic est',
    ];

    public function check(string $modelOutput): ComplianceResult
    {
        $normalized = Str::lower($modelOutput);

        $violations = array_values(array_filter(
            self::FORBIDDEN_DEFINITIVE_PHRASES,
            fn (string $phrase): bool => str_contains($normalized, $phrase),
        ));

        return $violations === [] ? ComplianceResult::pass() : ComplianceResult::fail($violations);
    }
}
