<?php

namespace App\Http\Controllers;

use App\Models\Appointment;

use Illuminate\Support\Str;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use App\Http\Resources\AppointmentResource;
use App\Http\Requests\SaveAppointmentRequest;
use App\Http\Resources\AppointmentCollection;

class AppointmentController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request): AppointmentCollection
    {
        $sortField = $request->input('sort');

        $sortDirection = Str::of($sortField)->startsWith('-') ? 'desc' : 'asc';

        $sortField = ltrim($sortField, '-');

        $appointments = Appointment::orderBy($sortField, $sortDirection)->get();

        return AppointmentCollection::make($appointments);
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
    public function show(Appointment $appointment): AppointmentResource
    {
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
