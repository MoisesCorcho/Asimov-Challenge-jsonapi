<?php

namespace Tests\Feature\Appointments;

use Tests\TestCase;
use App\Models\Appointment;
use Illuminate\Foundation\Testing\RefreshDatabase;

/**
 * Este archivo se utiliza para verificar el funcionamiento de la inclusión
 * de categorías relacionadas en las respuestas JSON de las citas (Appointments).
 * Este tipo de prueba es esencial cuando se trabaja con una API que sigue la
 * especificación JSON:API, ya que esta especificación permite incluir recursos
 * relacionados en una única solicitud, lo que reduce la necesidad de realizar
 * múltiples solicitudes para obtener datos relacionados.
 *
 * La inclusión de recursos relacionados (categoria en este caso) mediante el
 * parámetro include permite que la respuesta de un endpoint incluya no solo
 * el recurso solicitado directamente, sino también recursos relacionados que
 * podrían ser útiles para el cliente que consume la API. Esto reduce la
 * necesidad de realizar múltiples solicitudes al servidor para obtener
 * información adicional relacionada.
 *
 * Ej. GET /api/v1/appointments/1?include=category
 */
class IncludeCategoryTest extends TestCase
{

    use RefreshDatabase;

    /** @test */
    public function can_include_related_category_of_an_appointment(): void
    {
        $appointment = Appointment::factory()->create();

        $url = route('api.v1.appointments.show', [
            'appointment' => $appointment,
            'include' => 'category'
        ]);

        $this->getJson($url)->assertJson([
            'included' => [
                [
                    'type' => 'categories',
                    'id' => $appointment->category->getRouteKey(),
                    'attributes' => [
                        'name' => $appointment->category->name
                    ]
                ]
            ]
        ]);
    }

    /** @test */
    public function can_include_related_categories_of_multiple_appointments(): void
    {
        $appointment = Appointment::factory()->create()->load('category');
        $appointment2 = Appointment::factory()->create()->load('category');

        $url = route('api.v1.appointments.index', [
            'include' => 'category'
        ]);

        $this->getJson($url)->assertJson([
            'included' => [
                [
                    'type' => 'categories',
                    'id' => $appointment->category->getRouteKey(),
                    'attributes' => [
                        'name' => $appointment->category->name
                    ]
                ],
                [
                    'type' => 'categories',
                    'id' => $appointment2->category->getRouteKey(),
                    'attributes' => [
                        'name' => $appointment2->category->name
                    ]
                ],
            ]
        ]);
    }

    /** @test */
    public function cannot_include_unknown_relationships(): void
    {
        $appointment = Appointment::factory()->create();

        // appointments/1?include=unknown
        $url = route('api.v1.appointments.show', [
            'appointment' => $appointment,
            'include' => 'unknown,unknown2'
        ]);

        $this->getJson($url)->assertJsonApiError(
            title: 'Bad Request',
            detail: "The include relationship 'unknown' is not allowed in the 'appointments' resource.",
            status: '400'
        );

        // appointments?include=unknown
        $url = route('api.v1.appointments.index', [
            'include' => 'unknown,unknown2'
        ]);

        $this->getJson($url)->assertJsonApiError(
            title: 'Bad Request',
            detail: "The include relationship 'unknown' is not allowed in the 'appointments' resource.",
            status: '400'
        );
    }
}
