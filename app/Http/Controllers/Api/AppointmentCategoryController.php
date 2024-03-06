<?php

namespace App\Http\Controllers\Api;

use App\Models\Category;
use App\Models\Appointment;
use Illuminate\Http\Request;
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

    public function update(Appointment $appointment, Request $request)
    {
        $request->validate([
            'data.id' => ['exists:categories,id']
        ]);

        $categoryId = $request->input('data.id');

        $appointment->update(['category_id' => $categoryId]);

        return CategoryResource::identifier($appointment->category);
    }
}
