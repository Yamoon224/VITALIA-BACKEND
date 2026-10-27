<?php

namespace App\Domains\Notification\Contracts;

use App\Models\User;

/**
 * Port vers les passerelles d'envoi (section 9, 10.2 — Port/Adaptateur) :
 * push (FCM/APNs), SMS, WhatsApp, e-mail. Le domaine ne connaît que ce
 * contrat ; chaque canal réel est un adaptateur enregistré dans
 * `DomainServiceProvider` (même garde que `LlmGatewayContract`).
 */
interface NotificationChannelContract
{
    /** @param  array<string, mixed>  $context */
    public function send(User $user, string $message, array $context = []): void;
}
