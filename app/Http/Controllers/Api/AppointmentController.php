<?php

namespace App\Http\Controllers\Api;

use App\Models\Appointment;

use Illuminate\Http\Response;
use App\Http\Controllers\Controller;
use App\Http\Resources\AppointmentResource;
use App\Http\Requests\SaveAppointmentRequest;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class AppointmentController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(): AnonymousResourceCollection
    {
        $appointments = Appointment::query()
            ->allowedIncludes(['category'])
            ->allowedFilters(['date', 'year', 'month', 'start_time', 'email'])
            ->allowedSorts(['date', 'start_time'])
            ->sparseFieldset()
            ->jsonPaginate();

        return AppointmentResource::collection( $appointments );
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(SaveAppointmentRequest $request): AppointmentResource
    {
        $appointment = Appointment::create($request->validated());

        return AppointmentResource::make($appointment);
    }

    /**
     * Display the specified resource.
     */
    public function show($appointment): JsonResource
    {
        $appointment = Appointment::where('id', $appointment)
            ->allowedIncludes(['category'])
            ->sparseFieldset()
            ->firstOrFail();

        return new AppointmentResource($appointment);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(SaveAppointmentRequest $request, Appointment $appointment): AppointmentResource
    {
        $appointment->update($request->validated());

        return AppointmentResource::make($appointment);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Appointment $appointment): Response
    {
        $appointment->delete();

        return response()->noContent();
    }
}
