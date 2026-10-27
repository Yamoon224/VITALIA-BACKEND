<?php

namespace App\Models;

use App\Domains\Identity\Enums\ConsentType;
use Database\Factories\ConsentFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Carbon;

/**
 * Historique des consentements d'un utilisateur (section 5.1).
 *
 * Chaque changement d'état crée une nouvelle ligne plutôt que d'écraser la
 * précédente : l'historique complet des consentements est un droit de
 * l'utilisateur (section 5.1) et une preuve de conformité (section 8).
 *
 * @property int $id
 * @property int $user_id
 * @property ConsentType $type
 * @property bool $granted
 * @property Carbon|null $granted_at
 * @property Carbon|null $revoked_at
 */
#[Fillable(['user_id', 'type', 'granted', 'granted_at', 'revoked_at'])]
class Consent extends Model
{
    /** @use HasFactory<ConsentFactory> */
    use HasFactory;

    protected function casts(): array
    {
        return [
            'type' => ConsentType::class,
            'granted' => 'boolean',
            'granted_at' => 'datetime',
            'revoked_at' => 'datetime',
        ];
    }

    /** @return BelongsTo<User, $this> */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
