<?php

namespace App\Domains\Notification\Enums;

/** Rappels (section 5.10) : prise de médicaments, rendez-vous, examens, vaccinations. */
enum ReminderType: string
{
    case Medication = 'medication';
    case Appointment = 'appointment';
    case Examination = 'examination';
    case Vaccination = 'vaccination';
    case FollowUp = 'follow_up';
}
