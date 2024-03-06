<?php

namespace Tests\Feature\Appointments;

use Tests\TestCase;
use App\Models\User;
use App\Models\Appointment;
use Illuminate\Foundation\Testing\RefreshDatabase;

class AuthorRelationshipTest extends TestCase
{

    use RefreshDatabase;

    /** @test */
    public function can_fetch_the_associated_author_identifier(): void
    {
        $appointment = Appointment::factory()->create();

        $url = route('api.v1.appointments.relationships.author', $appointment);

        $response = $this->getJson($url);

        $response->assertExactJson([
            'data' => [
                'id' => (string) $appointment->author->getRouteKey(),
                'type' => 'authors'
            ]
        ]);
    }

    /** @test */
    public function can_fetch_the_associated_author_resource(): void
    {
        $appointment = Appointment::factory()->create();

        $url = route('api.v1.appointments.author', $appointment);

        $response = $this->getJson($url);

        $response->assertJson([
            'data' => [
                'id' => (string) $appointment->author->getRouteKey(),
                'type' => 'authors',
                'attributes' => [
                    'name' => $appointment->author->name
                ]
            ]
        ]);
    }

    /** @test */
    public function can_update_the_associated_author(): void
    {
        $author = User::factory()->create();
        $appointment = Appointment::factory()->create();

        $url = route('api.v1.appointments.relationships.author', $appointment);

        $response = $this->patchJson($url, [
            'data' => [
                'type' => 'authors',
                'id'   => (string) $author->getRouteKey()
            ]
        ]);

        $response->assertExactJson([
            'data' => [
                'type' => 'authors',
                'id'   => (string) $author->getRouteKey()
            ]
        ]);

        $this->assertDatabaseHas('appointments', [
            'date' => $appointment->date,
            'user_id' => (string) $author->id
        ]);

    }

    /** @test */
    public function author_must_exist_in_database(): void
    {
        $appointment = Appointment::factory()->create();

        $url = route('api.v1.appointments.relationships.author', $appointment);

        $this->patchJson($url, [
            'data' => [
                'type' => 'authors',
                'id'   => 'non-existing'
            ]
        ])->assertJsonApiValidationErrors('data.id');

        $this->assertDatabaseHas('appointments', [
            'date' => $appointment->date,
            'user_id' => $appointment->user_id
        ]);
    }
}
