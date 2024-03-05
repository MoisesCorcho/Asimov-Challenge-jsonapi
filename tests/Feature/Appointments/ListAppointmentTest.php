<?php

namespace Tests\Feature\Appointments;

use App\Models\Appointment;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ListAppointmentTest extends TestCase
{

    use RefreshDatabase;

    /** @test */
    public function can_fetch_a_single_appointment(): void
    {
        $this->withoutExceptionHandling();

        $appointment = Appointment::factory()->create();

        $response = $this->getJson(route('api.v1.appointments.show', $appointment));

        $response->assertJsonApiResource($appointment, [
            'date' => $appointment->date,
            'start_time' => $appointment->start_time,
            'email' => $appointment->email
        ])->assertJsonApiRelationshipLinks($appointment, ['category', 'author']);
    }

    /** @test */
    public function can_fetch_all_appointments()
    {
        $this->withoutExceptionHandling();

        $appointments = Appointment::factory()->count(3)->create();

        $response = $this->getJson(route('api.v1.appointments.index'));

        $response->assertJsonApiResourceCollection($appointments, [
            'date', 'start_time', 'email'
        ]);
    }

}
