<?php

namespace App\Models;

use App\Domains\Conversation\Enums\MessageFeedback;
use App\Domains\Conversation\Enums\MessageSenderType;
use Database\Factories\MessageFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * @property int $id
 * @property int $conversation_id
 * @property MessageSenderType $sender_type
 * @property string $content
 * @property MessageFeedback|null $feedback
 */
#[Fillable(['conversation_id', 'sender_type', 'content', 'feedback'])]
class Message extends Model
{
    /** @use HasFactory<MessageFactory> */
    use HasFactory;

    protected function casts(): array
    {
        return [
            'sender_type' => MessageSenderType::class,
            'feedback' => MessageFeedback::class,
        ];
    }

    /** @return BelongsTo<Conversation, $this> */
    public function conversation(): BelongsTo
    {
        return $this->belongsTo(Conversation::class);
    }
}
