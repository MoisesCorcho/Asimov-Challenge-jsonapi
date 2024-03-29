<?php

namespace App\Http\Controllers\Api;

use App\Models\Comment;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Resources\AuthorResource;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * Este controlador se encarga de gestionar las relaciones entre los comentarios (Comments)
 * y los autores (Authors). Proporciona métodos para obtener, actualizar y mostrar
 * el autor asociado a un comentario específico.
 */
class CommentAuthorController extends Controller
{
    /**
     * Obtener el identificador del Autor relacionado al Comentario.
     *
     * @param Comment $comment El comentario para el cual se desea obtener el autor relacionado.
     * @return array El identificador del autor en forma de array.
     */
    public function index(Comment $comment): array
    {
        return AuthorResource::identifier($comment->author);
    }

    /**
     * Obtener el recurso completo del Autor relacionado al Comentario.
     *
     * @param Comment $comment El comentario para el cual se desea obtener el autor relacionado.
     * @return array El identificador del autor en forma de array.
     */
    public function show(Comment $comment): JsonResource
    {
        return AuthorResource::make($comment->author);
    }

    /**
     * Actualizar el Autor asociado al Comentario.
     *
     * @param Comment $comment El comentario que se actualizará.
     * @param Request $request La solicitud HTTP que contiene los datos del autor.
     * @return array El identificador del nuevo autor asociado al comentario.
     */
    public function update(Comment $comment, Request $request): array
    {
        $request->validate([
            'data.id' => 'exists:users,id'
        ]);

        $authorId = $request->input('data.id');

        $comment->update(['user_id' => $authorId]);

        return AuthorResource::identifier($comment->author);
    }
}
