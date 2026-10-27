<?php

use App\Domains\Safety\Enums\UrgencyLevel;
use App\Domains\Safety\Support\KeywordEmergencyDetector;

/*
|--------------------------------------------------------------------------
| Zéro faux négatif toléré sur les cas critiques (section 12.1)
|--------------------------------------------------------------------------
*/

it('detects a cardiac emergency signal', function (): void {
    $assessment = (new KeywordEmergencyDetector)->assess("J'ai une douleur thoracique très forte depuis 10 minutes.");

    expect($assessment->isEmergency())->toBeTrue()
        ->and($assessment->level)->toBe(UrgencyLevel::Emergency)
        ->and($assessment->matchedSignals)->toContain('douleur thoracique');
});

it('detects a mental health crisis and attaches the crisis protocol', function (): void {
    $assessment = (new KeywordEmergencyDetector)->assess("Je n'en peux plus, j'ai envie de mourir.");

    expect($assessment->isEmergency())->toBeTrue()
        ->and($assessment->mentalHealthProtocol)->toBe('crisis_listening');
});

it('does not raise an emergency for an ordinary wellness question', function (): void {
    $assessment = (new KeywordEmergencyDetector)->assess("Quels aliments m'aideraient à mieux dormir ?");

    expect($assessment->isEmergency())->toBeFalse()
        ->and($assessment->level)->toBe(UrgencyLevel::SelfCareWithMonitoring);
});

it('is accent and case insensitive', function (): void {
    $assessment = (new KeywordEmergencyDetector)->assess('PERTE DE CONNAISSANCE après être tombé.');

    expect($assessment->isEmergency())->toBeTrue();
});
