<?php

namespace App\Models;

use App\Domains\Billing\Enums\SubscriptionPlan;
use App\Domains\Billing\Enums\SubscriptionStatus;
use Database\Factories\SubscriptionFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Carbon;

/**
 * @property int $id
 * @property int $user_id
 * @property SubscriptionPlan $plan
 * @property SubscriptionStatus $status
 * @property Carbon|null $renews_at
 */
#[Fillable(['user_id', 'plan', 'status', 'renews_at'])]
class Subscription extends Model
{
    /** @use HasFactory<SubscriptionFactory> */
    use HasFactory;

    protected function casts(): array
    {
        return [
            'plan' => SubscriptionPlan::class,
            'status' => SubscriptionStatus::class,
            'renews_at' => 'datetime',
        ];
    }

    /** @return BelongsTo<User, $this> */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function isActivePremium(): bool
    {
        return $this->plan === SubscriptionPlan::Premium && $this->status === SubscriptionStatus::Active;
    }
}
