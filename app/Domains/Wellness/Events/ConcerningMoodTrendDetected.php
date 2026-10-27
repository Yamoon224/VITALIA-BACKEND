<?php

namespace App\Domains\Wellness\Events;

use App\Models\User;
use Illuminate\Foundation\Events\Dispatchable;

/**
 * Repérage de tendances préoccupantes et suggestion de consulter
 * (section 5.9). N'est jamais un diagnostic ni une alerte d'urgence : c'est
 * le domaine `Safety` qui gère les signaux de crise (section 5.4).
 */
class ConcerningMoodTrendDetected
{
    use Dispatchable;

    public function __construct(public readonly User $user, public readonly float $averageMoodLevel) {}
}
