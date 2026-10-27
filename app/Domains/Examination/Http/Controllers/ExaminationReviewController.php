<?php

namespace App\Domains\Examination\Http\Controllers;

use App\Domains\Examination\Contracts\ExaminationRequestRepositoryContract;
use App\Domains\Examination\Http\Requests\RefuseExaminationRequest;
use App\Domains\Examination\Http\Resources\ExaminationRequestResource;
use App\Domains\Examination\Services\ExaminationReviewService;
use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

/** File de revue praticien (section 5.13), symétrique de `PrescriptionReviewController`. */
class ExaminationReviewController extends Controller
{
    public function __construct(
        private readonly ExaminationReviewService $review,
        private readonly ExaminationRequestRepositoryContract $repository,
    ) {}

    public function pending(): JsonResponse
    {
        return response()->json([
            'data' => ExaminationRequestResource::collection($this->repository->pendingReview()),
        ]);
    }

    public function validateRequest(Request $request, int $examination): ExaminationRequestResource
    {
        $model = $this->repository->find($examination);

        abort_if($model === null, 404);

        return new ExaminationRequestResource($this->review->validate($model, $request->user()));
    }

    public function refuse(RefuseExaminationRequest $request, int $examination): ExaminationRequestResource
    {
        $model = $this->repository->find($examination);

        abort_if($model === null, 404);

        return new ExaminationRequestResource(
            $this->review->refuse($model, $request->user(), $request->string('reason')->toString()),
        );
    }
}
