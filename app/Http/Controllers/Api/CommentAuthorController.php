<?php

namespace App\Http\Controllers\Api;

use App\Models\Comment;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Resources\AuthorResource;
use Illuminate\Http\Resources\Json\JsonResource;

class CommentAuthorController extends Controller
{
    /**
     * Obtener el identificador del Autor relacionado al Comentario.
     *
     * @param Comment $comment
     * @return array
     */
    public function index(Comment $comment): array
    {
        return AuthorResource::identifier($comment->author);
    }

    /**
     * Obtener el autor relacionado al Comentario.
     *
     * @param Comment $comment
     * @return JsonResource
     */
    public function show(Comment $comment): JsonResource
    {
        return AuthorResource::make($comment->author);
    }

    /**
     * Obtener el Autor asociado al Comentario.
     *
     * @param Comment $comment
     * @param Request $request
     * @return array
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
