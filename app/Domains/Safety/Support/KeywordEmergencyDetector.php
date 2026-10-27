<?php

namespace App\Domains\Safety\Support;

use App\Domains\Safety\Contracts\EmergencyDetectorContract;
use App\Domains\Safety\DTOs\EmergencyAssessment;
use App\Domains\Safety\Enums\UrgencyLevel;
use Illuminate\Support\Str;

/**
 * Première couche de détection : motifs cliniques de gravité (section 5.4).
 *
 * Volontairement simple et lisible plutôt qu'exhaustif au stade du socle :
 * la liste de signaux et le classifieur de gravité qui la complète
 * (section 5.4) sont affinés par le comité clinique (phase 4 du planning)
 * et rejoués contre le « golden set » (section 6, 12) avant toute évolution.
 * Zéro faux négatif toléré sur les cas critiques du jeu de référence
 * (section 12.1).
 */
class KeywordEmergencyDetector implements EmergencyDetectorContract
{
    /** @var list<string> */
    private const array CRITICAL_SIGNALS = [
        'douleur thoracique', 'douleur dans la poitrine',
        'difficulté à respirer', 'détresse respiratoire', 'étouffe',
        'paralysie', 'visage tordu', 'avc',
        'saignement abondant', 'hémorragie',
        'perte de connaissance', 'évanoui', 'inconscient',
        'convulsion', 'crise d\'épilepsie',
        'réaction allergique sévère', 'anaphylaxie', 'gonflement de la gorge',
    ];

    /** @var list<string> */
    private const array MENTAL_HEALTH_SIGNALS = [
        'envie de mourir', 'me suicider', 'suicide', 'en finir avec la vie',
        'me faire du mal',
    ];

    public function assess(string $message): EmergencyAssessment
    {
        $normalized = Str::lower(Str::ascii($message));

        $matched = array_values(array_filter(
            self::CRITICAL_SIGNALS,
            fn (string $signal): bool => str_contains($normalized, Str::ascii($signal)),
        ));

        if ($matched !== []) {
            return new EmergencyAssessment(UrgencyLevel::Emergency, $matched);
        }

        $mentalHealthMatch = array_values(array_filter(
            self::MENTAL_HEALTH_SIGNALS,
            fn (string $signal): bool => str_contains($normalized, Str::ascii($signal)),
        ));

        if ($mentalHealthMatch !== []) {
            return new EmergencyAssessment(
                UrgencyLevel::Emergency,
                $mentalHealthMatch,
                mentalHealthProtocol: 'crisis_listening',
            );
        }

        return EmergencyAssessment::none();
    }
}
