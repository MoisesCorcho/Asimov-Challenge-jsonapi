<?php

namespace App\Http\Controllers\Api;

use App\Models\Appointment;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Resources\CategoryResource;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * Este controlador se encarga de gestionar las relaciones entre las citas (Appointments)
 * y las categorías (Categories). Proporciona métodos para obtener, actualizar y mostrar
 * la categoría asociada a una cita específica.
 */
class AppointmentCategoryController extends Controller
{
    /**
     * Retorna el identificador de la categoría asociada a la cita.
     *
     * @param  Appointment  $appointment La cita para la cual se desea obtener la categoría.
     * @return array El identificador de la categoría en forma de array.
     */
    public function index(Appointment $appointment): array
    {
        return CategoryResource::identifier($appointment->category);
    }

    /**
     * Obtiene el recurso completo de la categoría asociada a la cita.
     *
     * @param  Appointment  $appointment La cita para la cual se desea mostrar la categoría.
     * @return \Illuminate\Http\Resources\Json\JsonResource El recurso JSON de la categoría.
     */
    public function show(Appointment $appointment): JsonResource
    {
        return CategoryResource::make($appointment->category);
    }

    /**
     * Actualiza la categoría asociada a la cita.
     *
     * @param  Appointment  $appointment La cita que se actualizará.
     * @param  Request  $request La solicitud HTTP que contiene los datos de la categoría.
     * @return array El identificador de la nueva categoría asociada a la cita.
     */
    public function update(Appointment $appointment, Request $request): array
    {
        $request->validate([
            'data.id' => ['exists:categories,id']
        ]);

        $categoryId = $request->input('data.id');

        $appointment->update(['category_id' => $categoryId]);

        return CategoryResource::identifier($appointment->category);
    }
}
