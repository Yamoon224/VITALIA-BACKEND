<?php

namespace App\Models;

use Database\Factories\NotificationPreferenceFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * Contenu discret par défaut (section 5.10) : `push_enabled` est la seule
 * préférence activée par défaut, aucune donnée médicale détaillée n'est
 * jamais envoyée dans le corps d'une notification (section 5.14).
 *
 * @property int $id
 * @property int $user_id
 * @property bool $push_enabled
 * @property bool $sms_enabled
 * @property bool $whatsapp_enabled
 * @property bool $email_enabled
 * @property int|null $quiet_hours_start
 * @property int|null $quiet_hours_end
 */
#[Fillable(['user_id', 'push_enabled', 'sms_enabled', 'whatsapp_enabled', 'email_enabled', 'quiet_hours_start', 'quiet_hours_end'])]
class NotificationPreference extends Model
{
    /** @use HasFactory<NotificationPreferenceFactory> */
    use HasFactory;

    protected function casts(): array
    {
        return [
            'push_enabled' => 'boolean',
            'sms_enabled' => 'boolean',
            'whatsapp_enabled' => 'boolean',
            'email_enabled' => 'boolean',
        ];
    }

    /** @return BelongsTo<User, $this> */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
