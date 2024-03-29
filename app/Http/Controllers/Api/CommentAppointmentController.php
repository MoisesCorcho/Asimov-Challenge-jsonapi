<?php

namespace App\Http\Controllers\Api;

use App\Models\Comment;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Resources\AppointmentResource;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * Este controlador se encarga de gestionar las relaciones entre los comentarios (Comments)
 * y las citas (Appointments). Proporciona métodos para obtener, actualizar y mostrar
 * la cita asociada a un comentario específico.
 */
class CommentAppointmentController extends Controller
{
    /**
     * Retorna el identificador de la cita asociada al comentario.
     *
     * @param  Comment  $comment El comentario para el cual se desea obtener la cita asociada.
     * @return array El identificador de la cita en forma de array.
     */
    public function index(Comment $comment): array
    {
        return AppointmentResource::identifier($comment->appointment);
    }

    /**
     * Muestra los detalles completos de la cita asociada al comentario.
     *
     * @param  Comment  $comment El comentario para el cual se desea mostrar la cita asociada.
     * @return \Illuminate\Http\Resources\Json\JsonResource El recurso JSON de la cita.
     */
    public function show(Comment $comment): JsonResource
    {
        return AppointmentResource::make($comment->appointment);
    }

    /**
     * Actualiza la cita asociada al comentario.
     *
     * @param  Comment  $comment El comentario que se actualizará.
     * @param  Request  $request La solicitud HTTP que contiene los datos de la cita.
     * @return array El identificador de la nueva cita asociada al comentario.
     */
    public function update(Comment $comment, Request $request): array
    {
        $request->validate([
            'data.id' => ['exists:appointments,id']
        ]);

        $appointmentId = $request->input('data.id');

        $comment->update(['appointment_id' => $appointmentId]);

        return AppointmentResource::identifier($comment->appointment);
    }
}
