<?php

namespace Tests\Feature\Appointments;

use App\Models\Appointment;
use DateTime;
use Carbon\Carbon;
use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class CreateArticleTest extends TestCase
{

    use RefreshDatabase;

    /** @test */
    public function can_create_articles()
    {

        $this->withoutExceptionHandling();

        $response = $this->postJson(route('api.v1.appointments.store'), [
            'data' => [
                'type' => 'appointments',
                'attributes' => [
                    'date' => date('Y-m-d'),
                    'start_time' => '08:00:00',
                    'email' => 'falseemail@gmail.com'
                ],
            ]
        ]);

        $response->assertCreated();

        $appointment = Appointment::first();

        $response->assertHeader(
            'Location',
            route('api.v1.appointments.show', $appointment)
        );

        $response->assertExactJson([
            'data' => [
                'type' => 'appointments',
                'id' => (string) $appointment->getRouteKey(),
                'attributes' => [
                    'date' => $appointment->date,
                    'start_time' => $appointment->start_time,
                    'email' => $appointment->email
                ],
                'links' => [
                    'self' => route('api.v1.appointments.show', $appointment->getRouteKey())
                ]
            ]
        ]);
    }
}
