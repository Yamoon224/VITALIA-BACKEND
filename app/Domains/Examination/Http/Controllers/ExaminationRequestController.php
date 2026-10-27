<?php

namespace App\Domains\Examination\Http\Controllers;

use App\Domains\Examination\Contracts\ExaminationRequestRepositoryContract;
use App\Domains\Examination\Http\Resources\ExaminationRequestResource;
use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

/**
 * Côté patient : ses demandes d'examens, à tous les stades (section 5.6).
 * La revue et la validation restent réservées à l'espace praticien
 * (`ExaminationReviewController`, section 5.13).
 */
class ExaminationRequestController extends Controller
{
    public function __construct(private readonly ExaminationRequestRepositoryContract $requests) {}

    public function index(Request $request): JsonResponse
    {
        return response()->json([
            'data' => ExaminationRequestResource::collection($this->requests->forPatient($request->user())),
        ]);
    }
}
