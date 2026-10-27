<?php

namespace App\Domains\Examination\Contracts;

use App\Models\ExaminationRequest;
use App\Models\User;
use Illuminate\Support\Collection;

interface ExaminationRequestRepositoryContract
{
    /** @param  array<int, array<string, mixed>>  $items */
    public function createDraft(User $patient, array $items, ?int $conversationId = null): ExaminationRequest;

    public function find(int $id): ?ExaminationRequest;

    /** @return Collection<int, ExaminationRequest> */
    public function pendingReview(): Collection;

    public function save(ExaminationRequest $request): ExaminationRequest;

    /**
     * Demandes d'examens du patient, les plus récentes d'abord (section 5.6,
     * « demandes d'examens » dans l'application mobile).
     *
     * @return Collection<int, ExaminationRequest>
     */
    public function forPatient(User $patient): Collection;
}
