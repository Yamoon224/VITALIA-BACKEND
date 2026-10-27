<?php

namespace App\Domains\Examination\Services;

use App\Domains\Examination\Contracts\ExaminationRequestRepositoryContract;
use App\Domains\Examination\Enums\ExaminationRequestStatus;
use App\Domains\Examination\Events\ExaminationValidated;
use App\Domains\Examination\Exceptions\InvalidExaminationTransitionException;
use App\Models\ExaminationRequest;
use App\Models\User;

/**
 * Émission d'une prescription d'examens officielle uniquement après
 * signature (section 5.6) : seule `validate()` peut amener une demande à
 * l'état `Validated`, et exige un praticien.
 */
class ExaminationReviewService
{
    public function __construct(private readonly ExaminationRequestRepositoryContract $requests) {}

    public function validate(ExaminationRequest $request, User $practitioner): ExaminationRequest
    {
        if (! $practitioner->hasRole('practitioner')) {
            throw InvalidExaminationTransitionException::from($request->status, ExaminationRequestStatus::Validated);
        }

        $this->transition($request, ExaminationRequestStatus::Validated);

        $request->practitioner_id = $practitioner->id;
        $request->validated_at = now();

        $saved = $this->requests->save($request);

        ExaminationValidated::dispatch($saved);

        return $saved;
    }

    public function refuse(ExaminationRequest $request, User $practitioner, string $reason): ExaminationRequest
    {
        $this->transition($request, ExaminationRequestStatus::Refused);

        $request->practitioner_id = $practitioner->id;
        $request->refusal_reason = $reason;

        return $this->requests->save($request);
    }

    private function transition(ExaminationRequest $request, ExaminationRequestStatus $next): void
    {
        if (! $request->status->canTransitionTo($next)) {
            throw InvalidExaminationTransitionException::from($request->status, $next);
        }

        $request->status = $next;
    }
}
