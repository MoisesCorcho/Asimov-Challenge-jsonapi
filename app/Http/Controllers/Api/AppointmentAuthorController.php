<?php

namespace App\Http\Controllers\Api;

use App\Models\Appointment;
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
}
