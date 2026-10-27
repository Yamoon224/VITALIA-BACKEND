<?php

namespace App\Domains\Consultation\Events;

use App\Models\ConsultationSession;
use Illuminate\Foundation\Events\Dispatchable;

class ConsultationCompleted
{
    use Dispatchable;

    public function __construct(public readonly ConsultationSession $session) {}
}
