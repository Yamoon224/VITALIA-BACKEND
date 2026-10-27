<?php

use App\Domains\Shared\Exceptions\DomainException;
use App\Domains\Shared\Http\Middleware\RequireRole;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Http\Exceptions\ThrottleRequestsException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        api: __DIR__.'/../routes/api.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
        apiPrefix: 'api/v1',
    )
    // Les commandes planifiées vivent par domaine (`app/Domains/*/Console`),
    // pas dans `app/Console/Commands` (section 9).
    ->withCommands([app_path('Domains')])
    ->withMiddleware(function (Middleware $middleware): void {
        $middleware->alias([
            // Exige un des rôles donnés (section 4, 8) : `role:practitioner`,
            // `role:administrator,clinical_referent`, etc.
            'role' => RequireRole::class,
        ]);

        // API pure, sans page de connexion côté serveur (section 1 : seule
        // l'application mobile sert les utilisateurs standard).
        $middleware->redirectGuestsTo(fn () => null);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        /*
         * Gestion centralisée des erreurs (section 10.3) : un seul endroit
         * décide de la forme d'une réponse d'erreur, aucun contrôleur
         * n'intercepte quoi que ce soit.
         *
         * Contrat : { "message": string, "error_code": string, "context": object }
         * enrichi de "errors" pour les erreurs de validation (422).
         */
        $wantsJson = fn (Request $request): bool => $request->is('api/*') || $request->expectsJson();

        /** @param  array<string, mixed>  $context */
        $error = fn (string $message, string $code, int $status, array $context = []): JsonResponse => response()->json([
            'message' => $message,
            'error_code' => $code,
            'context' => (object) $context,
        ], $status);

        $exceptions->shouldRenderJsonWhen($wantsJson);

        $exceptions->render(fn (ValidationException $exception, Request $request) => $wantsJson($request)
            ? response()->json([
                'message' => $exception->getMessage(),
                'error_code' => 'validation_failed',
                'errors' => $exception->errors(),
                'context' => (object) [],
            ], 422)
            : null);

        $exceptions->render(fn (DomainException $exception, Request $request) => $wantsJson($request)
            ? $exception->render()
            : null);

        $exceptions->render(fn (AuthenticationException $exception, Request $request) => $wantsJson($request)
            ? $error('Non authentifié. Reconnectez-vous.', 'unauthenticated', 401)
            : null);

        $exceptions->render(fn (ModelNotFoundException $exception, Request $request) => $wantsJson($request)
            ? $error('Ressource introuvable.', 'not_found', 404)
            : null);

        $exceptions->render(fn (NotFoundHttpException $exception, Request $request) => $wantsJson($request)
            ? $error('Ressource introuvable.', 'not_found', 404)
            : null);

        $exceptions->render(fn (ThrottleRequestsException $exception, Request $request) => $wantsJson($request)
            ? $error(
                'Trop de tentatives. Patientez avant de réessayer.',
                'too_many_requests',
                429,
                ['retry_after_seconds' => (int) ($exception->getHeaders()['Retry-After'] ?? 0)],
            )->withHeaders($exception->getHeaders())
            : null);
    })->create();
