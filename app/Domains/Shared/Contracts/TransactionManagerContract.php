<?php

namespace App\Domains\Shared\Contracts;

use Closure;

/**
 * Exécute un bloc de code dans une transaction de base de données.
 *
 * Isole les services métier de la façade `DB` d'Eloquent : un test peut
 * fournir une implémentation qui n'ouvre aucune transaction réelle.
 */
interface TransactionManagerContract
{
    /**
     * @template TReturn
     *
     * @param  Closure(): TReturn  $callback
     * @return TReturn
     */
    public function run(Closure $callback): mixed;
}
