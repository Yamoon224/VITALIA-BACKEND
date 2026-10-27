<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Carbon;

/**
 * Suivi de la consommation de l'IA par utilisateur (section 5.12) : append-only,
 * une ligne par appel au modèle, pour piloter les coûts et fixer des quotas.
 *
 * @property int $id
 * @property int $user_id
 * @property string $model
 * @property int $input_tokens
 * @property int $output_tokens
 * @property Carbon $created_at
 */
#[Fillable(['user_id', 'model', 'input_tokens', 'output_tokens', 'created_at'])]
class AiUsageLog extends Model
{
    public $timestamps = false;

    protected function casts(): array
    {
        return [
            'created_at' => 'datetime',
        ];
    }

    /** @return BelongsTo<User, $this> */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
