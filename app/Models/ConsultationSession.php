<?php

namespace App\Models;

use App\Domains\Consultation\Enums\ConsultationStatus;
use App\Domains\Safety\Enums\UrgencyLevel;
use Database\Factories\ConsultationSessionFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Carbon;

/**
 * @property int $id
 * @property int $conversation_id
 * @property int $user_id
 * @property string|null $motif
 * @property string|null $protocol_code
 * @property array<string, string>|null $answers
 * @property ConsultationStatus $status
 * @property string|null $summary
 * @property list<array{condition: string, confidence: float}>|null $hypotheses
 * @property UrgencyLevel|null $orientation_level
 * @property list<string>|null $red_flags
 * @property Carbon|null $completed_at
 */
#[Fillable(['conversation_id', 'user_id', 'motif', 'protocol_code', 'answers', 'status', 'summary', 'hypotheses', 'orientation_level', 'red_flags', 'completed_at'])]
class ConsultationSession extends Model
{
    /** @use HasFactory<ConsultationSessionFactory> */
    use HasFactory;

    protected function casts(): array
    {
        return [
            'status' => ConsultationStatus::class,
            'answers' => 'array',
            'hypotheses' => 'array',
            'orientation_level' => UrgencyLevel::class,
            'red_flags' => 'array',
            'completed_at' => 'datetime',
        ];
    }

    /** @return BelongsTo<Conversation, $this> */
    public function conversation(): BelongsTo
    {
        return $this->belongsTo(Conversation::class);
    }

    /** @return BelongsTo<User, $this> */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
