<?php

namespace App\Domains\Conversation\Http\Controllers;

use App\Domains\Conversation\Contracts\ConversationRepositoryContract;
use App\Domains\Conversation\Enums\ConversationTopic;
use App\Domains\Conversation\Enums\MessageFeedback;
use App\Domains\Conversation\Http\Requests\RateMessageRequest;
use App\Domains\Conversation\Http\Requests\SendMessageRequest;
use App\Domains\Conversation\Http\Requests\StartConversationRequest;
use App\Domains\Conversation\Http\Resources\ConversationResource;
use App\Domains\Conversation\Http\Resources\MessageResource;
use App\Domains\Conversation\Services\ConversationService;
use App\Domains\Conversation\Services\MessageFeedbackService;
use App\Http\Controllers\Controller;
use App\Models\Message;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\StreamedResponse;

/**
 * Contrôleur mince (section 10.1, « S — Responsabilité unique ») : il
 * valide, délègue à `ConversationService` ou au dépôt pour les lectures, et
 * met en forme. Aucune règle métier ici.
 */
class ConversationController extends Controller
{
    public function __construct(
        private readonly ConversationService $conversations,
        private readonly ConversationRepositoryContract $repository,
        private readonly MessageFeedbackService $feedback,
    ) {}

    public function index(Request $request): JsonResponse
    {
        return response()->json([
            'data' => ConversationResource::collection($this->repository->listForUser($request->user())),
        ]);
    }

    public function store(StartConversationRequest $request): ConversationResource
    {
        $conversation = $this->conversations->start(
            $request->user(),
            $request->enum('topic', ConversationTopic::class),
        );

        return new ConversationResource($conversation);
    }

    public function show(Request $request, int $conversation): ConversationResource
    {
        $model = $this->repository->findForUser($conversation, $request->user());

        abort_if($model === null, 404);

        return new ConversationResource($model->load('messages'));
    }

    public function sendMessage(SendMessageRequest $request, int $conversation): JsonResponse
    {
        $model = $this->repository->findForUser($conversation, $request->user());

        abort_if($model === null, 404);

        $answer = $this->conversations->sendMessage($request->user(), $model, $request->string('content')->toString());

        return response()->json([
            'content' => $answer->content,
            // Produit par le code côté client à partir de ces champs,
            // jamais en interprétant le texte du modèle (section 11.1).
            'is_emergency' => $answer->isEmergency,
            'urgency_level' => $answer->emergencyAssessment?->level->value,
            'quota_exceeded' => $answer->quotaExceeded,
        ]);
    }

    /**
     * Variante Server-Sent Events (section 6, « Streaming »). Chaque
     * fragment de texte est émis dès réception ; un évènement `done` final
     * porte les mêmes champs de sécurité que `sendMessage()`, pour que le
     * client affiche l'écran d'urgence ou l'état de quota à partir du code,
     * jamais du texte diffusé (section 11.1).
     */
    public function streamMessage(SendMessageRequest $request, int $conversation): StreamedResponse
    {
        $model = $this->repository->findForUser($conversation, $request->user());

        abort_if($model === null, 404);

        $user = $request->user();
        $content = $request->string('content')->toString();

        return response()->stream(function () use ($user, $model, $content): void {
            $emit = function (string $event, array $payload): void {
                echo "event: {$event}\n";
                echo 'data: '.json_encode($payload)."\n\n";

                if (ob_get_level() > 0) {
                    ob_flush();
                }
                flush();
            };

            $answer = $this->conversations->sendMessageStreaming(
                $user,
                $model,
                $content,
                onDelta: fn (string $delta) => $emit('delta', ['text' => $delta]),
            );

            $emit('done', [
                'content' => $answer->content,
                'is_emergency' => $answer->isEmergency,
                'urgency_level' => $answer->emergencyAssessment?->level->value,
                'quota_exceeded' => $answer->quotaExceeded,
            ]);
        }, 200, [
            'Content-Type' => 'text/event-stream',
            'Cache-Control' => 'no-cache',
            'X-Accel-Buffering' => 'no',
            'Connection' => 'keep-alive',
        ]);
    }

    public function rateMessage(RateMessageRequest $request, int $conversation, int $message): MessageResource
    {
        $conversationModel = $this->repository->findForUser($conversation, $request->user());

        abort_if($conversationModel === null, 404);

        $messageModel = Message::query()
            ->where('conversation_id', $conversationModel->id)
            ->find($message);

        abort_if($messageModel === null, 404);

        return new MessageResource(
            $this->feedback->rate($messageModel, $request->enum('feedback', MessageFeedback::class)),
        );
    }
}
