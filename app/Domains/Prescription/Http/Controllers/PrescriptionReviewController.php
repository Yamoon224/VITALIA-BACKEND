<?php

namespace App\Domains\Prescription\Http\Controllers;

use App\Domains\Prescription\Contracts\PrescriptionRepositoryContract;
use App\Domains\Prescription\Http\Requests\RefusePrescriptionRequest;
use App\Domains\Prescription\Http\Resources\PrescriptionResource;
use App\Domains\Prescription\Services\PrescriptionReviewService;
use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

/**
 * File de revue praticien (section 5.13) : consommée par l'interface web
 * d'administration, jamais par l'application mobile (section 4.1). La route
 * est protégée par le middleware `role:practitioner` (section 8).
 */
class PrescriptionReviewController extends Controller
{
    public function __construct(
        private readonly PrescriptionReviewService $review,
        private readonly PrescriptionRepositoryContract $repository,
    ) {}

    public function pending(): JsonResponse
    {
        return response()->json([
            'data' => PrescriptionResource::collection($this->repository->pendingReview()),
        ]);
    }

    public function sign(Request $request, int $prescription): PrescriptionResource
    {
        $model = $this->repository->find($prescription);

        abort_if($model === null, 404);

        return new PrescriptionResource($this->review->sign($model, $request->user()));
    }

    public function refuse(RefusePrescriptionRequest $request, int $prescription): PrescriptionResource
    {
        $model = $this->repository->find($prescription);

        abort_if($model === null, 404);

        return new PrescriptionResource(
            $this->review->refuse($model, $request->user(), $request->string('reason')->toString()),
        );
    }
}
