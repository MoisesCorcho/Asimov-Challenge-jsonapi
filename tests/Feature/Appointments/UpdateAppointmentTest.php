<?php

namespace Tests\Feature\Appointments;

use Tests\TestCase;
use App\Models\User;
use App\Models\Category;
use App\JsonApi\Document;
use App\Models\Appointment;
use Illuminate\Foundation\Testing\RefreshDatabase;

class UpdateAppointmentTest extends TestCase
{

    use RefreshDatabase;

    /** @test */
    public function can_update_appointments()
    {
        $this->withoutExceptionHandling();

        $appointment = Appointment::factory()->create([
            'date' => '2026-01-01',
            'start_time' => '12:00',
        ]);

        $response = $this->patchJson(route('api.v1.appointments.update', $appointment),
            Document::type('appointments')
                ->id(1)
                ->attributes([
                    'date' => '2026-01-01',
                    'start_time' => '10:00',
                    'email' => 'updatedupdatedfalseemail@gmail.com'
                ])
                ->toArray()
        );

        $response->assertOk();

        $appointment2 = Appointment::first();

        $response->assertJsonApiResource($appointment2, [
            'date' => $appointment2->date,
            'start_time' => $appointment2->start_time,
            'email' => $appointment2->email
        ]);
    }

    /** @test */
    public function date_is_required()
    {
        $appointment = Appointment::factory()->create();

        $response = $this->patchJson(route('api.v1.appointments.update', $appointment), [
            'data' => [
                'id' => (string) $appointment->getRouteKey(),
                'type' => 'appointments',
                'attributes' => [
                    'start_time' => '11:00',
                    'email' => 'updatedfalseemail@gmail.com'
                ],
            ]
        ]);

        $response->assertJsonApiValidationErrors('date');
    }

    /** @test */
    public function date_format_must_be_Year_month_day()
    {
        $appointment = Appointment::factory()->create();

        $response = $this->patchJson(route('api.v1.appointments.update', $appointment), [
            'data' => [
                'id' => (string) $appointment->getRouteKey(),
                'type' => 'appointments',
                'attributes' => [
                    'date' => '02-11-2025',
                    'start_time' => '11:00',
                    'email' => 'updatedfalseemail@gmail.com'
                ],
            ]
        ]);

        $response->assertJsonApiValidationErrors('date');
    }

    /** @test */
    public function the_appointment_date_must_be_greater_than_or_equal_to_the_current_date()
    {

        $appointment = Appointment::factory()->create();

        $response = $this->patchJson(route('api.v1.appointments.update', $appointment), [
            'data' => [
                'id' => (string) $appointment->getRouteKey(),
                'type' => 'appointments',
                'attributes' => [
                    'date' => '2022-02-05',
                    'start_time' => '11:00',
                    'email' => 'updatedfalseemail@gmail.com'
                ],
            ]
        ]);

        $response->assertJsonApiValidationErrors('date');
    }

    /** @test */
    public function start_time_is_required()
    {
        $appointment = Appointment::factory()->create();

        $response = $this->patchJson(route('api.v1.appointments.update', $appointment), [
            'data' => [
                'id' => (string) $appointment->getRouteKey(),
                'type' => 'appointments',
                'attributes' => [
                    'date' => date('Y-m-d'),
                    'email' => 'updatedfalseemail@gmail.com'
                ],
            ]
        ]);

        $response->assertJsonApiValidationErrors('start_time');
    }

