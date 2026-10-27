<?php

namespace App\Domains\Assistant\Http\Controllers;

use App\Domains\Assistant\Contracts\PromptVersionRepositoryContract;
use App\Domains\Assistant\Http\Requests\DraftPromptVersionRequest;
use App\Domains\Assistant\Http\Resources\PromptVersionResource;
use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

/**
 * Brouillon, revue, approbation, retour arrière (section 5.13). Réservé aux
 * administrateurs et référents cliniques ; toute modification du prompt
 * suit le même cycle de vie que le code (versionné, relu, réversible).
 */
class PromptVersionController extends Controller
{
    public function __construct(private readonly PromptVersionRepositoryContract $versions) {}

    public function history(string $blockKey): JsonResponse
    {
        return response()->json([
            'data' => PromptVersionResource::collection($this->versions->historyFor($blockKey)),
        ]);
    }

    public function store(DraftPromptVersionRequest $request): PromptVersionResource
    {
        $version = $this->versions->draft(
            $request->string('block_key')->toString(),
            $request->string('content')->toString(),
            $request->user(),
        );

        return new PromptVersionResource($version);
    }

    public function publish(Request $request, int $version): PromptVersionResource
    {
        $model = $this->versions->find($version);

        abort_if($model === null, 404);

        return new PromptVersionResource($this->versions->publish($model));
    }
}
