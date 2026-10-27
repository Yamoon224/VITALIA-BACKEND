<?php

namespace App\Domains\Consultation\Support;

use App\Domains\Consultation\DTOs\ConsultationSynthesis;
use App\Domains\Safety\Enums\UrgencyLevel;

/**
 * Valide la sortie JSON du modèle contre le schéma attendu (section 6,
 * « Sorties structurées ») : une sortie non conforme est rejetée, jamais
 * affichée telle quelle — `validate()` retourne `null` plutôt que de
 * laisser passer une forme inattendue.
 */
class SynthesisValidator
{
    public function validate(string $rawJson): ?ConsultationSynthesis
    {
        $decoded = json_decode($this->stripCodeFence($rawJson), true);

        if (! is_array($decoded)) {
            return null;
        }

        if (! $this->hasValidShape($decoded)) {
            return null;
        }

        $orientation = UrgencyLevel::tryFrom($decoded['orientation_level']);

        if ($orientation === null) {
            return null;
        }

        return new ConsultationSynthesis(
            summary: $decoded['summary'],
            hypotheses: array_map(
                fn (array $h): array => ['condition' => $h['condition'], 'confidence' => (float) $h['confidence']],
                $decoded['hypotheses'],
            ),
            orientationLevel: $orientation,
            redFlags: $decoded['red_flags'],
        );
    }

    /** @param  array<string, mixed>  $decoded */
    private function hasValidShape(array $decoded): bool
    {
        if (! isset($decoded['summary'], $decoded['hypotheses'], $decoded['orientation_level'], $decoded['red_flags'])) {
            return false;
        }

        if (! is_string($decoded['summary']) || ! is_string($decoded['orientation_level'])) {
            return false;
        }

        if (! is_array($decoded['hypotheses']) || ! is_array($decoded['red_flags'])) {
            return false;
        }

        foreach ($decoded['hypotheses'] as $hypothesis) {
            if (! is_array($hypothesis) || ! isset($hypothesis['condition'], $hypothesis['confidence'])) {
                return false;
            }

            if (! is_string($hypothesis['condition']) || ! is_numeric($hypothesis['confidence'])) {
                return false;
            }
        }

        foreach ($decoded['red_flags'] as $flag) {
            if (! is_string($flag)) {
                return false;
            }
        }

        return true;
    }

    /** Le modèle entoure parfois le JSON d'un bloc de code Markdown. */
    private function stripCodeFence(string $raw): string
    {
        $trimmed = trim($raw);

        return preg_replace('/^```(?:json)?\s*|\s*```$/', '', $trimmed) ?? $trimmed;
    }
}
