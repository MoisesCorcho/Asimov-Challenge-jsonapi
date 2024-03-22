<?php

namespace Tests\Feature\Comments;

use Tests\TestCase;
use App\Models\User;
use App\Models\Comment;
use App\JsonApi\Document;
use App\Models\Appointment;
use Laravel\Sanctum\Sanctum;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class CreateCommentTest extends TestCase
{

    use RefreshDatabase;

    /** @test */
    public function guests_cannot_create_comments(): void
    {
        $this->postJson(route('api.v1.comments.store'))
            ->assertJsonApiError(
                title: 'Unauthenticated',
                detail: 'This action requires authentication.',
                status: '401'
            );

        $this->assertDatabaseCount('comments', 0);
    }

    /** @test */
    public function can_create_comments()
    {
        $user = User::factory()->create();
        $appointment = Appointment::factory()->create();

        Sanctum::actingAs($user);

        $response = $this->postJson(route('api.v1.comments.store'),
            Document::type('comments')
                ->attributes([
                    'body' => $commentBody = 'Comment body'
                ])->relationshipsData([
                    'appointment' => $appointment,
                    'author'   => $user
                ])->toArray()
        )->assertCreated();

        $comment = Comment::first();

        $response->assertJsonApiResource($comment, [
            'body' => $commentBody
        ]);

        $this->assertDatabaseCount('comments', 1);

        $this->assertDatabaseHas('comments', [
            'body' => $commentBody,
            'appointment_id' => $appointment->id,
            'user_id' => $user->id
        ]);
    }

    /** @test */
    public function body_is_required()
    {
        /** Cualquier usuario que se cree tendrá los permisos necesarios
         * para la autenticacion de Sanctum
         */
        Sanctum::actingAs(User::factory()->create());

        $response = $this->postJson(route('api.v1.comments.store'),
            Document::type('comments')
                ->attributes([
                    'body' => null
                ])->toArray()
        );

        $response->assertJsonApiValidationErrors('body');
    }

    /** @test */
    public function appointment_relationship_is_required()
    {
        /** Cualquier usuario que se cree tendrá los permisos necesarios
         * para la autenticacion de Sanctum
         */
        Sanctum::actingAs(User::factory()->create());

        $response = $this->postJson(route('api.v1.comments.store'),
            Document::type('comments')
                ->attributes([
                    'body' => 'Comment Body'
                ])->toArray()
        );

        $response->assertJsonApiValidationErrors('relationships.appointment');
    }

    /** @test */
    public function appointment_must_exist_in_database()
    {
        /** Cualquier usuario que se cree tendrá los permisos necesarios
         * para la autenticacion de Sanctum
         */
        Sanctum::actingAs(User::factory()->create());

        $response = $this->postJson(route('api.v1.comments.store'),
            Document::type('comments')
                ->attributes([
                    'body' => 'Comment Body'
                ])->relationshipsData([
                    // Se utiliza make para que se cree el recurso en memoria, pero no en base de datos.
                    'appointment' => Appointment::factory()->make(['id' => '1']),
                ])->toArray()
        );

        $response->assertJsonApiValidationErrors('relationships.appointment');
    }

    /** @test */
    public function author_relationship_is_required()
    {
        /** Cualquier usuario que se cree tendrá los permisos necesarios
         * para la autenticacion de Sanctum
        */
        Sanctum::actingAs(User::factory()->create());

        $response = $this->postJson(route('api.v1.comments.store'),
            Document::type('comments')
                ->attributes([
                    'body' => 'Comment Body'
                ])->relationshipsData([
                    // Se utiliza make para que se cree el recurso en memoria, pero no en base de datos.
                    'appointment' => Appointment::factory()->create(),
                ])->toArray()
        );

        $response->assertJsonApiValidationErrors('relationships.author');
    }

    /** @test */
    public function author_must_exist_in_database()
    {
        /** Cualquier usuario que se cree tendrá los permisos necesarios
         * para la autenticacion de Sanctum
         */
        Sanctum::actingAs(User::factory()->create());

        $response = $this->postJson(route('api.v1.comments.store'),
            Document::type('comments')
                ->attributes([
                    'body' => 'Comment Body'
                ])->relationshipsData([
                    // Se utiliza make para que se cree el recurso en memoria, pero no en base de datos.
                    'appointment' => Appointment::factory()->create(),
                    'author' => User::factory()->make(['id' => 'uuid'])
                ])->toArray()
        );

        $response->assertJsonApiValidationErrors('relationships.author');
    }
}
