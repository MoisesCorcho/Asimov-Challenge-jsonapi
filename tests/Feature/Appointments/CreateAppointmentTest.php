<?php

namespace Tests\Feature\Appointments;

use Tests\TestCase;
use App\JsonApi\Document;
use App\Models\Appointment;
use Illuminate\Foundation\Testing\RefreshDatabase;

class CreateAppointmentTest extends TestCase
{

    use RefreshDatabase;

    /** @test */
    public function can_create_appointments()
    {
        $this->withoutExceptionHandling();

        $response = $this->postJson(route('api.v1.appointments.store'),
            Document::type('appointments')
                ->attributes([
                    'date' => '2025-11-17',
                    'start_time' => '11:00',
                    'email' => 'falseemail@gmail.com'
                ])
                ->toArray()
        );

        $response->assertCreated();

        $appointment = Appointment::first();

        $response->assertJsonApiResource($appointment, [
            'date' => $appointment->date,
            'start_time' => substr($appointment->start_time, 0, 5),
            'email' => $appointment->email
        ]);

    }

    /** @test */
    public function date_is_required()
    {
        $response = $this->postJson(route('api.v1.appointments.store'), [
            'data' => [
                'type' => 'appointments',
                'attributes' => [
                    'start_time' => '11:00',
                    'email' => 'falseemail@gmail.com'
                ],
            ]
        ]);

        $response->assertJsonApiValidationErrors('date');
    }

    /** @test */
    public function date_format_must_be_Year_month_day()
    {
        $response = $this->postJson(route('api.v1.appointments.store'), [
            'data' => [
                'type' => 'appointments',
                'attributes' => [
                    'date' => '02-11-2025',
                    'start_time' => '11:00',
                    'email' => 'falseemail@gmail.com'
                ],
            ]
        ]);

        $response->assertJsonApiValidationErrors('date');
    }

    /** @test */
    public function the_appointment_date_must_be_greater_than_or_equal_to_the_current_date()
    {

        $response = $this->postJson(route('api.v1.appointments.store'), [
            'data' => [
                'type' => 'appointments',
                'attributes' => [
                    'date' => '2022-02-05',
                    'start_time' => '11:00',
                    'email' => 'falseemail@gmail.com'
                ],
            ]
        ]);

        $response->assertJsonApiValidationErrors('date');
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

        $response->assertJsonApiValidationErrors('start_time');
    }

    /** @test */
    public function email_is_required()
    {
        $response = $this->postJson(route('api.v1.appointments.store'), [
            'data' => [
                'type' => 'appointments',
                'attributes' => [
                    'date' => date('Y-m-d'),
                    'start_time' => '11:00'
                ],
            ]
        ]);

        $response->assertJsonApiValidationErrors('email');
    }

    /** @test */
    public function date_can_not_be_weekeend()
    {
        $response = $this->postJson(route('api.v1.appointments.store'), [
            'data' => [
                'type' => 'appointments',
                'attributes' => [
                    'date' => '2024-02-10',
                    'start_time' => '11:00',
                    'email' => 'falseemail@gmail.com'
                ],
            ]
        ]);

        $response->assertJsonApiValidationErrors('date');
    }

    /** @test */
    public function time_format_must_be_Hour_Minutes()
    {
        $response = $this->postJson(route('api.v1.appointments.store'), [
            'data' => [
                'type' => 'appointments',
                'attributes' => [
                    'date' => '2026-01-07',
                    'start_time' => '11:00:00',
                    'email' => 'falseemail@gmail.com'
                ],
            ]
        ]);

        $response->assertJsonApiValidationErrors('start_time');
    }

    /** @test */
    public function time_can_not_be_before_the_current_time()
    {
        $response = $this->postJson(route('api.v1.appointments.store'), [
            'data' => [
                'type' => 'appointments',
                'attributes' => [
                    'date' => now()->toDateString(),
                    'start_time' => now()->subHour()->format('H:i'),
                    'email' => 'falseemail@gmail.com'
                ],
            ]
        ]);

        $response->assertJsonApiValidationErrors('start_time');
    }

    /** @test */
    public function time_must_be_in_office_time()
    {
        $response = $this->postJson(route('api.v1.appointments.store'), [
            'data' => [
                'type' => 'appointments',
                'attributes' => [
                    'date' => '2026-01-01',
                    'start_time' => '07:00',
                    'email' => 'falseemail@gmail.com'
                ],
            ]
        ]);

        $response = $this->postJson(route('api.v1.appointments.store'), [
            'data' => [
                'type' => 'appointments',
                'attributes' => [
                    'date' => '2026-01-01',
                    'start_time' => '16:00',
                    'email' => 'falseemail@gmail.com'
                ],
            ]
        ]);

        $response->assertJsonApiValidationErrors('start_time');
    }

    /** @test */
    public function appointments_may_only_last_an_hour()
    {
        Appointment::create([
            'date' => '2026-01-01',
            'start_time' => '12:00',
            'email' => 'falseemail@gmail.com'
        ]);

        $response = $this->postJson(route('api.v1.appointments.store'), [
            'data' => [
                'type' => 'appointments',
                'attributes' => [
                    'date' => '2026-01-01',
                    'start_time' => '12:00',
                    'email' => 'falseemail@gmail.com'
                ],
            ]
        ]);

        $response->assertJsonApiValidationErrors('start_time');

        $response2 = $this->postJson(route('api.v1.appointments.store'), [
            'data' => [
                'type' => 'appointments',
                'attributes' => [
                    'date' => '2026-01-01',
                    'start_time' => '11:35',
                    'email' => 'falseemail@gmail.com'
                ],
            ]
        ]);

        $response2->assertJsonApiValidationErrors('start_time');

        $response3 = $this->postJson(route('api.v1.appointments.store'), [
            'data' => [
                'type' => 'appointments',
                'attributes' => [
                    'date' => '2026-01-01',
                    'start_time' => '12:55',
                    'email' => 'falseemail@gmail.com'
                ],
            ]
        ]);

        $response3->assertJsonApiValidationErrors('start_time');

        $response4 = $this->postJson(route('api.v1.appointments.store'), [
            'data' => [
                'type' => 'appointments',
                'attributes' => [
                    'date' => '2026-01-01',
                    'start_time' => '14:05',
                    'email' => 'falseemail@gmail.com'
                ],
            ]
        ]);

        $response4->assertJsonApiValidationErrors('start_time');
    }

    /** @test */
    public function email_address_must_be_a_valid_email()
    {
        $response = $this->postJson(route('api.v1.appointments.store'), [
            'data' => [
                'type' => 'appointments',
                'attributes' => [
                    'date' => '2026-01-01',
                    'start_time' => '10:00',
                    'email' => 'wrongemail.com'
                ],
            ]
        ]);

        $response->assertJsonApiValidationErrors('email');
    }
}
