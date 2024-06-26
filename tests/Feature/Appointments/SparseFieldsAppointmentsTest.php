<?php

namespace Tests\Feature\Appointments;

use App\Models\Appointment;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * Este test se encarga de verificar el funcionamiento de la funcionalidad
 * de campos escasos (sparse fields) en el contexto de las citas (Appointment).
 * Esta clase de prueba garantiza que la API pueda responder correctamente a las
 * solicitudes que especifican campos específicos a incluir o excluir en las
 * respuestas JSON de las citas.
 */
class SparseFieldsAppointmentsTest extends TestCase
{

    use RefreshDatabase;

    /** @test */
    public function specific_fields_can_be_requested_in_the_appointment_index(): void
    {
        $appointment = Appointment::factory()->create([
            'date' => '2026-01-01',
            'start_time' => '10:00'
        ]);

        // appointments?fields[appointments]=date,start_time
        $url = route('api.v1.appointments.index', [
            'fields' => [
                'appointments' => 'id,date,start-time'
            ]
        ]);

        $this->getJson($url)->assertJsonFragment([
            'date' => $appointment->date,
            'start-time' => $appointment->start_time
        ])->assertJsonMissing([
            'email' => $appointment->email
        ])->assertJsonMissing([
            'email' => null
        ]);
    }

    /** @test */
    public function specific_fields_can_be_requested_in_the_appointment_show(): void
    {
        $appointment = Appointment::factory()->create([
            'date' => '2026-01-01',
            'start_time' => '10:00'
        ]);

        // appointments/id?fields[appointments]=date,start_time
        $url = route('api.v1.appointments.show', [
            'appointment' => $appointment,
            'fields' => [
                'appointments' => 'id,date,start-time'
            ]
        ]);

        $this->getJson($url)->assertJsonFragment([
            'date' => $appointment->date,
            'start-time' => $appointment->start_time
        ])->assertJsonMissing([
            'email' => $appointment->email
        ])->assertJsonMissing([
            'email' => null
        ]);
    }

    /** @test */
    public function route_key_must_be_added_automatically_in_the_appointment_index(): void
    {
        $appointment = Appointment::factory()->create([
            'date' => '2026-01-01',
        ]);

        // appointments?fields[appointments]=date
        $url = route('api.v1.appointments.index', [
            'fields' => [
                'appointments' => 'date'
            ]
        ]);

        $this->getJson($url)->assertJsonFragment([
            'date' => $appointment->date,
        ])->assertJsonMissing([
            'start-time' => $appointment->start_time,
            'email' => $appointment->email
        ]);
    }
    /** @test */
    public function route_key_must_be_added_automatically_in_the_appointment_show(): void
    {
        $appointment = Appointment::factory()->create([
            'date' => '2026-01-01',
        ]);

        // appointments/id?fields[appointments]=date
        $url = route('api.v1.appointments.show', [
            'appointment' => $appointment,
            'fields' => [
                'appointments' => 'date'
            ]
        ]);

        $this->getJson($url)->assertJsonFragment([
            'date' => $appointment->date,
        ])->assertJsonMissing([
            'start-time' => $appointment->start_time,
            'email' => $appointment->email
        ]);
    }
}
