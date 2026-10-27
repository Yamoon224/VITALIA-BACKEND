<?php

namespace App\Domains\Wellness\Enums;

/** Objectifs et suivi (section 5.9) : poids, pas, sommeil, hydratation. */
enum WellnessGoalType: string
{
    case Weight = 'weight';
    case Steps = 'steps';
    case SleepHours = 'sleep_hours';
    case HydrationLiters = 'hydration_liters';
}
