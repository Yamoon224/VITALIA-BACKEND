<?php

namespace App\Domains\Prescription\Events;

use App\Models\Prescription;
use Illuminate\Foundation\Events\Dispatchable;

/**
 * Événement de domaine (section 9.1) : déclenche notification au patient,
 * rappels de prise et entrée d'audit, sans que le domaine `Prescription` ait
 * à connaître `Notification` ni `Audit` (section 10.2, pattern Observateur).
 */
class PrescriptionSigned
{
    use Dispatchable;

    public function __construct(public readonly Prescription $prescription) {}
}
