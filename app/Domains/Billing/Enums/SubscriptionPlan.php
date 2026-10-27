<?php

namespace App\Domains\Billing\Enums;

/** Formules d'abonnement (section 5.12). */
enum SubscriptionPlan: string
{
    case Free = 'free';
    case Premium = 'premium';
}
