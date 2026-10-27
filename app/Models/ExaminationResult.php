<?php

namespace App\Models;

use Database\Factories\ExaminationResultFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Carbon;

/**
 * Valeur extraite d'un bilan (section 5.5, 5.6) : les bornes de référence
 * et le drapeau `is_abnormal` sont calculés par des règles déterministes
 * (`ResultInterpretationService`), jamais par le modèle (section 5.5,
 * « vérification déterministe des unités et bornes »).
 *
 * @property int $id
 * @property int $user_id
 * @property int|null $examination_request_id
 * @property string $parameter
 * @property float $value
 * @property string $unit
 * @property float|null $reference_low
 * @property float|null $reference_high
 * @property bool $is_abnormal
 * @property Carbon $recorded_at
 */
#[Fillable(['user_id', 'examination_request_id', 'parameter', 'value', 'unit', 'reference_low', 'reference_high', 'is_abnormal', 'recorded_at'])]
class ExaminationResult extends Model
{
    /** @use HasFactory<ExaminationResultFactory> */
    use HasFactory;

    protected function casts(): array
    {
        return [
            'value' => 'float',
            'reference_low' => 'float',
            'reference_high' => 'float',
            'is_abnormal' => 'boolean',
            'recorded_at' => 'datetime',
        ];
    }

    /** @return BelongsTo<User, $this> */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /** @return BelongsTo<ExaminationRequest, $this> */
    public function examinationRequest(): BelongsTo
    {
        return $this->belongsTo(ExaminationRequest::class);
    }
}
