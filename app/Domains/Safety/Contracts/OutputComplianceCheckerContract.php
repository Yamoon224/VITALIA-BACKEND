<?php

namespace App\Domains\Safety\Contracts;

use App\Domains\Safety\DTOs\ComplianceResult;

/**
 * Contrôle après modèle (section 6, « Garde-fous en couches »).
 *
 * Vérifie qu'une réponse générée ne pose pas de diagnostic définitif, ne
 * propose pas de posologie hors circuit validé, et porte les avertissements
 * requis. Une sortie non conforme est rejetée ou régénérée, jamais affichée
 * telle quelle (section 6, « Sorties structurées »).
 */
interface OutputComplianceCheckerContract
{
    public function check(string $modelOutput): ComplianceResult;
}
