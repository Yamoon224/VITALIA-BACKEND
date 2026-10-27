<?php

namespace App\Domains\Prescription\Repositories;

use App\Domains\Prescription\Contracts\PrescriptionRepositoryContract;
use App\Domains\Prescription\Enums\PrescriptionStatus;
use App\Models\Prescription;
use App\Models\User;
use Illuminate\Support\Collection;

class EloquentPrescriptionRepository implements PrescriptionRepositoryContract
{
    /**
     * @param  array<int, array<string, mixed>>  $items
     * @param  array<int, array<string, mixed>>  $safetyAlerts
     */
    public function createDraft(User $patient, array $items, array $safetyAlerts = []): Prescription
    {
        return Prescription::query()->create([
            'user_id' => $patient->id,
            'status' => PrescriptionStatus::Draft,
            'items' => $items,
            'safety_alerts' => $safetyAlerts,
        ]);
    }

    public function find(int $id): ?Prescription
    {
        return Prescription::query()->find($id);
    }

    /** @return Collection<int, Prescription> */
    public function pendingReview(): Collection
    {
        return Prescription::query()
            ->where('status', PrescriptionStatus::InReview)
            ->oldest('updated_at')
            ->get();
    }

    public function save(Prescription $prescription): Prescription
    {
        $prescription->save();

        return $prescription;
    }

    /** @return Collection<int, Prescription> */
    public function forPatient(User $patient): Collection
    {
        // Tri par id plutôt que par `created_at` : deux ordonnances créées à
        // la même seconde ne doivent jamais revenir dans un ordre
        // indéterminé (section 5.14, « ordonnances reçues »).
        return Prescription::query()
            ->where('user_id', $patient->id)
            ->latest('id')
            ->get();
    }
}
