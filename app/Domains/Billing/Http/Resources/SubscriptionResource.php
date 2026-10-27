<?php

namespace App\Domains\Billing\Http\Resources;

use App\Models\Subscription;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/** @mixin Subscription */
class SubscriptionResource extends JsonResource
{
    /** @return array<string, mixed> */
    public function toArray(Request $request): array
    {
        return [
            'plan' => $this->plan,
            'status' => $this->status,
            'renews_at' => $this->renews_at,
        ];
    }
}
