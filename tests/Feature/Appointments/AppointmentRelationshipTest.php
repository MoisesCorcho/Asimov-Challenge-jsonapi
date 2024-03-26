<?php

namespace Tests\Feature\Appointments;

use App\Models\Appointment;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class AppointmentRelationshipTest extends TestCase
{

    use RefreshDatabase;

    /** @test */
    public function can_fetch_the_associated_comments_identifiers(): void
    {
        // Se uitliza el metodo magico 'hasComments' para crear comentarios asociados al 'Appointment' creado.
        // lo que se hace dicho metodo, es llamar a la relacion comments a traves del modelo Appointment.
        $appointment = Appointment::factory()->hasComments(2)->create();

        $url = route('api.v1.appointments.relationships.comments', $appointment);

        $response = $this->getJson($url);

        // Se verifica que se retornen los dos comentarios asociados al comentario dentro de la llave 'Data'.
        // Es decir, se confirma que se recibieron los dos identificadores de los comentarios asociaddos.
        $response->assertJsonCount(2, 'data');

        // Se accede a los comentarios asociados en el Appointment y por cada uno se verifica que se hayan recibido
        // las llaves 'id' y 'type'.
        $appointment->comments->map(fn ($comment) => $response->assertJsonFragment([
            'id' => (string) $comment->getRouteKey(),
            'type' => 'comments'
        ]));

    }
}
