<?php

namespace App\Http\Controllers;

use App\Models\Appointment;

use Illuminate\Http\Response;
use App\Http\Resources\AppointmentResource;
use App\Http\Requests\SaveAppointmentRequest;
use App\Http\Resources\AppointmentCollection;

class AppointmentController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(): AppointmentCollection
    {
        $appointments = Appointment::query();

        // filter

        $allowedFilters = ['date', 'year', 'month', 'start_time', 'email'];

        foreach (request('filter', []) as $filter => $value) {
            abort_unless(in_array($filter, $allowedFilters), 400);

            if ($filter === 'year') {
                $appointments->whereYear('date', $value);
            } else if ($filter === 'month') {
                $appointments->whereMonth('date', $value);
            } else {
                $appointments->where($filter, 'LIKE', '%'.$value.'%');
            }
        }

        $appointments->allowedSorts(['date', 'start_time']);

        return AppointmentCollection::make( $appointments->jsonPaginate() );
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
