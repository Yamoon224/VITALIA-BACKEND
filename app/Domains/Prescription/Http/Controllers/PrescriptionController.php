<?php

namespace App\Domains\Prescription\Http\Controllers;

use App\Domains\Prescription\Contracts\PrescriptionRepositoryContract;
use App\Domains\Prescription\Http\Resources\PrescriptionResource;
use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

/**
 * Côté patient : ses ordonnances, à tous les stades (section 5.14,
 * « ordonnances reçues » dans l'application mobile). La revue et la
 * signature restent réservées à l'espace praticien
 * (`PrescriptionReviewController`, section 5.13).
 */
class PrescriptionController extends Controller
{
    public function __construct(private readonly PrescriptionRepositoryContract $prescriptions) {}

    public function index(Request $request): JsonResponse
    {
        return response()->json([
            'data' => PrescriptionResource::collection($this->prescriptions->forPatient($request->user())),
        ]);
    }
}
