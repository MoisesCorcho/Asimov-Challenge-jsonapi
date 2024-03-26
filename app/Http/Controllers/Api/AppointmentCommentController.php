<?php

namespace App\Http\Controllers\Api;

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
}
