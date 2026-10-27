<?php

namespace App\Domains\Examination\Http\Controllers;

use App\Domains\Examination\Http\Requests\RecordExaminationResultRequest;
use App\Domains\Examination\Http\Resources\ExaminationResultResource;
use App\Domains\Examination\Services\ResultInterpretationService;
use App\Http\Controllers\Controller;
use App\Models\ExaminationResult;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

/**
 * Historique des examens (section 5.6, 5.8) : point d'entrée pour la saisie
 * manuelle de résultats, en attendant l'extraction automatique depuis un
 * document (domaine `HealthRecord`, OCR — hors scaffolding).
 */
class ExaminationResultController extends Controller
{
    public function __construct(private readonly ResultInterpretationService $interpretation) {}

    public function index(Request $request): JsonResponse
    {
        return response()->json([
            'data' => ExaminationResultResource::collection(
                ExaminationResult::query()
                    ->where('user_id', $request->user()->id)
                    ->latest('recorded_at')
                    ->get(),
            ),
        ]);
    }

    public function store(RecordExaminationResultRequest $request): ExaminationResultResource
    {
        $result = $this->interpretation->record(
            $request->user(),
            $request->validated(),
            $request->integer('examination_request_id') ?: null,
        );

        return new ExaminationResultResource($result);
    }
}
