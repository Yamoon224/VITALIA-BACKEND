<?php

namespace App\Domains\Examination\Http\Controllers;

use App\Domains\Examination\Http\Resources\LaboratoryResource;
use App\Http\Controllers\Controller;
use App\Models\Laboratory;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class LaboratoryDirectoryController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $city = $request->string('city')->toString();

        return response()->json([
            'data' => LaboratoryResource::collection(
                Laboratory::query()
                    ->when($city !== '', fn ($query) => $query->where('city', $city))
                    ->get(),
            ),
        ]);
    }
}
