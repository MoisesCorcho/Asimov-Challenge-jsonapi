<?php

namespace Tests\Feature\Appointments;

use App\Models\Appointment;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class ListAppointmentTest extends TestCase
{

    use RefreshDatabase;

    /** @test */
    public function can_fetch_a_single_appointment(): void
    {

        $this->withoutExceptionHandling();

        $appointment = Appointment::factory()->create();

        // $response = $this->getJson('api/v1/appointments/'.$appointment->getRouteKey());
        $response = $this->getJson(route('api.v1.appointments.show', $appointment));

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
    public function can_fetch_all_appointments()
    {
        $this->withoutExceptionHandling();

        $appointments = Appointment::factory()->count(3)->create();

        $response = $this->getJson(route('api.v1.appointments.index'));

        $response->assertExactJson([
            'data' => [
                [
                    'type' => 'appointments',
                    'id' => (string) $appointments[0]->getRouteKey(),
                    'attributes' => [
                        'date' => $appointments[0]->date,
                        'start_time' => $appointments[0]->start_time,
                        'email' => $appointments[0]->email
                    ],
                    'links' => [
                        'self' => route('api.v1.appointments.show', $appointments[0])
                    ]
                ],
                [
                    'type' => 'appointments',
                    'id' => (string) $appointments[1]->getRouteKey(),
                    'attributes' => [
                        'date' => $appointments[1]->date,
                        'start_time' => $appointments[1]->start_time,
                        'email' => $appointments[1]->email
                    ],
                    'links' => [
                        'self' => route('api.v1.appointments.show', $appointments[1])
                    ]
                ],
                [
                    'type' => 'appointments',
                    'id' => (string) $appointments[2]->getRouteKey(),
                    'attributes' => [
                        'date' => $appointments[2]->date,
                        'start_time' => $appointments[2]->start_time,
                        'email' => $appointments[2]->email
                    ],
                    'links' => [
                        'self' => route('api.v1.appointments.show', $appointments[2])
                    ]
                ],
            ],
            'links' => [
                'self' => route('api.v1.appointments.index')
            ]
        ]);
    }

}
