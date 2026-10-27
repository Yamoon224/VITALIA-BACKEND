<?php

use App\Domains\Consultation\Support\SynthesisValidator;
use App\Domains\Safety\Enums\UrgencyLevel;

it('parses a well-formed synthesis', function (): void {
    $json = json_encode([
        'summary' => 'Céphalée légère depuis deux jours, sans signe de gravité.',
        'hypotheses' => [['condition' => 'Céphalée de tension', 'confidence' => 0.6]],
        'orientation_level' => 'self_care_with_monitoring',
        'red_flags' => ['fièvre élevée associée'],
    ]);

    $synthesis = (new SynthesisValidator)->validate($json);

    expect($synthesis)->not->toBeNull()
        ->and($synthesis->orientationLevel)->toBe(UrgencyLevel::SelfCareWithMonitoring)
        ->and($synthesis->hypotheses[0]['condition'])->toBe('Céphalée de tension');
});

it('strips a markdown code fence around the JSON payload', function (): void {
    $json = "```json\n".json_encode([
        'summary' => 'Résumé.',
        'hypotheses' => [],
        'orientation_level' => 'scheduled_consultation',
        'red_flags' => [],
    ])."\n```";

    expect((new SynthesisValidator)->validate($json))->not->toBeNull();
});

it('rejects a payload with an unknown orientation level', function (): void {
    $json = json_encode([
        'summary' => 'Résumé.',
        'hypotheses' => [],
        'orientation_level' => 'not_a_real_level',
        'red_flags' => [],
    ]);

    expect((new SynthesisValidator)->validate($json))->toBeNull();
});

it('rejects a payload missing required fields', function (): void {
    expect((new SynthesisValidator)->validate('{"summary": "incomplet"}'))->toBeNull();
});

it('rejects anything that is not valid JSON', function (): void {
    expect((new SynthesisValidator)->validate('ceci n\'est pas du JSON'))->toBeNull();
});
