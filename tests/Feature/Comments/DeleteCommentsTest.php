<?php

namespace Tests\Feature\Comments;

use Tests\TestCase;
use App\Models\User;
use App\Models\Comment;
use Laravel\Sanctum\Sanctum;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class DeleteCommentsTest extends TestCase
{

    use RefreshDatabase;

    /** @test */
    public function guests_cannot_delete_comments(): void
    {
        $comment = Comment::factory()->create();

        $this->deleteJson(route('api.v1.comments.destroy', $comment))
            ->assertJsonApiError(
                title: 'Unauthenticated',
                detail: 'This action requires authentication.',
                status: "401"
            );

        $this->assertDatabaseCount('comments', 1);
    }

    /** @test */
    public function can_delete_owned_comments(): void
    {
        $comment = Comment::factory()->create();

        /** Cualquier usuario que se cree tendrá los permisos necesarios
         * para la autenticacion de Sanctum.
         *
         * El token para este usuario se crea con la habilidad (Hability)
         *  de crear, es decir, con la convencion 'comment:delete'
         *  para que asi, no haya problemas de autorizacion con los Policies
         */
        Sanctum::actingAs($comment->author, ['comment:delete']);

        $this->deleteJson(route('api.v1.comments.destroy', $comment))
            ->assertNoContent();

        $this->assertDatabaseCount('comments', 0);
    }

    /** @test */
    public function can_delete_comments_owned_by_other_users(): void
    {
        /** Cualquier usuario que se cree tendrá los permisos necesarios
         * para la autenticacion de Sanctum
         */
        $comment = Comment::factory()->create();

        // Se inicia sesion con otro usuario que no sea el que creó el comentario.
        Sanctum::actingAs(User::factory()->create());

        $this->deleteJson(route('api.v1.comments.destroy', $comment))
            ->assertForbidden();

        $this->assertDatabaseCount('comments', 1);
    }
}
