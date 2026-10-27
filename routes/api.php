<?php

use App\Domains\Assistant\Http\Controllers\PromptVersionController;
use App\Domains\Audit\Http\Controllers\AuditLogController;
use App\Domains\Audit\Http\Controllers\SupervisionController;
use App\Domains\Billing\Http\Controllers\SubscriptionController;
use App\Domains\Consultation\Http\Controllers\ConsultationController;
use App\Domains\Conversation\Http\Controllers\ConversationController;
use App\Domains\Examination\Http\Controllers\ExaminationRecommendationController;
use App\Domains\Examination\Http\Controllers\ExaminationRequestController;
use App\Domains\Examination\Http\Controllers\ExaminationResultController;
use App\Domains\Examination\Http\Controllers\ExaminationReviewController;
use App\Domains\Examination\Http\Controllers\LaboratoryDirectoryController;
use App\Domains\HealthRecord\Http\Controllers\AgentMemoryController;
use App\Domains\HealthRecord\Http\Controllers\HealthProfileController;
use App\Domains\Identity\Http\Controllers\AuthController;
use App\Domains\Notification\Http\Controllers\NotificationPreferenceController;
use App\Domains\Notification\Http\Controllers\ReminderController;
use App\Domains\Practitioner\Http\Controllers\AppointmentController;
use App\Domains\Practitioner\Http\Controllers\PractitionerDirectoryController;
use App\Domains\Practitioner\Http\Controllers\PractitionerVerificationController;
use App\Domains\Prescription\Http\Controllers\PrescriptionController;
use App\Domains\Prescription\Http\Controllers\PrescriptionReviewController;
use App\Domains\Wellness\Http\Controllers\MoodController;
use App\Domains\Wellness\Http\Controllers\WellnessGoalController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API REST versionnée (section 9) — préfixe api/v1 fixé dans bootstrap/app.php
|--------------------------------------------------------------------------
|
| Consommée par l'application mobile Flutter (utilisateurs) et par
| l'interface web NextJS (administration, praticiens) — section 1.
|
*/

Route::post('/auth/register', [AuthController::class, 'register']);
Route::post('/auth/login', [AuthController::class, 'login']);

