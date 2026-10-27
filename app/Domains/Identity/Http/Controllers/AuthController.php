<?php

namespace App\Domains\Identity\Http\Controllers;

use App\Domains\Identity\Exceptions\InvalidCredentialsException;
use App\Domains\Identity\Http\Requests\LoginRequest;
use App\Domains\Identity\Http\Requests\RegisterRequest;
use App\Domains\Identity\Http\Resources\UserResource;
use App\Domains\Identity\Services\RegistrationService;
use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

/**
 * Inscription et session (section 5.1).
 *
 * Volontairement minimal au stade du socle : l'OTP par SMS, la biométrie et
 * la double authentification (section 8) sont ajoutées avec le domaine
 * `Notification` et le durcissement sécurité (planning, phase 4).
 */
class AuthController extends Controller
{
    public function __construct(private readonly RegistrationService $registrations) {}

    public function register(RegisterRequest $request): JsonResponse
    {
        $user = $this->registrations->register($request->validated());

        $token = $user->createToken('mobile')->plainTextToken;

        return response()->json([
            'user' => new UserResource($user),
            'token' => $token,
        ], 201);
    }

    /**
     * Connexion par e-mail et mot de passe (interface web, section 5.13).
     *
     * L'application mobile se connecte par OTP (section 5.1) : cette route
     * sert le personnel (praticiens, référents, administrateurs, support).
     */
    public function login(LoginRequest $request): JsonResponse
    {
        $user = User::query()->where('email', $request->string('email')->toString())->first();

        if (! $user || ! Hash::check($request->string('password')->toString(), $user->password)) {
            throw InvalidCredentialsException::make();
        }

        $token = $user->createToken('admin-web')->plainTextToken;

        return response()->json([
            'user' => new UserResource($user),
            'token' => $token,
        ]);
    }

    public function me(Request $request): UserResource
    {
        return new UserResource($request->user());
    }

    public function logout(Request $request): JsonResponse
    {
        $request->user()?->currentAccessToken()?->delete();

        return response()->json(['message' => 'Déconnecté.']);
    }
}
