<?php

namespace App\Domains\Billing\Http\Controllers;

use App\Domains\Billing\Contracts\SubscriptionRepositoryContract;
use App\Domains\Billing\Enums\SubscriptionPlan;
use App\Domains\Billing\Http\Resources\SubscriptionResource;
use App\Http\Controllers\Controller;
use App\Models\Subscription;
use Illuminate\Http\Request;

/**
 * Formule gratuite par défaut (section 5.12). La mise à niveau réelle
 * (paiement mobile money / carte) relève d'une passerelle externe non
 * encore câblée : `subscribe()` enregistre directement le changement de
 * plan, en attendant l'intégration du domaine `Billing` avec un agrégateur
 * de paiement (planning, phase 7).
 */
class SubscriptionController extends Controller
{
    public function __construct(private readonly SubscriptionRepositoryContract $subscriptions) {}

    public function show(Request $request): SubscriptionResource
    {
        $subscription = $this->subscriptions->forUser($request->user())
            ?? new Subscription(['plan' => SubscriptionPlan::Free]);

        return new SubscriptionResource($subscription);
    }

    public function subscribe(Request $request): SubscriptionResource
    {
        return new SubscriptionResource(
            $this->subscriptions->setPlan($request->user(), SubscriptionPlan::Premium),
        );
    }

    public function cancel(Request $request): SubscriptionResource
    {
        return new SubscriptionResource(
            $this->subscriptions->setPlan($request->user(), SubscriptionPlan::Free),
        );
    }
}
