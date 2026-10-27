<?php

namespace App\Domains\Prescription\Services;

use App\Domains\Prescription\Contracts\PrescriptionRepositoryContract;
use App\Domains\Prescription\Enums\PrescriptionStatus;
use App\Domains\Prescription\Events\PrescriptionSigned;
use App\Domains\Prescription\Exceptions\InvalidPrescriptionTransitionException;
use App\Models\Prescription;
use App\Models\User;
use Illuminate\Support\Str;

/**
 * Seul point d'écriture du cycle de vie d'une ordonnance (section 5.7).
 *
 * Verrou technique : `sign()` est la seule méthode de tout le système qui
 * peut amener une ordonnance à l'état `Signed`, et elle exige un praticien
 * (section 5.7, « impossibilité technique de contourner » ; section 11,
 * idempotence des opérations sensibles).
 */
class PrescriptionReviewService
{
    public function __construct(private readonly PrescriptionRepositoryContract $prescriptions) {}

    public function submitForReview(Prescription $prescription): Prescription
    {
        $this->transition($prescription, PrescriptionStatus::InReview);

        return $this->prescriptions->save($prescription);
    }

    public function sign(Prescription $prescription, User $practitioner): Prescription
    {
        if (! $practitioner->hasRole('practitioner')) {
            throw InvalidPrescriptionTransitionException::from($prescription->status, PrescriptionStatus::Signed);
        }

        $this->transition($prescription, PrescriptionStatus::Signed);

        $prescription->practitioner_id = $practitioner->id;
        $prescription->signed_at = now();
        $prescription->signature_hash = hash('sha256', $prescription->id.'|'.$practitioner->id.'|'.now()->toISOString().'|'.Str::uuid());

        $saved = $this->prescriptions->save($prescription);

        PrescriptionSigned::dispatch($saved);

        return $saved;
    }

    public function refuse(Prescription $prescription, User $practitioner, string $reason): Prescription
    {
        $this->transition($prescription, PrescriptionStatus::Refused);

        $prescription->practitioner_id = $practitioner->id;
        $prescription->refusal_reason = $reason;

        return $this->prescriptions->save($prescription);
    }

    public function markTransmitted(Prescription $prescription): Prescription
    {
        $this->transition($prescription, PrescriptionStatus::Transmitted);

        $prescription->transmitted_at = now();

        return $this->prescriptions->save($prescription);
    }

    private function transition(Prescription $prescription, PrescriptionStatus $next): void
    {
        if (! $prescription->status->canTransitionTo($next)) {
            throw InvalidPrescriptionTransitionException::from($prescription->status, $next);
        }

        $prescription->status = $next;
    }
}
