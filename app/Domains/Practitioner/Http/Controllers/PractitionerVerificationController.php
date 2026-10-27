<?php

namespace App\Domains\Practitioner\Http\Controllers;

use App\Domains\Practitioner\Http\Resources\PractitionerProfileResource;
use App\Http\Controllers\Controller;
use App\Models\PractitionerProfile;
use Illuminate\Http\JsonResponse;

/**
 * Processus de vérification des diplômes et inscriptions à l'ordre
 * (section 5.13, 14) : réservé aux administrateurs. `verified_at` n'est
 * jamais renseigné par le praticien lui-même.
 */
class PractitionerVerificationController extends Controller
{
    public function pending(): JsonResponse
    {
        return response()->json([
            'data' => PractitionerProfileResource::collection(
                PractitionerProfile::query()->whereNull('verified_at')->with('user')->get(),
            ),
        ]);
    }

    public function verify(int $practitioner): PractitionerProfileResource
    {
        $profile = PractitionerProfile::query()->where('user_id', $practitioner)->firstOrFail();
        $profile->verified_at = now();
        $profile->save();

        return new PractitionerProfileResource($profile);
    }

    public function revoke(int $practitioner): PractitionerProfileResource
    {
        $profile = PractitionerProfile::query()->where('user_id', $practitioner)->firstOrFail();
        $profile->verified_at = null;
        $profile->save();

        return new PractitionerProfileResource($profile);
    }
}
