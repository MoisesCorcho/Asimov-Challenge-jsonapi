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
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;

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
            ->allowedIncludes(['category', 'author'])
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

        $data = $request->validated()['data'];
        $appointmentData = $data['attributes'];

        if ( isset($data['relationships']) ) {
            $appointmentData['category_id'] = $data['relationships']['category']['data']['id'];
            $appointmentData['user_id'] = $data['relationships']['author']['data']['id'];
        }

        $appointment = Appointment::create($appointmentData);

        return AppointmentResource::make($appointment);
    }

    /**
     * Display the specified resource.
     */
    public function show($appointment): JsonResource
    {
        $appointment = Appointment::where('id', $appointment)
            ->allowedIncludes(['category', 'author'])
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

        $data = $request->validated()['data'];
        $appointmentData = $data['attributes'];

        if ( isset($data['relationships']) ) {

            if ( isset($data['relationships']['author']) ) {
                $appointmentData['user_id'] = $data['relationships']['author']['data']['id'];
            }

            if ( isset($data['relationships']['category']) ) {
                $appointmentData['category_id'] = $data['relationships']['category']['data']['id'];
            }
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
