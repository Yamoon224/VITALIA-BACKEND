<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Carbon;

/**
 * Journal d'audit inviolable (section 8, 9).
 *
 * Append-only par convention applicative : aucun service du système ne met
 * à jour ni ne supprime une ligne existante (pas de `Auditable::update()`
 * exposé). `context` porte un instantané structuré de l'évènement, jamais de
 * donnée de santé en clair (section 7, « Auditabilité »).
 *
 * @property int $id
 * @property int|null $actor_id
 * @property string $action
 * @property string|null $auditable_type
 * @property int|null $auditable_id
 * @property array<string, mixed>|null $context
 * @property string|null $ip_address
 * @property Carbon $created_at
 */
#[Fillable(['actor_id', 'action', 'auditable_type', 'auditable_id', 'context', 'ip_address', 'created_at'])]
class AuditLog extends Model
{
    public $timestamps = false;

    protected function casts(): array
    {
        return [
            'context' => 'array',
            'created_at' => 'datetime',
        ];
    }

    /** @return BelongsTo<User, $this> */
    public function actor(): BelongsTo
    {
        return $this->belongsTo(User::class, 'actor_id');
    }
}
