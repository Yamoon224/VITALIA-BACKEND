<?php

namespace App\Domains\Consultation\Contracts;

use App\Models\ConsultationSession;
use App\Models\Conversation;
use App\Models\User;

interface ConsultationRepositoryContract
{
    public function startFor(User $user, Conversation $conversation, ?string $motif, ?string $protocolCode): ConsultationSession;

    public function activeFor(Conversation $conversation): ?ConsultationSession;

    public function save(ConsultationSession $session): ConsultationSession;
}
