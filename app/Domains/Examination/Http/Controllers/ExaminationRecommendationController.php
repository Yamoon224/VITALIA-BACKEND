<?php

namespace App\Domains\Examination\Http\Controllers;

use App\Domains\Conversation\Contracts\ConversationRepositoryContract;
use App\Domains\Examination\Http\Resources\ExaminationRequestResource;
use App\Domains\Examination\Services\ExaminationRecommendationService;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class ExaminationRecommendationController extends Controller
{
    public function __construct(
        private readonly ExaminationRecommendationService $recommendations,
        private readonly ConversationRepositoryContract $conversations,
    ) {}

    public function store(Request $request, int $conversation): ExaminationRequestResource
    {
        $model = $this->conversations->findForUser($conversation, $request->user());

        abort_if($model === null, 404);

        return new ExaminationRequestResource(
            $this->recommendations->generate($request->user(), $model->load('messages')),
        );
    }
}
