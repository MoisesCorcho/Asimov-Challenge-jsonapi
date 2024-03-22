<?php

namespace Tests\Feature\Comments;

use Tests\TestCase;
use App\Models\Comment;
use Illuminate\Foundation\Testing\RefreshDatabase;

class ListCommentsTest extends TestCase
{

    use RefreshDatabase;

    /** @test */
    public function can_fetch_a_single_comment(): void
    {
        $comment = Comment::factory()->create(['body' => 'Comment Body']);

        $response = $this->getJson(route('api.v1.comments.show', $comment));

        $response->assertJsonApiResource($comment, [
            'body' => 'Comment Body'
        ]);
    }

    /** @test */
    public function can_fetch_all_comments()
    {
        $comments = Comment::factory()->count(3)->create();

        $response = $this->getJson(route('api.v1.comments.index'));

        $response->assertJsonApiResourceCollection($comments, [
            'body'
        ]);
    }

    /** @test */
    public function it_returns_a_json_api_error_object_when_an_article_is_not_found(): void
    {
        $response = $this->getJson(route('api.v1.comments.show', 'non-existing'));

        $response->assertJsonApiError(
            title: 'Not Found',
            detail: "No records found with that id.",
            status: '404'
        );
    }
}
