<?php

namespace App\Domains\Examination\Events;

use App\Models\ExaminationResult;
use Illuminate\Foundation\Events\Dispatchable;

/**
 * Toute valeur critique déclenche une recommandation de consultation rapide
 * et une alerte au praticien référent s'il existe (section 5.6).
 */
class CriticalResultRecorded
{
    use Dispatchable;

    public function __construct(public readonly ExaminationResult $result) {}
}
