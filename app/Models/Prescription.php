<?php

namespace App\Models;

use App\Domains\Prescription\Enums\PrescriptionStatus;
use Database\Factories\PrescriptionFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Carbon;

/**
 * Brouillon puis ordonnance (section 5.7).
 *
 * `items` et `safety_alerts` sont des instantanés JSON du brouillon généré
 * par l'IA (dénomination, dosage, voie, posologie, durée, alertes
 * d'interaction) : la source de vérité des médicaments reste le référentiel
 * (hors scaffolding), jamais une génération libre du modèle.
 *
 * @property int $id
 * @property int $user_id
 * @property int|null $practitioner_id
 * @property PrescriptionStatus $status
 * @property array<int, array<string, mixed>> $items
 * @property array<int, array<string, mixed>>|null $safety_alerts
 * @property string|null $refusal_reason
 * @property Carbon|null $signed_at
 * @property string|null $signature_hash
 * @property Carbon|null $transmitted_at
 */
#[Fillable(['user_id', 'practitioner_id', 'status', 'items', 'safety_alerts', 'refusal_reason', 'signed_at', 'signature_hash', 'transmitted_at'])]
class Prescription extends Model
{
    /** @use HasFactory<PrescriptionFactory> */
    use HasFactory;

    protected function casts(): array
    {
        return [
            'status' => PrescriptionStatus::class,
            'items' => 'array',
            'safety_alerts' => 'array',
            'signed_at' => 'datetime',
            'transmitted_at' => 'datetime',
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
