<?php

namespace App\Domains\HealthRecord\Http\Controllers;

use App\Domains\HealthRecord\Http\Requests\UpdateHealthProfileRequest;
use App\Domains\HealthRecord\Http\Resources\HealthProfileResource;
use App\Domains\HealthRecord\Services\HealthProfileService;
use App\Http\Controllers\Controller;
use App\Models\HealthProfile;
use Illuminate\Http\Request;

class HealthProfileController extends Controller
{
    public function __construct(private readonly HealthProfileService $profiles) {}

    public function show(Request $request): HealthProfileResource
    {
        $profile = $this->profiles->show($request->user()) ?? new HealthProfile(['user_id' => $request->user()->id]);

        return new HealthProfileResource($profile);
    }

    public function update(UpdateHealthProfileRequest $request): HealthProfileResource
    {
        return new HealthProfileResource($this->profiles->update($request->user(), $request->validated()));
    }
}
