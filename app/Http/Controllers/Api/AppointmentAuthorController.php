<?php

namespace App\Http\Controllers\Api;

use App\Models\User;
use App\Models\Appointment;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Resources\AuthorResource;

class AppointmentAuthorController extends Controller
{
    public function index(Appointment $appointment): array
    {
        return AuthorResource::identifier($appointment->author);
    }

    public function show(Appointment $appointment)
    {
        return AuthorResource::make($appointment->author);
    }

    public function update(Appointment $appointment, Request $request)
    {
        $authorId = $request->input('data.id');

        $appointment->update(['user_id' => $authorId]);

        return AuthorResource::identifier($appointment->author);
    }
}
