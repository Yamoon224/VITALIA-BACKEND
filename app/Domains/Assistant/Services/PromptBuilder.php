<?php

namespace App\Domains\Assistant\Services;

use App\Domains\Assistant\Contracts\PromptVersionRepositoryContract;
use App\Models\User;

/**
 * Assembleur du prompt système (section 6, 10.2 — pattern Fabrique/Builder).
 *
 * Le prompt est composé de blocs (identité et périmètre, règles de sécurité,
 * ton, protocoles du motif en cours, profil santé résumé) et jamais
 * concaténé directement dans un contrôleur (section 6). Chaque bloc est une
 * méthode nommée pour rester lisible et testable isolément.
 *
 * Les trois premiers blocs sont administrables (section 5.13, « gestion des
 * versions de prompts ») : si une version est publiée pour un
 * `block_key`, elle remplace le texte codé en dur ci-dessous, qui reste le
 * filet de sécurité par défaut. Les garde-fous déterministes du domaine
 * `Safety` ne dépendent jamais de ce texte (section 3.1, « défense en
 * profondeur ») : même un bloc mal édité ne désactive pas la détection
 * d'urgence.
 */
class PromptBuilder
{
    private const string VERSION = '2026.10.1';

    public function __construct(private readonly PromptVersionRepositoryContract $versions) {}

    public function build(User $user, ?string $consultationProtocol = null, ?string $healthProfileSummary = null): string
    {
        $blocks = array_filter([
            $this->resolve('identity_and_scope', $this->defaultIdentityAndScope()),
            $this->resolve('safety_rules', $this->defaultSafetyRules()),
            $this->resolve('tone', $this->defaultTone()),
            $consultationProtocol !== null ? $this->protocol($consultationProtocol) : null,
            $healthProfileSummary !== null ? $this->healthProfile($healthProfileSummary) : null,
        ]);

        return implode("\n\n", $blocks);
    }

    public function version(): string
    {
        return self::VERSION;
    }

    private function resolve(string $blockKey, string $default): string
    {
        return $this->versions->publishedContentFor($blockKey) ?? $default;
    }

    private function defaultIdentityAndScope(): string
    {
        return <<<'PROMPT'
            Tu es l'agent conversationnel de VITALIA, dédié exclusivement à la santé
            et au bien-être. Tu n'es pas un professionnel de santé : tu informes et
            orientes, tu ne diagnostiques jamais de façon définitive et tu ne
            prescris jamais seul. Toute demande hors santé et bien-être est déclinée
            poliment avec une redirection.
            PROMPT;
    }

    private function defaultSafetyRules(): string
    {
        return <<<'PROMPT'
            Règles de sécurité impératives :
            - Exprime toujours ton incertitude et rappelle tes limites.
            - Ne pose jamais de diagnostic définitif : propose des hypothèses avec
              leur niveau de confiance et des signes d'alerte à surveiller.
            - Ne donne jamais de posologie hors d'un brouillon d'ordonnance soumis à
              un praticien.
            - En cas de signe de gravité, oriente immédiatement vers les secours.
            PROMPT;
    }

    private function defaultTone(): string
    {
        return 'Ton : clair, simple, chaleureux, en français, avec des messages courts adaptés au mobile.';
    }

    private function protocol(string $protocol): string
    {
        return "Protocole clinique applicable à ce motif de consultation :\n{$protocol}";
    }

    private function healthProfile(string $summary): string
    {
        return "Profil santé connu de l'utilisateur (avec son consentement) :\n{$summary}";
    }
}
