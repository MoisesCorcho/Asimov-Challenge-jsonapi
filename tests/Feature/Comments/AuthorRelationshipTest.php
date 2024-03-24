<?php

namespace Tests\Feature\Comments;

use Tests\TestCase;
use App\Models\User;
use App\Models\Comment;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class AuthorRelationshipTest extends TestCase
{

    use RefreshDatabase;

    /** @test */
    public function can_fetch_the_associated_author_identifier(): void
    {
        $comment = Comment::factory()->create();

        $url = route('api.v1.comments.relationships.author', $comment);

        $response = $this->getJson($url);

        // Se espera un JSON con el identificador del Author.
        $response->assertExactJson([
            'data' => [
                'id' => (string) $comment->author->getRouteKey(),
                'type' => 'authors'
            ]
        ]);
    }

    /** @test */
    public function can_fetch_the_associated_author_resource(): void
    {
        // Se crea un comentario
        $comment = Comment::factory()->create();

        // Se crea la ruta
        $url = route('api.v1.comments.author', $comment);

        // Se hace la solicitud con la ruta antes creada.
        $response = $this->getJson($url);

        // Se espera que la respuesta contenga este JSON.
        $response->assertJson([
            'data' => [
                'id' => (string) $comment->author->getRouteKey(),
                'type' => 'authors',
                'attributes' => [
                    'name' => $comment->author->name,
                ]
            ]
        ]);
    }

    /** @test */
    public function can_update_the_associated_author(): void
    {
        // Se crean un Comment y un Appointment
        $comment = Comment::factory()->create();
        $author = User::factory()->create();

        // Se crea la ruta para actualizar el Appointment asociado al Comment
        $url = route('api.v1.comments.relationships.author', $comment);

        // Se realiza la solicitud de tipo PATCH enviando el documento JSON necesario.
        $response = $this->patchJson($url, [
            'data' => [
                'type' => 'authors',
                'id'   => (string) $author->getRouteKey()
            ]
        ]);

        // Se verifica que se reciba exactamente el documento JSON especificado.
        $response->assertExactJson([
            'data' => [
                'type' => 'authors',
                'id'   => (string) $author->getRouteKey()
            ]
        ]);

        // Se verifica que la tabla comments en la base de datos tenga los datos especificados.
        $this->assertDatabaseHas('comments', [
            'body' => $comment->body,
            'user_id' => (string) $author->id
        ]);

    }

    /** @test */
    public function author_must_exist_in_database(): void
    {
        $comment = Comment::factory()->create();

        $url = route('api.v1.comments.relationships.author', $comment);

        $this->patchJson($url, [
            'data' => [
                'type' => 'authors',
                'id'   => 'non-existing'
            ]
        ])->assertJsonApiValidationErrors('data.id');

        // Se verifica que la tabla comments en la base de datos tenga los datos especificados.
        $this->assertDatabaseHas('comments', [
            'body' => $comment->body,
            'user_id' => (string) $comment->user_id
        ]);
    }
}
