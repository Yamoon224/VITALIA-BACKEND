<?php

namespace App\Providers;

use App\Domains\Assistant\Contracts\LlmGatewayContract;
use App\Domains\Assistant\Contracts\PromptVersionRepositoryContract;
use App\Domains\Assistant\Providers\ArrayLlmProvider;
use App\Domains\Assistant\Providers\ClaudeLlmProvider;
use App\Domains\Assistant\Repositories\EloquentPromptVersionRepository;
use App\Domains\Audit\Contracts\AuditLoggerContract;
use App\Domains\Audit\Listeners\LogConsultationCompleted;
use App\Domains\Audit\Listeners\LogCriticalResult;
use App\Domains\Audit\Listeners\LogEmergencyDetection;
use App\Domains\Audit\Listeners\LogExaminationValidated;
use App\Domains\Audit\Listeners\LogPrescriptionSigned;
use App\Domains\Audit\Services\AuditLogger;
use App\Domains\Billing\Contracts\AiUsageRecorderContract;
use App\Domains\Billing\Contracts\QuotaGuardContract;
use App\Domains\Billing\Contracts\SubscriptionRepositoryContract;
use App\Domains\Billing\Repositories\EloquentSubscriptionRepository;
use App\Domains\Billing\Services\AiUsageRecorder;
use App\Domains\Billing\Services\QuotaGuardService;
use App\Domains\Consultation\Contracts\ConsultationRepositoryContract;
use App\Domains\Consultation\Events\ConsultationCompleted;
use App\Domains\Consultation\Repositories\EloquentConsultationRepository;
use App\Domains\Conversation\Contracts\ConversationRepositoryContract;
use App\Domains\Conversation\Repositories\EloquentConversationRepository;
use App\Domains\Examination\Contracts\ExaminationRequestRepositoryContract;
use App\Domains\Examination\Events\CriticalResultRecorded;
use App\Domains\Examination\Events\ExaminationValidated;
use App\Domains\Examination\Repositories\EloquentExaminationRequestRepository;
use App\Domains\HealthRecord\Contracts\AgentMemoryRepositoryContract;
use App\Domains\HealthRecord\Contracts\HealthContextProviderContract;
use App\Domains\HealthRecord\Contracts\HealthProfileRepositoryContract;
use App\Domains\HealthRecord\Repositories\EloquentAgentMemoryRepository;
use App\Domains\HealthRecord\Repositories\EloquentHealthProfileRepository;
use App\Domains\HealthRecord\Services\HealthProfileService;
use App\Domains\Identity\Contracts\ConsentRepositoryContract;
use App\Domains\Identity\Contracts\UserRepositoryContract;
use App\Domains\Identity\Repositories\EloquentConsentRepository;
use App\Domains\Identity\Repositories\EloquentUserRepository;
use App\Domains\Notification\Channels\ArrayNotificationChannel;
use App\Domains\Notification\Contracts\NotificationChannelContract;
use App\Domains\Notification\Contracts\NotificationPreferenceRepositoryContract;
use App\Domains\Notification\Contracts\ReminderRepositoryContract;
use App\Domains\Notification\Listeners\ScheduleMedicationReminders;
use App\Domains\Notification\Repositories\EloquentNotificationPreferenceRepository;
use App\Domains\Notification\Repositories\EloquentReminderRepository;
use App\Domains\Practitioner\Contracts\AppointmentRepositoryContract;
use App\Domains\Practitioner\Contracts\PractitionerDirectoryRepositoryContract;
use App\Domains\Practitioner\Repositories\EloquentAppointmentRepository;
use App\Domains\Practitioner\Repositories\EloquentPractitionerDirectoryRepository;
use App\Domains\Prescription\Contracts\PrescriptionRepositoryContract;
use App\Domains\Prescription\Events\PrescriptionSigned;
use App\Domains\Prescription\Repositories\EloquentPrescriptionRepository;
use App\Domains\Safety\Contracts\EmergencyDetectorContract;
use App\Domains\Safety\Contracts\OutputComplianceCheckerContract;
use App\Domains\Safety\Events\EmergencyDetected;
use App\Domains\Safety\Support\BasicOutputComplianceChecker;
use App\Domains\Safety\Support\KeywordEmergencyDetector;
use App\Domains\Shared\Contracts\TransactionManagerContract;
use App\Domains\Shared\Support\DatabaseTransactionManager;
use App\Domains\Wellness\Contracts\MoodEntryRepositoryContract;
use App\Domains\Wellness\Contracts\WellnessGoalRepositoryContract;
use App\Domains\Wellness\Repositories\EloquentMoodEntryRepository;
use App\Domains\Wellness\Repositories\EloquentWellnessGoalRepository;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\ServiceProvider;
use RuntimeException;

/**
 * Point unique de câblage entre contrats et implémentations (inversion des
 * dépendances, section 10.1 — D).
 *
 * Aucun service métier ne référence une classe concrète de persistance ni de
 * fournisseur externe : substituer une implémentation, en test ou en
 * production, ne touche que ce fichier.
 */
