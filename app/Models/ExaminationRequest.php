<?php

namespace App\Models;

use App\Domains\Examination\Enums\ExaminationRequestStatus;
use Database\Factories\ExaminationRequestFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Carbon;

/**
 * @property int $id
 * @property int $user_id
 * @property int|null $conversation_id
 * @property int|null $practitioner_id
 * @property ExaminationRequestStatus $status
 * @property array<int, array<string, mixed>> $items
 * @property string|null $refusal_reason
 * @property Carbon|null $validated_at
 */
#[Fillable(['user_id', 'conversation_id', 'practitioner_id', 'status', 'items', 'refusal_reason', 'validated_at'])]
class ExaminationRequest extends Model
{
    /** @use HasFactory<ExaminationRequestFactory> */
    use HasFactory;

    protected function casts(): array
    {
        return [
            'status' => ExaminationRequestStatus::class,
            'items' => 'array',
            'validated_at' => 'datetime',
        ];
    }

    /** @return BelongsTo<User, $this> */
    public function patient(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    /** @return BelongsTo<User, $this> */
    public function practitioner(): BelongsTo
    {
        return $this->belongsTo(User::class, 'practitioner_id');
    }
}
