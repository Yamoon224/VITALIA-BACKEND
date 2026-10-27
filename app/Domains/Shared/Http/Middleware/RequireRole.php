<?php

namespace App\Domains\Shared\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Exige qu'un des rôles donnés soit porté par le compte authentifié.
 *
 * Cloisonnement par rôle (section 4) : un utilisateur standard n'a aucun
 * accès à l'interface d'administration ; un praticien n'accède qu'à son
 * espace de revue. Le filtrage se fait ici, côté serveur, jamais seulement
 * dans l'interface cliente (section 11.2).
 */
class RequireRole
{
    public function handle(Request $request, Closure $next, string ...$roles): Response
    {
        $user = $request->user();

        if (! $user || ! $user->hasAnyRole($roles)) {
            return response()->json([
                'message' => 'Accès refusé pour ce rôle.',
                'error_code' => 'forbidden_role',
                'context' => (object) ['required_roles' => $roles],
            ], 403);
        }

        return $next($request);
    }
}
