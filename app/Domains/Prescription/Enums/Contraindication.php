<?php

namespace App\Domains\Prescription\Enums;

/**
 * Contre-indications reconnues par `PrescriptionSafetyCheckService`
 * (section 5.7) : âge, grossesse, insuffisance rénale/hépatique si connues.
 */
enum Contraindication: string
{
    case Pregnancy = 'pregnancy';
    case RenalInsufficiency = 'renal_insufficiency';
    case HepaticInsufficiency = 'hepatic_insufficiency';
    case Pediatric = 'pediatric';
}
