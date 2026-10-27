<?php

namespace App\Domains\Wellness\Http\Controllers;

use App\Domains\Wellness\Enums\WellnessGoalType;
use App\Domains\Wellness\Http\Requests\SetWellnessGoalRequest;
use App\Domains\Wellness\Http\Resources\WellnessGoalResource;
use App\Domains\Wellness\Services\WellnessService;
use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class WellnessGoalController extends Controller
{
    public function __construct(private readonly WellnessService $wellness) {}

    public function index(Request $request): JsonResponse
    {
        return response()->json([
            'data' => WellnessGoalResource::collection($this->wellness->goalsFor($request->user())),
        ]);
    }

    public function store(SetWellnessGoalRequest $request): WellnessGoalResource
    {
        $goal = $this->wellness->setGoal(
            $request->user(),
            $request->enum('type', WellnessGoalType::class),
            (float) $request->input('target_value'),
        );

        return new WellnessGoalResource($goal);
    }
}
