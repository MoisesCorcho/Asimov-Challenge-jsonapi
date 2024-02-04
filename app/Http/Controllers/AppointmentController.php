<?php

namespace App\Http\Controllers;

use App\Models\Appointment;
use Illuminate\Http\Request;

use App\Http\Requests\StoreAppointmentRequest;
use App\Http\Requests\UpdateAppointmentRequest;
use App\Http\Resources\AppointmentCollection;
use App\Http\Resources\AppointmentResource;

class AppointmentController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $appointments = Appointment::all();

        return new AppointmentCollection($appointments);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {

        $appointment = Appointment::create([
            'date' => $request->input('data.attributes.date'),
            'start_time' => $request->input('data.attributes.start_time'),
            'email' => $request->input('data.attributes.email')
        ]);

        return AppointmentResource::make($appointment);
    }

    /**
     * Display the specified resource.
     */
    public function show(Appointment $appointment)
    {
        return new AppointmentResource($appointment);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateAppointmentRequest $request, Appointment $appointment)
    {
        $appointment->date = $request->date;
        $appointment->start_time = $request->start_time;
        $appointment->email = $request->email;
        $appointment->update();

        return response()->json([
            'message' => 'Appointment successfully updated.'
        ]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Appointment $appointment)
    {
        Appointment::destroy($appointment->id);

        return response()->json([
            'message' => 'Appointment successfully deleted.'
        ]);
    }
}
