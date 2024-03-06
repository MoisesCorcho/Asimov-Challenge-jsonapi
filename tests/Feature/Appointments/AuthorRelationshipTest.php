<?php

namespace Tests\Feature\Appointments;

use Tests\TestCase;
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
}
