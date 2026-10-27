<?php

namespace App\Models;

use Database\Factories\MedicationFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * Base de référence des médicaments (section 5.7) : interrogée par code,
 * jamais « de mémoire » par le modèle. Le nom, la forme et la posologie
 * d'un brouillon d'ordonnance proviennent de ce référentiel, pas d'une
 * génération libre (section 5.7).
 *
 * @property int $id
 * @property string $dci
 * @property string $form
 * @property string $standard_dosage
 * @property int $max_duration_days
 * @property bool $is_restricted
 * @property list<string>|null $allergen_tags
 * @property list<string>|null $contraindications
 * @property list<string>|null $interacts_with
 */
#[Fillable(['dci', 'form', 'standard_dosage', 'max_duration_days', 'is_restricted', 'allergen_tags', 'contraindications', 'interacts_with'])]
class Medication extends Model
{
    /** @use HasFactory<MedicationFactory> */
    use HasFactory;

    protected function casts(): array
    {
        return [
            'is_restricted' => 'boolean',
            'allergen_tags' => 'array',
            'contraindications' => 'array',
            'interacts_with' => 'array',
        ];
    }
}
