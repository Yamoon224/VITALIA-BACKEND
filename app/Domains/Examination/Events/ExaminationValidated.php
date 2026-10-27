<?php

namespace App\Domains\Examination\Events;

use App\Models\ExaminationRequest;
use Illuminate\Foundation\Events\Dispatchable;

class ExaminationValidated
{
    use Dispatchable;

    public function __construct(public readonly ExaminationRequest $request) {}
}
