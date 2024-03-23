<?php

namespace App\Http\Controllers\Api;

use App\Models\Comment;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Resources\CommentResource;
use App\Http\Resources\AppointmentResource;

class CommentAppointmentController extends Controller
{
    public function index(Comment $comment)
    {
        return AppointmentResource::identifier($comment->appointment);
    }

    public function show(Comment $comment)
    {
        return AppointmentResource::make($comment->appointment);
    }

    public function update(Comment $comment, Request $request)
    {
        $request->validate([
            'data.id' => ['exists:appointments,id']
        ]);

        $appointmentId = $request->input('data.id');

        $comment->update(['appointment_id' => $appointmentId]);

        return AppointmentResource::identifier($comment->appointment);
    }
}
