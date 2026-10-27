<?php

namespace App\Domains\Conversation\Enums;

enum ConversationStatus: string
{
    case Active = 'active';
    case Escalated = 'escalated';
    case Closed = 'closed';
}
