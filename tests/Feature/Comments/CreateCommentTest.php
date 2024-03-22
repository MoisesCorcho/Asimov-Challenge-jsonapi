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
            Document::type('appointments')
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
}
