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

class UpdateCommentsTest extends TestCase
{

    use RefreshDatabase;

    /** @test */
    public function guests_cannot_update_comments()
    {
        $comment = Comment::factory()->create();

        $this->patchJson(route('api.v1.comments.update', $comment))
            ->assertJsonApiError(
                title: 'Unauthenticated',
                detail: 'This action requires authentication.',
                status: '401'
            );
    }

    /** @test */
    public function can_update_owned_comments()
    {
        /** Cualquier usuario que se cree tendrá los permisos necesarios
         * para la autenticacion de Sanctum
         */

        $comment = Comment::factory()->create();

        /** El token para este usuario se crea con la habilidad (Hability)
         *  de crear, es decir, con la convencion 'comment:update'
         *  para que asi, no haya problemas de autorizacion con los Policies
         */
        Sanctum::actingAs($comment->author, ['comment:update']);

        $response = $this->patchJson(route('api.v1.comments.update', $comment),
            Document::type('comments')
                ->id(1)
                ->attributes([
                    'body' => 'Updated content',
                ])
                ->toArray()
        );

        $response->assertOk();

        $response->assertJsonApiResource($comment, [
            'body' => 'Updated content',
        ]);
    }

    /** @test */
    public function body_is_required()
    {
        /** Cualquier usuario que se cree tendrá los permisos necesarios
         * para la autenticacion de Sanctum
         */
        $comment = Comment::factory()->create();

        Sanctum::actingAs($comment->author);

        $response = $this->patchJson(route('api.v1.comments.update', $comment),
            Document::type('comments')
                ->id(1)
                ->attributes([
                    'body' => null
                ])->toArray()
        );

        $response->assertJsonApiValidationErrors('body');
    }

    /** @test */
    public function can_update_owned_comments_with_relationships()
    {
        $appointment = Appointment::factory()->create();
        $author = User::factory()->create();

        /** Cualquier usuario que se cree tendrá los permisos necesarios
         * para la autenticacion de Sanctum
         */
        $comment = Comment::factory()->create();

        /** El token para este usuario se crea con la habilidad (Hability)
         *  de crear, es decir, con la convencion 'comment:update'
         *  para que asi, no haya problemas de autorizacion con los Policies
         */
        Sanctum::actingAs($comment->author, ['comment:update']);

        $response = $this->patchJson(route('api.v1.comments.update', $comment),
            Document::type('comments')
                ->id(1)
                ->attributes([
                    'body' => 'Updated body',
                ])->relationshipsData([
                    'appointment' => $appointment,
                    // 'author' => $author
                ])->toArray()
        );

        $response->assertOk();

        // Se valida que el appointment sea igual al que tiene relacionado el comentario recien recargado de la base de datos.
        // $this->assertTrue($appointment->is($comment->fresh()->appointment));

        $response->assertJsonApiResource($comment, [
            'body' => 'Updated body',
        ]);

        $this->assertDatabaseHas('comments', [
            'body' => 'Updated body',
            'appointment_id' => $appointment->id,
            'user_id' => $comment->author->id
        ]);

    }

    /** @test */
    public function cannot_update_comments_owned_by_other_users()
    {
        $appointment = Appointment::factory()->create();
        $author = User::factory()->create();

        /** Cualquier usuario que se cree tendrá los permisos necesarios
         * para la autenticacion de Sanctum
         */
        Sanctum::actingAs(User::factory()->create(), ['comment:update']);

        $comment = Comment::factory()->create();

        $this->patchJson(route('api.v1.comments.update', $comment),
            Document::type('comments')
                ->id(1)
                ->attributes([
                    'body' => 'Updated body'
                ])->relationshipsData([
                    'appointment' => $appointment,
                    'author' => $author
                ])->toArray()
        )->assertForbidden();
    }
}
