<?php

namespace App\Models;

use App\Domains\Wellness\Enums\WellnessGoalType;
use Database\Factories\WellnessGoalFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * @property int $id
 * @property int $user_id
 * @property WellnessGoalType $type
 * @property float $target_value
 * @property float $current_value
 */
#[Fillable(['user_id', 'type', 'target_value', 'current_value'])]
class WellnessGoal extends Model
{
    /** @use HasFactory<WellnessGoalFactory> */
    use HasFactory;

    protected function casts(): array
    {
        return [
            'type' => WellnessGoalType::class,
            'target_value' => 'float',
            'current_value' => 'float',
        ];
    }

    /** @return BelongsTo<User, $this> */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
