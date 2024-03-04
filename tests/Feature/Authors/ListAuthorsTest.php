<?php

namespace Tests\Feature\Authors;

use Tests\TestCase;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

class ListAuthorsTest extends TestCase
{

    use RefreshDatabase;

    /** @test */
    public function can_fetch_a_single_author(): void
    {
        $author = User::factory()->create();

        $response = $this->getJson(route('api.v1.authors.show', $author));

        $response->assertJsonApiResource($author, [
            'name' => $author->name
        ]);
    }

    /** @test */
    public function can_fetch_all_authors()
    {
        $this->withoutExceptionHandling();

        $authors = User::factory()->count(3)->create();

        $response = $this->getJson(route('api.v1.authors.index'));

        $response->assertJsonApiResourceCollection($authors, [
            'name'
        ]);
    }
}
