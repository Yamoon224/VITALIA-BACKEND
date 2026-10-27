<?php

use App\Domains\Assistant\Providers\ArrayLlmProvider;
use App\Domains\Assistant\Providers\ClaudeLlmProvider;

return [

    /*
    |--------------------------------------------------------------------------
    | Fournisseur de l'agent IA
    |--------------------------------------------------------------------------
    |
    | Section 6 du cahier des charges : le modèle utilisé est configurable
    | par variable d'environnement et choisi par usage. `array` est le faux
    | fournisseur déterministe utilisé par défaut en environnement de test
    | (section 12.1, « Les tests de l'IA n'appellent pas le vrai
    | fournisseur dans la CI standard »).
    |
    */

    'driver' => env('ASSISTANT_AI_DRIVER', 'array'),

    'drivers' => [
        'array' => ArrayLlmProvider::class,
        'claude' => ClaudeLlmProvider::class,
    ],

    'claude' => [
        'key' => env('ANTHROPIC_API_KEY', ''),
        // Modèle de synthèse clinique (conversation, orientation).
        'respond_model' => env('ASSISTANT_CLAUDE_RESPOND_MODEL', 'claude-sonnet-4-5'),
        // Modèle rapide pour le tri et l'extraction (section 6).
        'short_task_model' => env('ASSISTANT_CLAUDE_SHORT_TASK_MODEL', 'claude-haiku-4-5'),
        'base_url' => env('ANTHROPIC_BASE_URL', 'https://api.anthropic.com/v1'),
        'max_tokens' => (int) env('ASSISTANT_CLAUDE_MAX_TOKENS', 1024),
    ],

    /*
    |--------------------------------------------------------------------------
    | Quotas (section 5.12, 11.1)
    |--------------------------------------------------------------------------
    */

    'quotas' => [
        'free_messages_per_day' => (int) env('ASSISTANT_FREE_MESSAGES_PER_DAY', 10),
    ],

];
