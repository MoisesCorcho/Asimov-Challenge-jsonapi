<?php

namespace App\Http\Controllers;

use App\Models\Appointment;
use App\Rules\WeekendsRule;

use Illuminate\Http\Request;
use App\Rules\CrossHoursRule;
use App\Rules\OfficeTimeRule;
use App\Rules\TimeIsNotInThePastRule;
use App\Http\Resources\AppointmentResource;
use App\Http\Resources\AppointmentCollection;
use App\Http\Requests\StoreAppointmentRequest;
use App\Http\Requests\UpdateAppointmentRequest;

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
        $request->validate([
            'data.attributes.date' => [
                'required',
                'date_format:Y-m-d',
                'after_or_equal:'.now()->toDateString(),
                new WeekendsRule
            ],
            'data.attributes.start_time' => [
                'required',
                'date_format:H:i',
                new TimeIsNotInThePastRule,
                new OfficeTimeRule,
                new CrossHoursRule
            ],
            'data.attributes.email' => [
                'required',
                'email'
            ]
        ]);

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
    public function update(Request $request, Appointment $appointment)
    {

        $request->validate([
            'data.attributes.date' => [
                'required',
                'date_format:Y-m-d',
                'after_or_equal:'.now()->toDateString(),
                new WeekendsRule
            ],
            'data.attributes.start_time' => [
                'required',
                'date_format:H:i',
                new TimeIsNotInThePastRule,
                new OfficeTimeRule,
                new CrossHoursRule
            ],
            'data.attributes.email' => [
                'required',
                'email'
            ]
        ]);

        $appointment->update([
            'date' => $request->input('data.attributes.date'),
            'start_time' => $request->input('data.attributes.start_time'),
            'email' => $request->input('data.attributes.email')
        ]);

        return AppointmentResource::make($appointment);
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
