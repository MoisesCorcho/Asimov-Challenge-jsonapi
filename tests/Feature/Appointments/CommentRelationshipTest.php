<?php

namespace Tests\Feature\Appointments;

use App\Models\Appointment;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

/**
 * Este archivo contiene un conjunto de pruebas para verificar las relaciones
 * entre citas (Appointments) y comentarios (Comments) en una aplicación.
 * Estas pruebas se realizan para garantizar que las relaciones entre los
 * modelos estén configuradas correctamente y que la API pueda proporcionar
 * la información necesaria sobre estos recursos relacionados.
 */
class CommentRelationshipTest extends TestCase
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

    /** @test */
    public function it_returns_an_empty_array_when_there_are_no_associated_comments(): void
    {
        // Se crea un Appointment sin comentarios.
        $appointment = Appointment::factory()->create();

        $url = route('api.v1.appointments.relationships.comments', $appointment);

        $response = $this->getJson($url);

        // La llave data debe tener 0 elementos, es decir, un array vacio.
        $response->assertJsonCount(0, 'data');

        // Se verifica que la respuesta sea un arraglo vacio asociado a la clave 'data'.
        $response->assertExactJson([
            'data' => []
        ]);

    }

    /** @test */
    public function can_fetch_the_associated_comments_resource(): void
    {
        // Se crea un Appointment sin comentarios.
        $appointment = Appointment::factory()->hasComments(2)->create();

        $url = route('api.v1.appointments.comments', $appointment);

        $response = $this->getJson($url);

        // La llave data debe tener 0 elementos, es decir, un array vacio.
        $response->assertJson([
            'data' => [
                [
                    'id' => $appointment->comments[0]->getRouteKey(),
                    'type' => 'comments',
                    'attributes' => [
                        'body' => $appointment->comments[0]->body
                    ]
                ],
                [
                    'id' => $appointment->comments[1]->getRouteKey(),
                    'type' => 'comments',
                    'attributes' => [
                        'body' => $appointment->comments[1]->body
                    ]
                ],
            ]
        ]);

    }
}
