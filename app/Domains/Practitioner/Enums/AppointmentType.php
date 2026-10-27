<?php

namespace App\Domains\Practitioner\Enums;

enum AppointmentType: string
{
    case Teleconsultation = 'teleconsultation';
    case InPerson = 'in_person';
}
