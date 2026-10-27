<?php

namespace App\Domains\Notification\Http\Controllers;

use App\Domains\Notification\Contracts\NotificationPreferenceRepositoryContract;
use App\Domains\Notification\Http\Requests\UpdateNotificationPreferenceRequest;
use App\Domains\Notification\Http\Resources\NotificationPreferenceResource;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class NotificationPreferenceController extends Controller
{
    public function __construct(private readonly NotificationPreferenceRepositoryContract $preferences) {}

    public function show(Request $request): NotificationPreferenceResource
    {
        return new NotificationPreferenceResource($this->preferences->forUser($request->user()));
    }

    public function update(UpdateNotificationPreferenceRequest $request): NotificationPreferenceResource
    {
        return new NotificationPreferenceResource(
            $this->preferences->update($request->user(), $request->validated()),
        );
    }
}
