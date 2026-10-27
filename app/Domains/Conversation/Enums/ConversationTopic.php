<?php

namespace App\Domains\Conversation\Enums;

/**
 * Conversations thématisées (section 5.2) : cadre le contexte et les
 * protocoles appliqués (quel bloc de prompt système charger).
 */
enum ConversationTopic: string
{
    case Consultation = 'consultation';
    case ExamResults = 'exam_results';
    case Wellness = 'wellness';
    case FollowUp = 'follow_up';
}
