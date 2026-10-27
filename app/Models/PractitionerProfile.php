<?php

namespace App\Models;

use Database\Factories\PractitionerProfileFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Carbon;

/**
 * Processus de vérification des diplômes et inscriptions à l'ordre
 * (section 5.13, 14) : `verified_at` n'est renseigné que par un
 * administrateur, jamais par le praticien lui-même.
 *
 * @property int $id
 * @property int $user_id
 * @property string $specialty
 * @property string $license_number
 * @property Carbon|null $verified_at
 * @property string|null $bio
 * @property list<string>|null $languages
 * @property int|null $consultation_fee_cents
 */
#[Fillable(['user_id', 'specialty', 'license_number', 'verified_at', 'bio', 'languages', 'consultation_fee_cents'])]
class PractitionerProfile extends Model
{
    /** @use HasFactory<PractitionerProfileFactory> */
    use HasFactory;

    protected function casts(): array
    {
        return [
            'verified_at' => 'datetime',
            'languages' => 'array',
        ];
    }

    /** @return BelongsTo<User, $this> */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function isVerified(): bool
    {
        return $this->verified_at !== null;
    }
}
