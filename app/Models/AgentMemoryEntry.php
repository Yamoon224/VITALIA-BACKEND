<?php

namespace App\Models;

use App\Domains\HealthRecord\Enums\MemoryEntryStatus;
use Database\Factories\AgentMemoryEntryFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * Élément que l'agent propose de retenir pour personnaliser ses réponses
 * (section 5.8). Reste `Proposed` tant que l'utilisateur ne l'a pas validé.
 *
 * @property int $id
 * @property int $user_id
 * @property int|null $conversation_id
 * @property string $content
 * @property MemoryEntryStatus $status
 */
#[Fillable(['user_id', 'conversation_id', 'content', 'status'])]
class AgentMemoryEntry extends Model
{
    /** @use HasFactory<AgentMemoryEntryFactory> */
    use HasFactory;

    protected function casts(): array
    {
        return [
            'status' => MemoryEntryStatus::class,
        ];
    }

    /** @return BelongsTo<User, $this> */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /** @return BelongsTo<Conversation, $this> */
    public function conversation(): BelongsTo
    {
        return $this->belongsTo(Conversation::class);
    }
}
