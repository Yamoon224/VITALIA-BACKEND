<?php

namespace App\Domains\Wellness\Http\Controllers;

use App\Domains\Wellness\Contracts\MoodEntryRepositoryContract;
use App\Domains\Wellness\Http\Requests\LogMoodRequest;
use App\Domains\Wellness\Http\Resources\MoodEntryResource;
use App\Domains\Wellness\Services\WellnessService;
use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class MoodController extends Controller
{
    public function __construct(
        private readonly WellnessService $wellness,
        private readonly MoodEntryRepositoryContract $moodEntries,
    ) {}

    public function index(Request $request): JsonResponse
    {
        return response()->json([
            'data' => MoodEntryResource::collection($this->moodEntries->recentFor($request->user(), 30)),
        ]);
    }

    public function store(LogMoodRequest $request): MoodEntryResource
    {
        $entry = $this->wellness->logMood(
            $request->user(),
            $request->integer('mood_level'),
            $request->string('note')->toString() ?: null,
        );

        return new MoodEntryResource($entry);
    }
}