    /** @test */
    public function email_is_required()
    {
        $appointment = Appointment::factory()->create();

        $response = $this->patchJson(route('api.v1.appointments.update', $appointment), [
            'data' => [
                'id' => (string) $appointment->getRouteKey(),
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
        $appointment = Appointment::factory()->create();

        $response = $this->patchJson(route('api.v1.appointments.update', $appointment), [
            'data' => [
                'id' => (string) $appointment->getRouteKey(),
                'type' => 'appointments',
                'attributes' => [
                    'date' => '2024-02-10',
                    'start_time' => '11:00',
                    'email' => 'updatedfalseemail@gmail.com'
                ],
            ]
        ]);

        $response->assertJsonApiValidationErrors('date');
    }

    /** @test */
    public function time_format_must_be_Hour_Minutes()
    {
        $appointment = Appointment::factory()->create();

        $response = $this->patchJson(route('api.v1.appointments.update', $appointment), [
            'data' => [
                'id' => (string) $appointment->getRouteKey(),
                'type' => 'appointments',
                'attributes' => [
                    'date' => '2026-01-07',
                    'start_time' => '11:00:00',
                    'email' => 'updatedfalseemail@gmail.com'
                ],
            ]
        ]);

        $response->assertJsonApiValidationErrors('start_time');
    }

    /** @test */
    public function time_can_not_be_before_the_current_time()
    {
        $appointment = Appointment::factory()->create();

        $response = $this->patchJson(route('api.v1.appointments.update', $appointment), [
            'data' => [
                'id' => (string) $appointment->getRouteKey(),
                'type' => 'appointments',
                'attributes' => [
                    'date' => now()->toDateString(),
                    'start_time' => now()->subHour()->format('H:i'),
                    'email' => 'updatedfalseemail@gmail.com'
                ],
            ]
        ]);

        $response->assertJsonApiValidationErrors('start_time');
    }

    /** @test */
    public function time_must_be_in_office_time()
    {
        $appointment = Appointment::factory()->create();

        $response = $this->patchJson(route('api.v1.appointments.update', $appointment), [
            'data' => [
                'id' => (string) $appointment->getRouteKey(),
                'type' => 'appointments',
                'attributes' => [
                    'date' => '2026-01-01',
                    'start_time' => '07:00',
                    'email' => 'updatedfalseemail@gmail.com'
                ],
            ]
        ]);

        $response = $this->patchJson(route('api.v1.appointments.update', $appointment), [
            'data' => [
                'id' => (string) $appointment->getRouteKey(),
                'type' => 'appointments',
                'attributes' => [
                    'date' => '2026-01-01',
                    'start_time' => '16:00',
                    'email' => 'updatedfalseemail@gmail.com'
                ],
            ]
        ]);

        $response->assertJsonApiValidationErrors('start_time');
    }

    /** @test */
    public function appointments_may_only_last_an_hour()
    {
        $user = User::factory()->create();
        $category = Category::factory()->create();

        $appointment = Appointment::create([
            'date' => '2026-01-01',
            'start_time' => '12:00',
            'email' => 'updatedfalseemail@gmail.com',
            'category_id' => $category->getRouteKey(),
            'user_id' => $user->getRouteKey()
        ]);

        $response = $this->patchJson(route('api.v1.appointments.update', $appointment), [
            'data' => [
                'id' => (string) $appointment->getRouteKey(),
                'type' => 'appointments',
                'attributes' => [
                    'date' => '2026-01-01',
                    'start_time' => '12:00',
                    'email' => 'updatedfalseemail@gmail.com'
                ],
            ]
        ]);

        $response->assertJsonApiValidationErrors('start_time');


        $response2 = $this->patchJson(route('api.v1.appointments.update', $appointment), [
            'data' => [
                'id' => (string) $appointment->getRouteKey(),
                'type' => 'appointments',
                'attributes' => [
                    'date' => '2026-01-01',
                    'start_time' => '11:35',
                    'email' => 'updatedfalseemail@gmail.com'
                ],
            ]
        ]);

        $response2->assertJsonApiValidationErrors('start_time');


        $response3 = $this->patchJson(route('api.v1.appointments.update', $appointment), [
            'data' => [
                'id' => (string) $appointment->getRouteKey(),
                'type' => 'appointments',
                'attributes' => [
                    'date' => '2026-01-01',
                    'start_time' => '12:55',
                    'email' => 'updatedfalseemail@gmail.com'
                ],
            ]
        ]);

        $response3->assertJsonApiValidationErrors('start_time');


        $response4 = $this->patchJson(route('api.v1.appointments.update', $appointment), [
            'data' => [
                'id' => (string) $appointment->getRouteKey(),
                'type' => 'appointments',
                'attributes' => [
                    'date' => '2026-01-01',
                    'start_time' => '14:05',
                    'email' => 'updatedfalseemail@gmail.com'
                ],
            ]
        ]);

        $response4->assertJsonApiValidationErrors('start_time');
    }

    /** @test */
    public function email_address_must_be_a_valid_email()
    {
        $appointment = Appointment::factory()->create();

        $response = $this->patchJson(route('api.v1.appointments.update', $appointment), [
            'data' => [
                'id' => (string) $appointment->getRouteKey(),
                'type' => 'appointments',
                'attributes' => [
                    'date' => '2026-01-01',
                    'start_time' => '10:00',
                    'email' => 'updatedwrongemail.com'
                ],
            ]
        ]);

        $response->assertJsonApiValidationErrors('email');
    }
}
