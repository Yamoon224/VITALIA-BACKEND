<?php

namespace App\Domains\Notification\Http\Controllers;

use App\Domains\Notification\Http\Resources\ReminderResource;
use App\Domains\Notification\Services\ReminderService;
use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ReminderController extends Controller
{
    public function __construct(private readonly ReminderService $reminders) {}

    public function index(Request $request): JsonResponse
    {
        return response()->json([
            'data' => ReminderResource::collection($this->reminders->upcomingFor($request->user())),
        ]);
    }
}
