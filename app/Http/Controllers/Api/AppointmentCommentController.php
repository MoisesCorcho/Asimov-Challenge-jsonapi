<?php

namespace App\Http\Controllers\Api;

use App\Models\Comment;
use App\Models\Appointment;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Resources\CommentResource;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

/**
 * Este controlador se encarga de gestionar las relaciones entre las citas (Appointments)
 * y los comentarios (Comments). Proporciona métodos para obtener, actualizar y mostrar
 * los comentarios asociados a una cita especifica.
 */
class AppointmentCommentController extends Controller
{
    /**
     * Obtiene los identificadores de los comentarios asociados a una cita.
     *
     * @param  Appointment  $appointment La cita de la cual se obtienen los comentarios.
     * @return array
     */
    public function index(Appointment $appointment): array
    {
        return CommentResource::identifiers($appointment->comments);
    }

    /**
     * Obtiene los recursos completos de los comentarios asociados a una cita.
     *
     * @param  Appointment  $appointment La cita de la cual se obtienen los comentarios.
     * @return \Illuminate\Http\Resources\Json\AnonymousResourceCollection
     */
    public function show(Appointment $appointment): AnonymousResourceCollection
    {
        return CommentResource::collection($appointment->comments);
    }

    /**
     * Actualiza la asociación de la cita para varios comentarios.
     *
     * @param  Appointment  $appointment La cita a la cual se asociarán los comentarios.
     * @param  Request  $request La solicitud HTTP recibida.
     * @return array
     */
    public function update(Appointment $appointment, Request $request): array
    {
        // Todos los id de los comentarios deben existir en su respectiva tabla
        // en base de datos.
        $request->validate([
            'data.*.id' => ['exists:comments,id']
        ]);

        /** Se obtienen todos los 'id' dentro de los elementos de la llave 'data'
         * Es como hacer un pluck en una coleccion de Laravel.
         */
        $commentIds = request()->input('data.*.id');

        // El metodo find tambien puede recibir un arreglo de 'ids'
        $comments = Comment::find($commentIds);

        $comments->each->update([
            'appointment_id' => $appointment->id
        ]);

        // Se envia la coleccion de modelos encontrada al metodo 'identifiers'
        // el cual arrojará la respuesta JSON con sus atributos 'type' y 'id'
        // por cada recurso.
        return CommentResource::identifiers($comments);
    }
}
