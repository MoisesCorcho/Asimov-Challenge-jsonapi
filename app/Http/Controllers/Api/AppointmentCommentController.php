<?php

namespace App\Http\Controllers\Api;

use App\Models\Comment;
use App\Models\Appointment;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Resources\CommentResource;

class AppointmentCommentController extends Controller
{
    public function index(Appointment $appointment)
    {
        return CommentResource::identifiers($appointment->comments);
    }

    public function show(Appointment $appointment)
    {
        return CommentResource::collection($appointment->comments);
    }

    public function update(Appointment $appointment, Request $request)
    {
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
