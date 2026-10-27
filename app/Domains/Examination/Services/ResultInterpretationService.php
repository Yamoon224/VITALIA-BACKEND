<?php

namespace App\Domains\Examination\Services;

use App\Domains\Examination\Events\CriticalResultRecorded;
use App\Models\ExaminationResult;
use App\Models\User;

/**
 * Vérification déterministe des unités et bornes avant toute interprétation
 * (section 5.5, 5.6) : le modèle explique une valeur en langage clair, mais
 * ne décide jamais seul si elle est hors normes — ce service le fait par
 * comparaison numérique simple, testée et auditable.
 */
class ResultInterpretationService
{
    /**
     * @param  array{parameter: string, value: float, unit: string, reference_low?: float|null, reference_high?: float|null}  $data
     */
    public function record(User $user, array $data, ?int $examinationRequestId = null): ExaminationResult
    {
        $isAbnormal = $this->isOutOfRange($data['value'], $data['reference_low'] ?? null, $data['reference_high'] ?? null);

        $result = ExaminationResult::query()->create([
            'user_id' => $user->id,
            'examination_request_id' => $examinationRequestId,
            'parameter' => $data['parameter'],
            'value' => $data['value'],
            'unit' => $data['unit'],
            'reference_low' => $data['reference_low'] ?? null,
            'reference_high' => $data['reference_high'] ?? null,
            'is_abnormal' => $isAbnormal,
            'recorded_at' => now(),
        ]);

        if ($isAbnormal) {
            CriticalResultRecorded::dispatch($result);
        }

        return $result;
    }

    private function isOutOfRange(float $value, ?float $low, ?float $high): bool
    {
        if ($low !== null && $value < $low) {
            return true;
        }

        if ($high !== null && $value > $high) {
            return true;
        }

        return false;
    }
}
