<?php

namespace App\Domains\Practitioner\Http\Controllers;

use App\Domains\Practitioner\Contracts\PractitionerDirectoryRepositoryContract;
use App\Domains\Practitioner\Http\Resources\PractitionerProfileResource;
use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

/** Annuaire de praticiens vérifiés (section 5.11), consommé par l'application mobile. */
class PractitionerDirectoryController extends Controller
{
    public function __construct(private readonly PractitionerDirectoryRepositoryContract $directory) {}

    public function index(Request $request): JsonResponse
    {
        return response()->json([
            'data' => PractitionerProfileResource::collection(
                $this->directory->verified($request->string('specialty')->toString() ?: null),
            ),
        ]);
    }
}
