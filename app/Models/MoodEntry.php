<?php

namespace App\Models;

use Database\Factories\MoodEntryFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Carbon;

/**
 * Journal d'humeur (section 5.9) : `mood_level` de 1 (très bas) à 5 (très
 * bon). Le repérage de tendances préoccupantes se fait sur une suite de
 * valeurs basses (`MoodTrendService`), jamais sur une entrée isolée.
 *
 * @property int $id
 * @property int $user_id
 * @property int $mood_level
 * @property string|null $note
 * @property Carbon $recorded_at
 */
#[Fillable(['user_id', 'mood_level', 'note', 'recorded_at'])]
class MoodEntry extends Model
{
    /** @use HasFactory<MoodEntryFactory> */
    use HasFactory;

    protected function casts(): array
    {
        return [
            'recorded_at' => 'datetime',
        ];
    }

    /** @return BelongsTo<User, $this> */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
