<?php

namespace App\Domains\Shared\Exceptions;

use Illuminate\Http\JsonResponse;
use RuntimeException;

/**
 * Base de toute exception métier applicative.
 *
 * Portée par un code d'erreur stable (`error_code`) que les clients (mobile,
 * admin web) peuvent tester sans dépendre du texte du message, et par un
 * statut HTTP explicite. `bootstrap/app.php` attrape ce type une seule fois
 * et délègue le rendu à `render()` : aucun contrôleur n'intercepte une
 * exception métier lui-même.
 */
abstract class DomainException extends RuntimeException
{
    /** @param  array<string, mixed>  $context */
    public function __construct(
        string $message,
        private readonly string $errorCode,
        private readonly int $status = 422,
        private readonly array $context = [],
    ) {
        parent::__construct($message);
    }

    public function render(): JsonResponse
    {
        return response()->json([
            'message' => $this->getMessage(),
            'error_code' => $this->errorCode,
            'context' => (object) $this->context,
        ], $this->status);
    }
}
