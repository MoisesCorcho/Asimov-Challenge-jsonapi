<?php

namespace App\Http\Controllers\Api;

use App\Models\Appointment;
use App\Http\Controllers\Controller;
use App\Http\Resources\CategoryResource;

class AppointmentCategoryController extends Controller
{
    public function index(Appointment $appointment): array
    {
        return CategoryResource::identifier($appointment->category);
    }

    public function show(Appointment $appointment)
    {
        return CategoryResource::make($appointment->category);
    }
}
