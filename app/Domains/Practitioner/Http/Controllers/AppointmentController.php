<?php

namespace App\Domains\Practitioner\Http\Controllers;

use App\Domains\Practitioner\Contracts\AppointmentRepositoryContract;
use App\Domains\Practitioner\Http\Requests\RequestAppointmentRequest;
use App\Domains\Practitioner\Http\Resources\AppointmentResource;
use App\Domains\Practitioner\Services\AppointmentService;
use App\Http\Controllers\Controller;
use App\Models\Appointment;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class AppointmentController extends Controller
{
    public function __construct(
        private readonly AppointmentService $appointments,
        private readonly AppointmentRepositoryContract $repository,
    ) {}

    public function index(Request $request): JsonResponse
    {
        $appointments = $request->user()->hasRole('practitioner')
            ? $this->repository->forPractitioner($request->user())
            : $this->repository->forPatient($request->user());

        return response()->json(['data' => AppointmentResource::collection($appointments)]);
    }

    public function store(RequestAppointmentRequest $request): AppointmentResource
    {
        $practitioner = User::query()->findOrFail($request->integer('practitioner_id'));

        $appointment = $this->appointments->request(
            $request->user(),
            $practitioner,
            $request->date('scheduled_at'),
            $request->string('type')->toString(),
        );

        return new AppointmentResource($appointment);
    }

    public function confirm(int $appointment): AppointmentResource
    {
        return new AppointmentResource($this->appointments->confirm($this->findOrFail($appointment)));
    }

    public function cancel(int $appointment): AppointmentResource
    {
        return new AppointmentResource($this->appointments->cancel($this->findOrFail($appointment)));
    }

    private function findOrFail(int $id): Appointment
    {
        $model = $this->repository->find($id);

        abort_if($model === null, 404);

        return $model;
    }
}
