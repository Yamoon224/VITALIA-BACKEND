<?php

namespace App\Domains\Notification\Channels;

use App\Domains\Notification\Contracts\NotificationChannelContract;
use App\Models\User;

/**
 * Faux canal déterministe pour les tests et le développement sans
 * fournisseur push/SMS configuré (même garde que `ArrayLlmProvider`,
 * section 12.1).
 */
class ArrayNotificationChannel implements NotificationChannelContract
{
    /** @var list<array{user_id: int, message: string, context: array<string, mixed>}> */
    private array $sent = [];

    public function send(User $user, string $message, array $context = []): void
    {
        $this->sent[] = ['user_id' => $user->id, 'message' => $message, 'context' => $context];
    }

    /** @return list<array{user_id: int, message: string, context: array<string, mixed>}> */
    public function sentMessages(): array
    {
        return $this->sent;
    }
}
