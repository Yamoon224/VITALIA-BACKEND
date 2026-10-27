<?php

namespace App\Domains\Examination\Repositories;

use App\Domains\Examination\Contracts\ExaminationRequestRepositoryContract;
use App\Domains\Examination\Enums\ExaminationRequestStatus;
use App\Models\ExaminationRequest;
use App\Models\User;
use Illuminate\Support\Collection;

class EloquentExaminationRequestRepository implements ExaminationRequestRepositoryContract
{
    public function createDraft(User $patient, array $items, ?int $conversationId = null): ExaminationRequest
    {
        return ExaminationRequest::query()->create([
            'user_id' => $patient->id,
            'conversation_id' => $conversationId,
            'status' => ExaminationRequestStatus::Draft,
            'items' => $items,
        ]);
    }

    public function find(int $id): ?ExaminationRequest
    {
        return ExaminationRequest::query()->find($id);
    }

    /** @return Collection<int, ExaminationRequest> */
    public function pendingReview(): Collection
    {
        return ExaminationRequest::query()
            ->where('status', ExaminationRequestStatus::Draft)
            ->oldest('updated_at')
            ->get();
    }

    public function save(ExaminationRequest $request): ExaminationRequest
    {
        $request->save();

        return $request;
    }

    /** @return Collection<int, ExaminationRequest> */
    public function forPatient(User $patient): Collection
    {
        // Tri par id plutôt que par `created_at` (section 5.6, « demandes
        // d'examens ») : deux demandes créées à la même seconde ne doivent
        // jamais revenir dans un ordre indéterminé.
        return ExaminationRequest::query()
            ->where('user_id', $patient->id)
            ->latest('id')
            ->get();
    }
}
