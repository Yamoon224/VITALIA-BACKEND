<?php

namespace App\Domains\HealthRecord\Http\Controllers;

use App\Domains\HealthRecord\Contracts\AgentMemoryRepositoryContract;
use App\Domains\HealthRecord\Http\Resources\AgentMemoryEntryResource;
use App\Domains\HealthRecord\Services\AgentMemoryService;
use App\Http\Controllers\Controller;
use App\Models\AgentMemoryEntry;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

/**
 * L'utilisateur voit, corrige et supprime ce que l'agent a retenu
 * (section 5.8) : cette file de propositions est la seule porte d'entrée
 * vers la mémoire persistante.
 */
class AgentMemoryController extends Controller
{
    public function __construct(
        private readonly AgentMemoryService $memory,
        private readonly AgentMemoryRepositoryContract $repository,
    ) {}

    public function pending(Request $request): JsonResponse
    {
        return response()->json([
            'data' => AgentMemoryEntryResource::collection($this->memory->pendingFor($request->user())),
        ]);
    }

    public function accept(Request $request, int $entry): AgentMemoryEntryResource
    {
        $model = $this->findOwned($request, $entry);

        return new AgentMemoryEntryResource($this->memory->accept($model));
    }

    public function reject(Request $request, int $entry): AgentMemoryEntryResource
    {
        $model = $this->findOwned($request, $entry);

        return new AgentMemoryEntryResource($this->memory->reject($model));
    }

    private function findOwned(Request $request, int $entryId): AgentMemoryEntry
    {
        $model = $this->repository->find($entryId);

        abort_if($model === null || $model->user_id !== $request->user()->id, 404);

        return $model;
    }
}
