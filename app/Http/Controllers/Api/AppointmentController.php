<?php

namespace App\Http\Controllers\Api;

use App\Models\Appointment;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use App\Http\Controllers\Controller;
use App\Http\Resources\AppointmentResource;
use App\Http\Requests\SaveAppointmentRequest;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class AppointmentController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:sanctum', [
            'only' => ['store', 'update', 'destroy']
        ]);
    }

    /**
     * Display a listing of the resource.
     */
    public function index(): AnonymousResourceCollection
    {
        $appointments = Appointment::query()
            ->allowedIncludes(['category', 'author', 'comments'])
            ->allowedFilters(['date', 'year', 'month', 'start_time', 'email', 'categories'])
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
        $this->authorize('create', new Appointment());

        $request->validatedData();
        $appointmentData = $request->getAttributes();

        $appointmentData['category_id'] = $request->getRelationshipId('category');
        $appointmentData['user_id'] = $request->getRelationshipId('author');

        $appointment = Appointment::create($appointmentData);

        return AppointmentResource::make($appointment);
    }

    /**
     * Display the specified resource.
     */
    public function show($appointment): JsonResource
    {
        $appointment = Appointment::where('id', $appointment)
            ->allowedIncludes(['category', 'author', 'comments'])
            ->sparseFieldset()
            ->firstOrFail();

        return new AppointmentResource($appointment);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(SaveAppointmentRequest $request, Appointment $appointment): AppointmentResource
    {
        /** Se manda como primer parametro el nombre del metodo de la politica
         * Policy (que se encuentra en la ruta App\Policies), y como segundo el modelo.
         */
        $this->authorize('update', $appointment);

        $request->validatedData();
        $appointmentData = $request->getAttributes();

        if ( $request->hasRelationship('author') ) {
            $appointmentData['user_id'] = $request->getRelationshipId('author');
        }

        if ( $request->hasRelationship('category') ) {
            $appointmentData['category_id'] = $request->getRelationshipId('category');
        }

        $appointment->update($appointmentData);

        return AppointmentResource::make($appointment);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Appointment $appointment, Request $request): Response
    {
        /** Se manda como primer parametro el nombre del metodo de la politica
         * Policy (que se encuentra en la ruta App\Policies), y como segundo el modelo.
         */
        $this->authorize('delete', $appointment);

        $appointment->delete();

        return response()->noContent();
    }
}