Route::middleware('auth:sanctum')->group(function (): void {
    Route::post('/auth/logout', [AuthController::class, 'logout']);
    Route::get('/auth/me', [AuthController::class, 'me']);

    // --- Conversations (app mobile — utilisateurs standard et aidants) -------
    Route::get('/conversations', [ConversationController::class, 'index']);
    Route::post('/conversations', [ConversationController::class, 'store']);
    Route::get('/conversations/{conversation}', [ConversationController::class, 'show']);
    Route::post('/conversations/{conversation}/messages', [ConversationController::class, 'sendMessage']);
    Route::post('/conversations/{conversation}/messages/stream', [ConversationController::class, 'streamMessage']);
    Route::patch('/conversations/{conversation}/messages/{message}/feedback', [ConversationController::class, 'rateMessage']);

    // --- Consultation guidée (anamnèse, section 5.3) -----------------------------
    Route::post('/conversations/{conversation}/consultation/start', [ConsultationController::class, 'start']);
    Route::get('/conversations/{conversation}/consultation/next-question', [ConsultationController::class, 'nextQuestion']);
    Route::post('/conversations/{conversation}/consultation/answer', [ConsultationController::class, 'answer']);
    Route::post('/conversations/{conversation}/consultation/finish', [ConsultationController::class, 'finish']);

    // --- Dossier santé et mémoire de l'agent (section 5.8) ------------------------
    Route::get('/health-profile', [HealthProfileController::class, 'show']);
    Route::put('/health-profile', [HealthProfileController::class, 'update']);
    Route::get('/agent-memory/pending', [AgentMemoryController::class, 'pending']);
    Route::post('/agent-memory/{entry}/accept', [AgentMemoryController::class, 'accept']);
    Route::post('/agent-memory/{entry}/reject', [AgentMemoryController::class, 'reject']);

    // --- Ordonnances du patient (section 5.14) --------------------------------------
    Route::get('/prescriptions', [PrescriptionController::class, 'index']);

    // --- Examens (section 5.6) ----------------------------------------------------
    Route::post('/conversations/{conversation}/examination-recommendation', [ExaminationRecommendationController::class, 'store']);
    Route::get('/examination-requests', [ExaminationRequestController::class, 'index']);
    Route::get('/examination-results', [ExaminationResultController::class, 'index']);
    Route::post('/examination-results', [ExaminationResultController::class, 'store']);
    Route::get('/laboratories', [LaboratoryDirectoryController::class, 'index']);

    // --- Annuaire et rendez-vous (section 5.11) -----------------------------------
    Route::get('/practitioners', [PractitionerDirectoryController::class, 'index']);
    Route::get('/appointments', [AppointmentController::class, 'index']);
    Route::post('/appointments', [AppointmentController::class, 'store']);
    Route::post('/appointments/{appointment}/cancel', [AppointmentController::class, 'cancel']);

    // --- Bien-être (section 5.9) ---------------------------------------------------
    Route::get('/wellness/goals', [WellnessGoalController::class, 'index']);
    Route::post('/wellness/goals', [WellnessGoalController::class, 'store']);
    Route::get('/wellness/mood', [MoodController::class, 'index']);
    Route::post('/wellness/mood', [MoodController::class, 'store']);

    // --- Abonnement et quotas (section 5.12) ----------------------------------------
    Route::get('/subscription', [SubscriptionController::class, 'show']);
    Route::post('/subscription/subscribe', [SubscriptionController::class, 'subscribe']);
    Route::post('/subscription/cancel', [SubscriptionController::class, 'cancel']);

    // --- Rappels et préférences de notification (section 5.10) -----------------------
    Route::get('/reminders', [ReminderController::class, 'index']);
    Route::get('/notification-preferences', [NotificationPreferenceController::class, 'show']);
    Route::put('/notification-preferences', [NotificationPreferenceController::class, 'update']);

    // --- Espace praticien (interface web — praticiens uniquement, section 5.13) -------
    Route::middleware('role:practitioner')->prefix('practitioner')->group(function (): void {
        Route::get('/prescriptions/pending', [PrescriptionReviewController::class, 'pending']);
        Route::post('/prescriptions/{prescription}/sign', [PrescriptionReviewController::class, 'sign']);
        Route::post('/prescriptions/{prescription}/refuse', [PrescriptionReviewController::class, 'refuse']);

        Route::get('/examinations/pending', [ExaminationReviewController::class, 'pending']);
        Route::post('/examinations/{examination}/validate', [ExaminationReviewController::class, 'validateRequest']);
        Route::post('/examinations/{examination}/refuse', [ExaminationReviewController::class, 'refuse']);

        Route::post('/appointments/{appointment}/confirm', [AppointmentController::class, 'confirm']);
    });

    // --- Supervision clinique et gestion (interface web — section 5.13) --------------
    Route::middleware('role:administrator,clinical_referent')->prefix('admin')->group(function (): void {
        Route::get('/supervision/stats', [SupervisionController::class, 'stats']);
        Route::get('/supervision/flagged-messages', [SupervisionController::class, 'flaggedMessages']);

        Route::get('/prompts/{blockKey}/history', [PromptVersionController::class, 'history']);
        Route::post('/prompts', [PromptVersionController::class, 'store']);
        Route::post('/prompts/{version}/publish', [PromptVersionController::class, 'publish']);

        // Vérification des diplômes et inscriptions (section 5.13, 14) :
        // réservée aux administrateurs, même au sein de ce groupe déjà
        // restreint — un référent clinique ne décide pas des habilitations.
        Route::middleware('role:administrator')->group(function (): void {
            Route::prefix('practitioners')->group(function (): void {
                Route::get('/pending', [PractitionerVerificationController::class, 'pending']);
                Route::post('/{practitioner}/verify', [PractitionerVerificationController::class, 'verify']);
                Route::post('/{practitioner}/revoke', [PractitionerVerificationController::class, 'revoke']);
            });

            // Lecture seule, append-only (section 5.13, 8) : aucune route de
            // mise à jour ni de suppression n'existe pour `audit_logs`.
            Route::get('/audit-logs', [AuditLogController::class, 'index']);
        });
    });
});
