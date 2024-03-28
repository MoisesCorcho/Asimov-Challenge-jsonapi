<?php

namespace Tests\Feature\Appointments;

use Tests\TestCase;
use App\Models\Comment;
use App\Models\Appointment;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

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
        $appointment = Appointment::factory()->hasComments(2)->create();

        $url = route('api.v1.appointments.comments', $appointment);

        $response = $this->getJson($url);

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

    /** @test */
    public function can_update_the_associated_comments(): void
    {
        // Internamente se crean dos comentarios cada uno asociado a un Appointment distinto
        // Es decir, que para este momento hay 2 comentarios y 2 Appointments en base de datos.
        $comments = Comment::factory(2)->create();

        // Se crea un Appointment al que se le van a asociar los comentarios a traves de la API.
        $appointment = Appointment::factory()->create();

        $url = route('api.v1.appointments.relationships.comments', $appointment);

        $response = $this->patchJson($url, [
            'data' => [
                [
                    'type' => 'comments',
                    'id' => (string) $comments[0]->getRouteKey()
                ],
                [
                    'type' => 'comments',
                    'id' => (string) $comments[1]->getRouteKey()
                ],
            ]
        ])->dump();

        $response->assertJsonCount(2, 'data');

        // Se espera que la respuesta contenga los fragmentos de JSON especificados.
        $comments->map(fn ($comment) => $response->assertJsonFragment([
            'type' => 'comments',
            'id' => (string) $comment->getRouteKey()
        ]));

        // Se espera que en la tabla 'comments' en la base de datos existan los campos
        // 'body' y 'appointment_id' con sus respectivos valores.
        $comments->map(fn ($comment) => $this->assertDatabaseHas('comments', [
            'body' => $comment->body,
            'appointment_id' => (string) $appointment->id
        ]));

    }
}
