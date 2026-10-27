# VITALIA — Backend (Laravel)

API REST du projet VITALIA : agent conversationnel santé et bien-être propulsé par l'API Claude.
Voir le cahier des charges (`Cahier_des_charges_VITALIA_v1.1.pdf`) pour le contexte complet.

## Architecture : monolithe modulaire

```
app/
├── Models/              # Modèles Eloquent partagés entre domaines
├── Domains/
│   ├── Shared/           # Socle : TransactionManagerContract, DomainException, middlewares
│   ├── Identity/         # Comptes, consentements, rôles
│   ├── Safety/           # Détection d'urgence, pipeline de garde-fous (critique)
│   ├── Assistant/        # LlmGateway (port), adaptateur Claude, PromptBuilder
│   ├── Conversation/     # Chat, messages, orchestration
│   ├── Prescription/     # Brouillons, revue, signature (machine à états)
│   ├── Audit/            # Journal inviolable, écouteurs d'évènements
│   ├── HealthRecord/     # Dossier santé, mémoire consentie de l'agent
│   ├── Consultation/     # Anamnèse guidée, synthèse clinique structurée (JSON validé)
│   ├── Examination/      # Examens : brouillons, revue, interprétation déterministe des résultats
│   ├── Wellness/         # Objectifs, journal d'humeur, repérage de tendances
│   ├── Practitioner/     # Annuaire vérifié, rendez-vous (machine à états)
│   ├── Billing/          # Abonnements, garde de quota (jamais appliqué aux urgences)
│   └── Notification/     # Rappels de traitement, préférences de canal
└── Providers/
    └── DomainServiceProvider.php   # Câblage unique contrat → implémentation
```

Chaque domaine suit la même convention interne :

| Dossier | Rôle |
|---|---|
| `Contracts/` | Interfaces (ports) exposées aux autres domaines et aux tests |
| `DTOs/` | Objets immuables transportant des données entre couches |
| `Enums/` | États et catégories métier |
| `Exceptions/` | Exceptions métier typées (`DomainException`) |
| `Http/Controllers,Requests,Resources/` | Couche HTTP, volontairement mince |
| `Repositories/` | Implémentations Eloquent des contrats de persistance |
| `Services/` | Règles métier, orchestration |
| `Support/` | Implémentations concrètes d'un contrat qui n'est pas une persistance (détecteurs, vérificateurs) |
| `Providers/` | Adaptateurs vers des fournisseurs externes (Claude, paiement...) |

**Règle du monolithe modulaire** (section 9.1 du cahier des charges) : un domaine n'utilise jamais
directement les classes internes d'un autre domaine, seulement ses `Contracts/` et ses événements.
Toute dépendance externe (fournisseur d'IA, base de données, paiement) est injectée derrière une
interface, jamais appelée en dur — voir `DomainServiceProvider`.

## Les 14 domaines sont câblés de bout en bout

- **Shared** — `TransactionManagerContract`, `DomainException`, middleware `role:`.
- **Identity** — inscription, connexion, consentements granulaires (`ConsentType`), rôles (`UserRole`).
- **Safety** — `EmergencyDetectorContract` (détection déterministe, indépendante du modèle),
  `OutputComplianceCheckerContract`, `SafetyPipeline` (avant/après appel au LLM). Prime sur le
  quota et sur toute autre logique (section 5.4).
- **Assistant** — `LlmGatewayContract` (port), `ClaudeLlmProvider` (adaptateur réel),
  `ArrayLlmProvider` (faux fournisseur déterministe pour les tests), `PromptBuilder`,
  `AssistantService` (orchestration : urgence → quota → prompt → LLM → conformité).
- **Conversation** — chat, messages, façade `ConversationService`.
- **HealthRecord** — profil santé, mémoire de l'agent (`Proposed → Accepted/Rejected`),
  `HealthContextProviderContract` : lecture étroite exposée à `Assistant` pour personnaliser le
  prompt système.
- **Consultation** — anamnèse guidée ; `ConsultationSynthesisService` demande une synthèse JSON au
  modèle, `SynthesisValidator` la valide contre un schéma et régénère une fois avant d'échouer
  explicitement (jamais de sortie non conforme persistée).
- **Examination** — brouillons d'examens (`Draft → Validated/Refused`), `ResultInterpretationService`
  (bornes de référence vérifiées par comparaison numérique, jamais par le modèle).
- **Prescription** — machine à états stricte (`draft → in_review → signed|refused → transmitted`),
  verrouillée dans `PrescriptionReviewService` (aucune signature hors de cette classe).
- **Practitioner** — annuaire de praticiens vérifiés, rendez-vous (`Requested → Confirmed →
  Completed`, ou `Cancelled`).
- **Wellness** — objectifs de suivi, journal d'humeur avec repérage déterministe de tendances
  préoccupantes (`ConcerningMoodTrendDetected`).
- **Billing** — abonnements, `QuotaGuardContract` : lecture étroite exposée à `Assistant` (« puis-je
  envoyer ce message ? »), jamais interrogée avant la détection d'urgence.
- **Notification** — `NotificationChannelContract` (port), rappels de traitement programmés
  automatiquement à la signature d'une ordonnance (écouteur sur `PrescriptionSigned`).
- **Audit** — journal inviolable, écouteurs sur les événements de tous les domaines ci-dessus.

43 tests Pest, Larastan et Pint au vert sur l'ensemble des domaines (`composer check`).

## Démarrer en local

```bash
composer install
cp .env.example .env
php artisan key:generate
touch database/database.sqlite   # ou configurer MySQL/PostgreSQL dans .env
php artisan migrate --seed
php artisan serve
```

Par défaut, `ASSISTANT_AI_DRIVER=array` : l'agent répond avec un faux fournisseur déterministe,
sans appel réseau ni clé API. Pour utiliser l'API Claude réelle, renseigner `ANTHROPIC_API_KEY`
dans `.env` et passer `ASSISTANT_AI_DRIVER=claude`.

## Qualité

```bash
composer test       # Pest (unit + feature)
composer lint        # Pint --test
composer format       # Pint
composer analyse       # Larastan / PHPStan
composer check          # lint + analyse + test — porte de sortie avant livraison
```
# VITALIA-BACKEND
