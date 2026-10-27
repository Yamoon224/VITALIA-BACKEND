<?php

namespace App\Domains\Shared\Support;

use App\Domains\Shared\Contracts\TransactionManagerContract;
use Closure;
use Illuminate\Support\Facades\DB;

class DatabaseTransactionManager implements TransactionManagerContract
{
    public function run(Closure $callback): mixed
    {
        return DB::transaction($callback);
    }
}
