<?php

namespace App\Domains\Safety\Services;

use App\Domains\Safety\Contracts\EmergencyDetectorContract;
use App\Domains\Safety\Contracts\OutputComplianceCheckerContract;
use App\Domains\Safety\DTOs\ComplianceResult;
use App\Domains\Safety\DTOs\EmergencyAssessment;
use App\Domains\Safety\Events\EmergencyDetected;
use App\Models\Conversation;
use App\Models\EmergencyDetection;
use App\Models\User;

/**
 * Chaîne de responsabilité exécutée avant et après chaque appel au modèle
 * (section 6, « Garde-fous en couches » ; section 10.2, pattern Pipeline).
 *
 * Le niveau d'urgence détecté prime sur toute autre logique de conversation
 * (section 5.4) : `AssistantService` doit interrompre le flux normal dès que
 * `before()` renvoie une évaluation d'urgence, avant même d'appeler le LLM.
 */
class SafetyPipeline
{
    public function __construct(
        private readonly EmergencyDetectorContract $emergencyDetector,
        private readonly OutputComplianceCheckerContract $outputChecker,
    ) {}

    public function before(User $user, Conversation $conversation, string $incomingMessage): EmergencyAssessment
    {
        $assessment = $this->emergencyDetector->assess($incomingMessage);

        if ($assessment->isEmergency()) {
            $detection = EmergencyDetection::query()->create([
                'user_id' => $user->id,
                'conversation_id' => $conversation->id,
                'level' => $assessment->level,
                'matched_signals' => $assessment->matchedSignals,
                'mental_health_protocol' => $assessment->mentalHealthProtocol,
                'message_excerpt' => mb_substr($incomingMessage, 0, 500),
            ]);

            EmergencyDetected::dispatch($detection);
        }

        return $assessment;
    }

    public function after(string $modelOutput): ComplianceResult
    {
        return $this->outputChecker->check($modelOutput);
    }
}
