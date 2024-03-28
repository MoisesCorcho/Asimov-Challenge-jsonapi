<?php

namespace Tests\Feature\Appointments;

use Tests\TestCase;
use App\Models\Appointment;
use Illuminate\Foundation\Testing\RefreshDatabase;

/**
 * Este archivo se utiliza para verificar el funcionamiento de la inclusión
 * de autores relacionadas en las respuestas JSON de las citas (Appointments).
 * Este tipo de prueba es esencial cuando se trabaja con una API que sigue la
 * especificación JSON:API, ya que esta especificación permite incluir recursos
 * relacionados en una única solicitud, lo que reduce la necesidad de realizar
 * múltiples solicitudes para obtener datos relacionados.
 *
 * La inclusión de recursos relacionados (autores en este caso) mediante el
 * parámetro include permite que la respuesta de un endpoint incluya no solo
 * el recurso solicitado directamente, sino también recursos relacionados que
 * podrían ser útiles para el cliente que consume la API. Esto reduce la
 * necesidad de realizar múltiples solicitudes al servidor para obtener
 * información adicional relacionada.
 *
 * Por ejemplo, supongamos que una aplicación de calendario tiene una API que
 * proporciona detalles sobre citas. Cada cita está asociada con un autor.
 * En lugar de tener que realizar una solicitud separada para obtener información
 * sobre el autor de cada cita, el cliente puede usar el parámetro include=author
 * para solicitar que la respuesta incluya también la información del autor
 * junto con la información de la cita.
 *
 * Ej. GET /api/v1/appointments/1?include=author
 *
 * Esto devolverá la información de la cita con los datos del autor incluidos en
 * la respuesta.
 */
class IncludeAuthorTest extends TestCase
{

    use RefreshDatabase;

    /** @test */
    public function can_include_related_author_of_an_appointment(): void
    {
        $appointment = Appointment::factory()->create();

        // appointments/1?include=author
        $url = route('api.v1.appointments.show', [
            'appointment' => $appointment,
            'include' => 'author'
        ]);

        $this->getJson($url)->assertJson([
            'included' => [
                [
                    'type' => 'authors',
                    'id' => $appointment->author->getRouteKey(),
                    'attributes' => [
                        'name' => $appointment->author->name
                    ]
                ]
            ]
        ]);
    }

    /** @test */
    public function can_include_related_authors_of_multiple_appointments(): void
    {
        $appointment = Appointment::factory()->create()->load('author');
        $appointment2 = Appointment::factory()->create()->load('author');

        $url = route('api.v1.appointments.index', [
            'include' => 'author'
        ]);

        $this->getJson($url)->assertJson([
            'included' => [
                [
                    'type' => 'authors',
                    'id' => $appointment->author->getRouteKey(),
                    'attributes' => [
                        'name' => $appointment->author->name
                    ]
                ],
                [
                    'type' => 'authors',
                    'id' => $appointment2->author->getRouteKey(),
                    'attributes' => [
                        'name' => $appointment2->author->name
                    ]
                ],
            ]
        ]);
    }
}
