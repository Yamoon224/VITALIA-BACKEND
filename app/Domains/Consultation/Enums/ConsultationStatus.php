<?php

namespace App\Domains\Consultation\Enums;

enum ConsultationStatus: string
{
    case InProgress = 'in_progress';
    case Completed = 'completed';
}
