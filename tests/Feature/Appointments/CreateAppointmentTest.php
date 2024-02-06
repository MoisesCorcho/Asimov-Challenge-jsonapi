<?php

namespace Tests\Feature\Appointments;

use App\Models\Appointment;
use DateTime;
use Carbon\Carbon;
use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class CreateAppointmentTest extends TestCase
{

    use RefreshDatabase;

    /** @test */
    public function can_create_appointments()
    {

        $this->withoutExceptionHandling();

        $response = $this->postJson(route('api.v1.appointments.store'), [
            'data' => [
                'type' => 'appointments',
                'attributes' => [
                    'date' => '2025-11-17',
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

    /** @test */
    public function date_is_required()
    {
        $response = $this->postJson(route('api.v1.appointments.store'), [
            'data' => [
                'type' => 'appointments',
                'attributes' => [
                    'start_time' => '08:00:00',
                    'email' => 'falseemail@gmail.com'
                ],
            ]
        ]);

        $response->assertJsonStructure([
            'errors' => [
                ['title', 'detail', 'source' => ['pointer']]
            ]
        ])->assertJsonFragment([
            'source' => ['pointer' => '/data/attributes/date']
        ])->assertHeader(
            'content-type', 'application/vnd.api+json'
        )->assertStatus(422);

        // $response->assertJsonValidationErrors('data.attributes.date');
    }

    /** @test */
    public function start_time_is_required()
    {
        $response = $this->postJson(route('api.v1.appointments.store'), [
            'data' => [
                'type' => 'appointments',
                'attributes' => [
                    'date' => date('Y-m-d'),
                    'email' => 'falseemail@gmail.com'
                ],
            ]
        ]);

        $response->assertJsonStructure([
            'errors' => [
                ['title', 'detail', 'source' => ['pointer']]
            ]
        ])->assertJsonFragment([
            'source' => ['pointer' => '/data/attributes/start_time']
        ])->assertHeader(
            'content-type', 'application/vnd.api+json'
        )->assertStatus(422);

        // $response->assertJsonValidationErrors('data.attributes.start_time');
    }

    /** @test */
    public function email_is_required()
    {
        $response = $this->postJson(route('api.v1.appointments.store'), [
            'data' => [
                'type' => 'appointments',
                'attributes' => [
                    'date' => date('Y-m-d'),
                    'start_time' => '08:00:00'
                ],
            ]
        ]);

        $response->assertJsonStructure([
            'errors' => [
                ['title', 'detail', 'source' => ['pointer']]
            ]
        ])->assertJsonFragment([
            'source' => ['pointer' => '/data/attributes/email']
        ])->assertHeader(
            'content-type', 'application/vnd.api+json'
        )->assertStatus(422);

        // $response->assertJsonValidationErrors('data.attributes.email');
    }

    /** @test */
    public function date_format_must_be_Year_month_day()
    {
        $response = $this->postJson(route('api.v1.appointments.store'), [
            'data' => [
                'type' => 'appointments',
                'attributes' => [
                    'date' => '02-11-2025',
                    'start_time' => '08:00:00',
                    'email' => 'falseemail@gmail.com'
                ],
            ]
        ]);

        $response->assertJsonStructure([
            'errors' => [
                ['title', 'detail', 'source' => ['pointer']]
            ]
        ])->assertJsonFragment([
            'source' => ['pointer' => '/data/attributes/date']
        ]);
    }

    /** @test */
    public function the_appointment_date_must_be_greater_than_or_equal_to_the_current_date()
    {

        $response = $this->postJson(route('api.v1.appointments.store'), [
            'data' => [
                'type' => 'appointments',
                'attributes' => [
                    'date' => '2022-02-05',
                    'start_time' => '08:00:00',
                    'email' => 'falseemail@gmail.com'
                ],
            ]
        ]);

        $response->assertJsonStructure([
            'errors' => [
                ['title', 'detail', 'source' => ['pointer']]
            ]
        ])->assertJsonFragment([
            'source' => ['pointer' => '/data/attributes/date']
        ]);

    }
}
