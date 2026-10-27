<?php

namespace App\Domains\Consultation\Http\Controllers;

use App\Domains\Consultation\Contracts\ConsultationRepositoryContract;
use App\Domains\Consultation\Http\Requests\AnswerProtocolQuestionRequest;
use App\Domains\Consultation\Http\Requests\StartConsultationRequest;
use App\Domains\Consultation\Http\Resources\ConsultationSessionResource;
use App\Domains\Consultation\Services\ConsultationProtocolService;
use App\Domains\Consultation\Services\ConsultationSynthesisService;
use App\Domains\Conversation\Contracts\ConversationRepositoryContract;
use App\Http\Controllers\Controller;
use App\Models\ConsultationSession;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

/**
 * Entretien structuré piloté par l'agent (section 5.3), adossé à une
 * conversation existante du domaine `Conversation` : ce contrôleur gère le
 * protocole question par question puis la synthèse finale.
 */
class ConsultationController extends Controller
{
    public function __construct(
        private readonly ConsultationProtocolService $protocol,
        private readonly ConsultationSynthesisService $consultations,
        private readonly ConversationRepositoryContract $conversations,
        private readonly ConsultationRepositoryContract $sessions,
    ) {}

    public function start(StartConsultationRequest $request, int $conversation): ConsultationSessionResource
    {
        $model = $this->conversations->findForUser($conversation, $request->user());

        abort_if($model === null, 404);

        $session = $this->protocol->start($request->user(), $model, $request->string('motif')->toString() ?: null);

        return new ConsultationSessionResource($session);
    }

    public function nextQuestion(Request $request, int $conversation): JsonResponse
    {
        $session = $this->activeSessionFor($request, $conversation);

        $question = $this->protocol->nextQuestion($session);

        return response()->json([
            'data' => $question === null ? null : ['code' => $question->code, 'text' => $question->text],
        ]);
    }

    public function answer(AnswerProtocolQuestionRequest $request, int $conversation): ConsultationSessionResource
    {
        $session = $this->activeSessionFor($request, $conversation);

        return new ConsultationSessionResource(
            $this->protocol->recordAnswer(
                $session,
                $request->string('question_code')->toString(),
                $request->string('answer')->toString(),
            ),
        );
    }

    public function finish(Request $request, int $conversation): ConsultationSessionResource
    {
        $model = $this->conversations->findForUser($conversation, $request->user());

        abort_if($model === null, 404);

        $session = $this->sessions->activeFor($model);

        abort_if($session === null, 404, 'Aucun entretien en cours pour cette conversation.');

        return new ConsultationSessionResource($this->consultations->finish($session, $model->load('messages')));
    }

    private function activeSessionFor(Request $request, int $conversationId): ConsultationSession
    {
        $model = $this->conversations->findForUser($conversationId, $request->user());

        abort_if($model === null, 404);

        $session = $this->sessions->activeFor($model);

        abort_if($session === null, 404, 'Aucun entretien en cours pour cette conversation.');

        return $session;
    }
}
