<?php

namespace Tests\Feature\Comments;

use Tests\TestCase;
use App\Models\Comment;
use App\Models\Appointment;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

/**
 * Este archivo encarga de probar la relación entre los comentarios
 * (Comment) y las citas (Appointment). Esta clase contiene varios
 * métodos de prueba que verifican diferentes aspectos de esta relación.
 */
class AppointmentRelationshipTest extends TestCase
{

    use RefreshDatabase;

    /** @test */
    public function can_fetch_the_associated_appointment_identifier(): void
    {
        $comment = Comment::factory()->create();

        $url = route('api.v1.comments.relationships.appointment', $comment);

        $response = $this->getJson($url);

        // Se espera un JSON con el identificado del Appointment.
        $response->assertExactJson([
            'data' => [
                'id' => (string) $comment->appointment->getRouteKey(),
                'type' => 'appointments'
            ]
        ]);
    }

    /** @test */
    public function can_fetch_the_associated_appointment_resource(): void
    {
        // Se crea un comentario
        $comment = Comment::factory()->create();

        // Se crea la ruta
        $url = route('api.v1.comments.appointment', $comment);

        // Se hace la solicitud con la ruta antes creada.
        $response = $this->getJson($url);

        // Se espera que la respuesta contenga este JSON.
        $response->assertJson([
            'data' => [
                'id' => (string) $comment->appointment->getRouteKey(),
                'type' => 'appointments',
                'attributes' => [
                    'date' => $comment->appointment->date,
                    'start-time' => $comment->appointment->start_time,
                    'email' => $comment->appointment->email
                ]
            ]
        ]);
    }

    /** @test */
    public function can_update_the_associated_appointment(): void
    {
        // Se crean un Comment y un Appointment
        $comment = Comment::factory()->create();
        $appointment = Appointment::factory()->create();

        // Se crea la ruta para actualizar el Appointment asociado al Comment
        $url = route('api.v1.comments.relationships.appointment', $comment);

        // Se realiza la solicitud de tipo patch enviando el documento JSON necesario.
        $response = $this->patchJson($url, [
            'data' => [
                'type' => 'appointments',
                'id'   => (string) $appointment->getRouteKey()
            ]
        ]);

        // Se verifica que se reciba exactamente el documento JSON especificado.
        $response->assertExactJson([
            'data' => [
                'type' => 'appointments',
                'id'   => (string) $appointment->getRouteKey()
            ]
        ]);

        // Se verifica que la tabla comments en la base de datos tenga los datos especificados.
        $this->assertDatabaseHas('comments', [
            'body' => $comment->body,
            'appointment_id' => (string) $appointment->id
        ]);

    }

    /** @test */
    public function appointment_must_exist_in_database(): void
    {
        $comment = Comment::factory()->create();

        $url = route('api.v1.comments.relationships.appointment', $comment);

        $this->patchJson($url, [
            'data' => [
                'type' => 'appointments',
                'id'   => 'non-existing'
            ]
        ])->assertJsonApiValidationErrors('data.id');

        // Se verifica que la tabla comments en la base de datos tenga los datos especificados.
        $this->assertDatabaseHas('comments', [
            'body' => $comment->body,
            'appointment_id' => (string) $comment->appointment_id
        ]);
    }
}
