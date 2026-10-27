<?php

namespace App\Domains\Conversation\Enums;

enum MessageSenderType: string
{
    case User = 'user';
    case Assistant = 'assistant';
    case System = 'system';
}
