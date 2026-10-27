<?php

namespace App\Models;

use Database\Factories\HealthProfileFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * Profil santé initial progressif (section 5.1) : chaque champ est
 * facultatif et modifiable, jamais requis pour utiliser l'application.
 *
 * @property int $id
 * @property int $user_id
 * @property int|null $birth_year
 * @property string|null $sex
 * @property int|null $height_cm
 * @property int|null $weight_kg
 * @property list<string>|null $allergies
 * @property list<string>|null $chronic_conditions
 * @property list<string>|null $current_medications
 * @property bool $is_pregnant
 * @property string|null $lifestyle_notes
 */
#[Fillable([
    'user_id', 'birth_year', 'sex', 'height_cm', 'weight_kg',
    'allergies', 'chronic_conditions', 'current_medications', 'is_pregnant', 'lifestyle_notes',
])]
class HealthProfile extends Model
{
    /** @use HasFactory<HealthProfileFactory> */
    use HasFactory;

    protected function casts(): array
    {
        return [
            'allergies' => 'array',
            'chronic_conditions' => 'array',
            'current_medications' => 'array',
            'is_pregnant' => 'boolean',
        ];
    }

    /** @return BelongsTo<User, $this> */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
