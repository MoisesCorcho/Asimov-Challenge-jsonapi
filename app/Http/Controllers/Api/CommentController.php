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
    public function __construct()
    {
        $this->middleware('auth:sanctum', [
            'only' => ['store']
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
    public function update(Request $request, Comment $comment)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Comment $comment)
    {
        //
    }
}
