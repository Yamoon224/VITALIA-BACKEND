<?php

namespace App\Models;

use App\Domains\Safety\Enums\UrgencyLevel;
use Database\Factories\EmergencyDetectionFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * Trace de chaque détection d'urgence (section 3.1, « alerte interne »).
 *
 * Visible du référent clinique sans délai ; alimente les indicateurs de
 * sensibilité et de faux négatifs suivis en continu (section 5.4).
 *
 * @property int $id
 * @property int $user_id
 * @property int|null $conversation_id
 * @property UrgencyLevel $level
 * @property list<string> $matched_signals
 * @property string|null $mental_health_protocol
 * @property string|null $message_excerpt
 */
#[Fillable(['user_id', 'conversation_id', 'level', 'matched_signals', 'mental_health_protocol', 'message_excerpt'])]
class EmergencyDetection extends Model
{
    /** @use HasFactory<EmergencyDetectionFactory> */
    use HasFactory;

    protected function casts(): array
    {
        return [
            'level' => UrgencyLevel::class,
            'matched_signals' => 'array',
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
