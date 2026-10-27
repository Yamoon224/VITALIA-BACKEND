<?php

namespace App\Domains\Identity\Enums;

/**
 * Profils du système (section 4 du cahier des charges).
 *
 * Portés comme rôles `spatie/laravel-permission` (table `roles`), pas comme
 * colonne figée sur `users` : un compte peut évoluer (ex. support promu
 * administrateur) sans migration de schéma.
 */
enum UserRole: string
{
    case Patient = 'patient';
    case Caregiver = 'caregiver';
    case Practitioner = 'practitioner';
    case ClinicalReferent = 'clinical_referent';
    case Support = 'support';
    case Administrator = 'administrator';

    /** Rôles qui n'ont accès qu'à l'application mobile (section 4.1). */
    public function isMobileOnly(): bool
    {
        return match ($this) {
            self::Patient, self::Caregiver => true,
            default => false,
        };
    }

    /** Rôles qui travaillent sur l'interface web d'administration (section 4.1). */
    public function isAdminWebUser(): bool
    {
        return ! $this->isMobileOnly();
    }
}
