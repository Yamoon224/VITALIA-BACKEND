<?php

namespace App\Domains\Examination\Support;

/**
 * Même garde que `SynthesisValidator` du domaine `Consultation` (section 6,
 * « Sorties structurées ») : une sortie non conforme est rejetée, jamais
 * transformée en brouillon d'examens.
 */
class ExaminationRecommendationValidator
{
    /** @return list<array{name: string, reason: string, prep_instructions: string}>|null */
    public function validate(string $rawJson): ?array
    {
        $decoded = json_decode($this->stripCodeFence($rawJson), true);

        if (! is_array($decoded)) {
            return null;
        }

        if ($decoded === []) {
            return [];
        }

        $items = [];

        foreach ($decoded as $item) {
            if (! is_array($item) || ! isset($item['name'], $item['reason'])) {
                return null;
            }

            if (! is_string($item['name']) || ! is_string($item['reason'])) {
                return null;
            }

            $items[] = [
                'name' => $item['name'],
                'reason' => $item['reason'],
                'prep_instructions' => is_string($item['prep_instructions'] ?? null) ? $item['prep_instructions'] : '',
            ];
        }

        return $items;
    }

    private function stripCodeFence(string $raw): string
    {
        $trimmed = trim($raw);

        return preg_replace('/^```(?:json)?\s*|\s*```$/', '', $trimmed) ?? $trimmed;
    }
}
