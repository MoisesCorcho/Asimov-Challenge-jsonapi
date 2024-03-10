<?php

namespace Tests\Feature\Appointments;

use Tests\TestCase;
use App\Models\Appointment;
use Illuminate\Foundation\Testing\RefreshDatabase;

class IncludeAuthorTest extends TestCase
{

    use RefreshDatabase;

    /** @test */
    public function can_include_related_author_of_an_appointment(): void
    {
        $appointment = Appointment::factory()->create();

        // appointments/1?include=author
        $url = route('api.v1.appointments.show', [
            'appointment' => $appointment,
            'include' => 'author'
        ]);

        $this->getJson($url)->assertJson([
            'included' => [
                [
                    'type' => 'authors',
                    'id' => $appointment->author->getRouteKey(),
                    'attributes' => [
                        'name' => $appointment->author->name
                    ]
                ]
            ]
        ]);
    }

    /** @test */
    public function can_include_related_authors_of_multiple_appointments(): void
    {
        $appointment = Appointment::factory()->create()->load('author');
        $appointment2 = Appointment::factory()->create()->load('author');

        $url = route('api.v1.appointments.index', [
            'include' => 'author'
        ]);

        $this->getJson($url)->assertJson([
            'included' => [
                [
                    'type' => 'authors',
                    'id' => $appointment->author->getRouteKey(),
                    'attributes' => [
                        'name' => $appointment->author->name
                    ]
                ],
                [
                    'type' => 'authors',
                    'id' => $appointment2->author->getRouteKey(),
                    'attributes' => [
                        'name' => $appointment2->author->name
                    ]
                ],
            ]
        ]);
    }
}
