<?php

use App\Domains\Prescription\Enums\PrescriptionStatus;

/*
|--------------------------------------------------------------------------
| Machine à états stricte (section 5.7, 10.2)
|--------------------------------------------------------------------------
*/

it('allows draft to transition only to in_review', function (): void {
    expect(PrescriptionStatus::Draft->canTransitionTo(PrescriptionStatus::InReview))->toBeTrue()
        ->and(PrescriptionStatus::Draft->canTransitionTo(PrescriptionStatus::Signed))->toBeFalse()
        ->and(PrescriptionStatus::Draft->canTransitionTo(PrescriptionStatus::Refused))->toBeFalse();
});

it('allows in_review to transition to signed or refused only', function (): void {
    expect(PrescriptionStatus::InReview->canTransitionTo(PrescriptionStatus::Signed))->toBeTrue()
        ->and(PrescriptionStatus::InReview->canTransitionTo(PrescriptionStatus::Refused))->toBeTrue()
        ->and(PrescriptionStatus::InReview->canTransitionTo(PrescriptionStatus::Transmitted))->toBeFalse();
});

it('never allows leaving a terminal state', function (): void {
    expect(PrescriptionStatus::Refused->allowedNextStates())->toBe([])
        ->and(PrescriptionStatus::Transmitted->allowedNextStates())->toBe([]);
});

it('only signed can become transmitted', function (): void {
    expect(PrescriptionStatus::Signed->canTransitionTo(PrescriptionStatus::Transmitted))->toBeTrue();
});
