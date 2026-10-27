<?php

namespace App\Domains\Prescription\Contracts;

use App\Models\Prescription;
use App\Models\User;
use Illuminate\Support\Collection;

interface PrescriptionRepositoryContract
{
    /**
     * @param  array<int, array<string, mixed>>  $items
     * @param  array<int, array<string, mixed>>  $safetyAlerts
     */
    public function createDraft(User $patient, array $items, array $safetyAlerts = []): Prescription;

    public function find(int $id): ?Prescription;

    /**
     * Ordonnances en attente de revue (section 5.13, « file de revue »).
     *
     * @return Collection<int, Prescription>
     */
    public function pendingReview(): Collection;

    /**
     * Ordonnances du patient, les plus récentes d'abord (section 5.14,
     * « ordonnances reçues » dans l'application mobile).
     *
     * @return Collection<int, Prescription>
     */
    public function forPatient(User $patient): Collection;

    public function save(Prescription $prescription): Prescription;
}
