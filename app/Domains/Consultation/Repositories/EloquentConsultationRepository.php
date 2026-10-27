<?php

namespace App\Domains\Consultation\Repositories;

use App\Domains\Consultation\Contracts\ConsultationRepositoryContract;
use App\Domains\Consultation\Enums\ConsultationStatus;
use App\Models\ConsultationSession;
use App\Models\Conversation;
use App\Models\User;

class EloquentConsultationRepository implements ConsultationRepositoryContract
{
    public function startFor(User $user, Conversation $conversation, ?string $motif, ?string $protocolCode): ConsultationSession
    {
        return ConsultationSession::query()->create([
            'conversation_id' => $conversation->id,
            'user_id' => $user->id,
            'motif' => $motif,
            'protocol_code' => $protocolCode,
            'answers' => [],
            'status' => ConsultationStatus::InProgress,
        ]);
    }

    public function activeFor(Conversation $conversation): ?ConsultationSession
    {
        return ConsultationSession::query()
            ->where('conversation_id', $conversation->id)
            ->where('status', ConsultationStatus::InProgress)
            ->latest()
            ->first();
    }

    public function save(ConsultationSession $session): ConsultationSession
    {
        $session->save();

        return $session;
    }
}
