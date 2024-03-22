<?php

namespace App\Http\Controllers\Api;

use App\Models\Comment;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Requests\SaveCommentRequest;
use App\Http\Resources\CommentResource;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Http\Resources\Json\JsonResource;

class CommentController extends Controller
{
    /**
     * Todas las solicitudes a los metodos de este controlador
     * que se encuentren definidos dentro del constructor
     * seran interceptadas por el middleware 'auth' con el guard
     * 'sanctum'que garantiza que el usurio este autenticado
     * mediante sanctum
     */
    public function __construct()
    {
        $this->middleware('auth:sanctum', [
            'only' => ['store', 'update']
        ]);
    }

    /**
     * Display a listing of the resource.
     */
    public function index(): AnonymousResourceCollection
    {
        $comments = Comment::paginate();

        return CommentResource::collection($comments);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(SaveCommentRequest $request)
    {
        $comment = new Comment;
        $comment->body = $request->input('data.attributes.body');

        $comment->user_id = $request->getRelationshipId('author');
        $comment->appointment_id = $request->getRelationshipId('appointment');

        $comment->save();

        return CommentResource::make($comment);
    }

    /**
     * Display the specified resource.
     */
    public function show(Comment $comment): JsonResource
    {
        return CommentResource::make($comment);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(SaveCommentRequest $request, Comment $comment)
    {
        $this->authorize('update', $comment);

        $comment->body = $request->input('data.attributes.body');

        // El envio de las relaciones se hace opcional.
        if ( $request->hasRelationship('appointment') ) {
            $comment->appointment_id = $request->getRelationshipId('appointment');
        }

        if ( $request->hasRelationship('author') ) {
            $comment->user_id = $request->getRelationshipId('author');
        }

        $comment->save();

        return CommentResource::make($comment);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Comment $comment)
    {
        //
    }
}
