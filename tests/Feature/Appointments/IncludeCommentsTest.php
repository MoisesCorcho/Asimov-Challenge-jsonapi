<?php

namespace Tests\Feature\Appointments;

use Tests\TestCase;
use App\Models\Appointment;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class IncludeCommentsTest extends TestCase
{

    use RefreshDatabase;

    /** @test */
    public function can_include_related_comments_of_an_appointment(): void
    {
        // Se crea un Appointment con dos comentarios asociados.
        $appointment = Appointment::factory()->hasComments(2)->create();

        // appointments/id?include=comments
        $url = route('api.v1.appointments.show', [
            'include' => 'comments',
            // El parametro se manda con este nombre, porque con ese nombre se está esperando
            // en el archivo de rutas api.
            // Ej. api/v1/appointments/{appointment} ..... api.v1.appointments.show › Api\AppointmentController@show
            'appointment' => $appointment,
        ]);

        // Se hace la peticion de tipo GET a la ruta.
        $response = $this->getJson($url);

        // Se espera que dentro de la llave 'included' en la respuesta vengan dos objetos.
        $response->assertJsonCount(2, 'included');

        // Por cada comentario se espera esta estructura JSON de la respuesta.
        $appointment->comments->map(fn ($comment) => $response->assertJsonFragment([
            'type' => 'comments',
            'id' => (string) $comment->getRouteKey(),
            'attributes' => [
                'body' => $comment->body
            ]
        ]));
    }
}
