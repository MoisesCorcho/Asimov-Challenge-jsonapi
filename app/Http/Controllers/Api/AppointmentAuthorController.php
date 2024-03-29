<?php

namespace App\Http\Controllers\Api;

use App\Models\Appointment;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Resources\AuthorResource;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * Este controlador se encarga de gestionar las relaciones entre las citas (Appointments)
 * y los autores (Authors). Proporciona métodos para obtener, actualizar y mostrar
 * el autor asociado a una cita específica.
 */
class AppointmentAuthorController extends Controller
{
    /**
     * Retorna el identificador del autor asociado a la cita.
     *
     * @param  Appointment  $appointment La cita para la cual se desea obtener el autor.
     * @return array El identificador del autor en forma de array.
     */
    public function index(Appointment $appointment): array
    {
        return AuthorResource::identifier($appointment->author);
    }

    /**
     * Muestra los detalles completos del autor asociado a la cita.
     *
     * @param  Appointment  $appointment La cita para la cual se desea mostrar el autor.
     * @return \Illuminate\Http\Resources\Json\JsonResource El recurso JSON del autor.
     */
    public function show(Appointment $appointment): JsonResource
    {
        return AuthorResource::make($appointment->author);
    }

    /**
     * Actualiza el autor asociado a la cita.
     *
     * @param  Appointment  $appointment La cita que se actualizará.
     * @param  Request  $request La solicitud HTTP que contiene los datos del autor.
     * @return array El identificador del nuevo autor asociado a la cita.
     */
    public function update(Appointment $appointment, Request $request): array
    {
        $request->validate([
            'data.id' => ['exists:users,id']
        ]);

        $authorId = $request->input('data.id');

        $appointment->update(['user_id' => $authorId]);

        return AuthorResource::identifier($appointment->author);
    }
}
