<?php

namespace App\Domains\HealthRecord\Services;

use App\Domains\HealthRecord\Contracts\AgentMemoryRepositoryContract;
use App\Domains\HealthRecord\Contracts\HealthContextProviderContract;
use App\Domains\HealthRecord\Contracts\HealthProfileRepositoryContract;
use App\Models\HealthProfile;
use App\Models\User;

class HealthProfileService implements HealthContextProviderContract
{
    public function __construct(
        private readonly HealthProfileRepositoryContract $profiles,
        private readonly AgentMemoryRepositoryContract $memory,
    ) {}

    public function show(User $user): ?HealthProfile
    {
        return $this->profiles->forUser($user);
    }

    /** @param  array<string, mixed>  $attributes */
    public function update(User $user, array $attributes): HealthProfile
    {
        return $this->profiles->updateFor($user, $attributes);
    }

    /**
     * Résumé textuel injecté dans le prompt système (section 6,
     * `PromptBuilder::healthProfile()`). Ne retourne que ce que l'agent a le
     * droit de voir : profil de base + mémoire acceptée par l'utilisateur,
     * jamais les propositions encore `Proposed` (section 5.8).
     */
    public function summaryFor(User $user): ?string
    {
        $profile = $this->profiles->forUser($user);
        $accepted = $this->memory->acceptedFor($user);

        if ($profile === null && $accepted->isEmpty()) {
            return null;
        }

        $lines = [];

        if ($profile !== null) {
            $lines[] = $this->describeProfile($profile);
        }

        foreach ($accepted as $entry) {
            $lines[] = "- {$entry->content}";
        }

        return implode("\n", array_filter($lines));
    }

    private function describeProfile(HealthProfile $profile): string
    {
        $parts = [];

        if ($profile->birth_year !== null) {
            $parts[] = 'année de naissance : '.$profile->birth_year;
        }

        if ($profile->sex !== null) {
            $parts[] = 'sexe : '.$profile->sex;
        }

        if (! empty($profile->allergies)) {
            $parts[] = 'allergies : '.implode(', ', $profile->allergies);
        }

        if (! empty($profile->chronic_conditions)) {
            $parts[] = 'antécédents : '.implode(', ', $profile->chronic_conditions);
        }

        if (! empty($profile->current_medications)) {
            $parts[] = 'traitements en cours : '.implode(', ', $profile->current_medications);
        }

        if ($profile->is_pregnant) {
            $parts[] = 'grossesse en cours';
        }

        return $parts === [] ? '' : ('- '.implode(' ; ', $parts));
    }
}