class DomainServiceProvider extends ServiceProvider
{
    /** @var array<class-string, class-string> */
    public array $bindings = [
        // --- Socle -----------------------------------------------------------
        TransactionManagerContract::class => DatabaseTransactionManager::class,

        // --- Comptes et consentements (Identity) ------------------------------
        UserRepositoryContract::class => EloquentUserRepository::class,
        ConsentRepositoryContract::class => EloquentConsentRepository::class,

        // --- Dossier santé et mémoire (HealthRecord) ----------------------------
        HealthProfileRepositoryContract::class => EloquentHealthProfileRepository::class,
        AgentMemoryRepositoryContract::class => EloquentAgentMemoryRepository::class,
        // Lecture étroite exposée au domaine `Assistant` (section 10.1 — I).
        HealthContextProviderContract::class => HealthProfileService::class,

        // --- Agent IA : prompts versionnés (Assistant) --------------------------
        PromptVersionRepositoryContract::class => EloquentPromptVersionRepository::class,

        // --- Garde-fous cliniques (Safety) -------------------------------------
        EmergencyDetectorContract::class => KeywordEmergencyDetector::class,
        OutputComplianceCheckerContract::class => BasicOutputComplianceChecker::class,

        // --- Journal d'audit ---------------------------------------------------
        AuditLoggerContract::class => AuditLogger::class,

        // --- Conversations -------------------------------------------------------
        ConversationRepositoryContract::class => EloquentConversationRepository::class,

        // --- Consultation guidée (anamnèse) -----------------------------------------
        ConsultationRepositoryContract::class => EloquentConsultationRepository::class,

        // --- Examens -------------------------------------------------------------------
        ExaminationRequestRepositoryContract::class => EloquentExaminationRequestRepository::class,

        // --- Prescriptions ---------------------------------------------------------
        PrescriptionRepositoryContract::class => EloquentPrescriptionRepository::class,

        // --- Praticiens et rendez-vous (Practitioner) -----------------------------------
        PractitionerDirectoryRepositoryContract::class => EloquentPractitionerDirectoryRepository::class,
        AppointmentRepositoryContract::class => EloquentAppointmentRepository::class,

        // --- Bien-être (Wellness) ---------------------------------------------------------
        WellnessGoalRepositoryContract::class => EloquentWellnessGoalRepository::class,
        MoodEntryRepositoryContract::class => EloquentMoodEntryRepository::class,

        // --- Facturation et quotas (Billing) -----------------------------------------------
        SubscriptionRepositoryContract::class => EloquentSubscriptionRepository::class,
        // Lecture étroite exposée au domaine `Assistant` : « puis-je envoyer ce
        // message ? », sans connaître les plans (section 10.1 — I).
        QuotaGuardContract::class => QuotaGuardService::class,
        AiUsageRecorderContract::class => AiUsageRecorder::class,

        // --- Rappels et préférences (Notification) ------------------------------------------
        ReminderRepositoryContract::class => EloquentReminderRepository::class,
        NotificationPreferenceRepositoryContract::class => EloquentNotificationPreferenceRepository::class,
    ];

    public function register(): void
    {
        $this->registerLlmGateway();
        $this->registerNotificationChannel();
    }

    public function boot(): void
    {
        // Les écouteurs vivent par domaine (`app/Domains/Audit/Listeners`,
        // `app/Domains/Notification/Listeners`), hors de la convention
        // `app/Listeners` : l'auto-découverte de Laravel ne les trouve pas,
        // ils sont donc enregistrés explicitement (section 10.2, pattern
        // Observateur).
        Event::listen(EmergencyDetected::class, LogEmergencyDetection::class);
        Event::listen(PrescriptionSigned::class, LogPrescriptionSigned::class);
        Event::listen(PrescriptionSigned::class, ScheduleMedicationReminders::class);
        Event::listen(ConsultationCompleted::class, LogConsultationCompleted::class);
        Event::listen(ExaminationValidated::class, LogExaminationValidated::class);
        Event::listen(CriticalResultRecorded::class, LogCriticalResult::class);
    }

    /**
     * Fournisseur de l'agent IA, choisi par configuration (section 6, 9).
     *
     * `ArrayLlmProvider` est aussi enregistré comme singleton sous son
     * propre nom : les tests le redemandent par sa classe concrète pour
     * préparer une réponse (`queue()`) ou inspecter les appels reçus, et
     * doivent obtenir exactement l'instance que les services ont reçue.
     *
     * Un pilote inconnu lève au lieu de retomber sur un autre : un
     * environnement qui croit appeler Claude alors qu'il appelle le faux
     * fournisseur ne se découvrirait qu'en production.
     */
    private function registerLlmGateway(): void
    {
        $this->app->singleton(ArrayLlmProvider::class);

        $this->app->singleton(LlmGatewayContract::class, function (): LlmGatewayContract {
            $name = (string) config('assistant.driver');
            $driver = config("assistant.drivers.{$name}");

            if (! is_string($driver) || ! is_subclass_of($driver, LlmGatewayContract::class)) {
                throw new RuntimeException(
                    "Pilote d'IA « {$name} » inconnu. Vérifiez ASSISTANT_AI_DRIVER et config/assistant.php.",
                );
            }

            return $driver === ClaudeLlmProvider::class
                ? new ClaudeLlmProvider(
                    apiKey: (string) config('assistant.claude.key'),
                    model: (string) config('assistant.claude.respond_model'),
                    baseUrl: (string) config('assistant.claude.base_url'),
                    maxTokens: (int) config('assistant.claude.max_tokens'),
                )
                : $this->app->make($driver);
        });
    }

    /**
     * Canal de notification (section 5.10, 9), choisi par configuration.
     *
     * Un seul canal au stade du socle (`array`, pour les tests et le
     * développement) ; les adaptateurs réels (FCM/APNs, SMS, WhatsApp,
     * e-mail) sont ajoutés avec le durcissement du domaine `Notification`
     * (planning, phase 7).
     */
    private function registerNotificationChannel(): void
    {
        $this->app->singleton(ArrayNotificationChannel::class);
        $this->app->singleton(NotificationChannelContract::class, ArrayNotificationChannel::class);
    }
}
