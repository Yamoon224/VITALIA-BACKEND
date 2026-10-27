<?php

namespace App\Domains\Safety\Events;

use App\Models\EmergencyDetection;
use Illuminate\Foundation\Events\Dispatchable;

/**
 * Événement de domaine (section 9.1, section 10.2 pattern Observateur).
 *
 * Découple la détection de ses effets secondaires : le domaine `Audit`
 * journalise, le domaine `Notification` peut alerter le référent clinique,
 * sans que `Safety` connaisse ni l'un ni l'autre.
 */
class EmergencyDetected
{
    use Dispatchable;

    public function __construct(public readonly EmergencyDetection $detection) {}
}
