<?php

namespace App\Domains\Audit\Http\Controllers;

use App\Domains\Conversation\Enums\MessageFeedback;
use App\Domains\Examination\Enums\ExaminationRequestStatus;
use App\Domains\Prescription\Enums\PrescriptionStatus;
use App\Http\Controllers\Controller;
use App\Models\Conversation;
use App\Models\EmergencyDetection;
use App\Models\ExaminationRequest;
use App\Models\Message;
use App\Models\Prescription;
use Illuminate\Http\JsonResponse;

/**
 * Tableau de bord de sécurité (section 5.13) : détections d'urgence, files
 * de revue, retours « inquiétant ». Réservé aux administrateurs et
 * référents cliniques (route protégée par `role:`).
 */
class SupervisionController extends Controller
{
    public function stats(): JsonResponse
    {
        return response()->json(['data' => [
            'emergency_detections_today' => EmergencyDetection::query()->whereDate('created_at', today())->count(),
            'conversations_today' => Conversation::query()->whereDate('created_at', today())->count(),
            'pending_prescriptions' => Prescription::query()->where('status', PrescriptionStatus::InReview)->count(),
            'pending_examinations' => ExaminationRequest::query()->where('status', ExaminationRequestStatus::Draft)->count(),
            'concerning_feedback_count' => Message::query()->where('feedback', MessageFeedback::Concerning)->count(),
        ]]);
    }

    public function flaggedMessages(): JsonResponse
    {
        $messages = Message::query()
            ->where('feedback', MessageFeedback::Concerning)
            ->with('conversation')
            ->latest()
            ->limit(50)
            ->get();

        return response()->json([
            'data' => $messages->map(fn (Message $message) => [
                'id' => $message->id,
                'conversation_id' => $message->conversation_id,
                'patient_id' => $message->conversation->user_id,
                'content' => $message->content,
                'created_at' => $message->created_at,
            ]),
        ]);
    }
}
