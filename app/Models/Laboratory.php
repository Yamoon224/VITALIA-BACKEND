<?php

namespace App\Models;

use App\Domains\Examination\Enums\LaboratoryType;
use Database\Factories\LaboratoryFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * Orientation vers des laboratoires et centres d'imagerie partenaires
 * (section 5.6, « annuaire géolocalisé »).
 *
 * @property int $id
 * @property string $name
 * @property LaboratoryType $type
 * @property string $city
 * @property string|null $address
 * @property float|null $latitude
 * @property float|null $longitude
 * @property string|null $phone
 */
#[Fillable(['name', 'type', 'city', 'address', 'latitude', 'longitude', 'phone'])]
class Laboratory extends Model
{
    /** @use HasFactory<LaboratoryFactory> */
    use HasFactory;

    protected function casts(): array
    {
        return [
            'type' => LaboratoryType::class,
            'latitude' => 'float',
            'longitude' => 'float',
        ];
    }
}
