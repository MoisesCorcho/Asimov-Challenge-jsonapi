<?php

namespace Tests\Feature\Appointments;

use App\Models\Appointment;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class DeleteAppointmentTest extends TestCase
{

    use RefreshDatabase;

    /** @test */
    public function can_delete_appointments(): void
    {
        $appointment = Appointment::factory()->create();

        $this->deleteJson(route('api.v1.appointments.destroy', $appointment))
            ->assertNoContent();

        $this->assertDatabaseCount('appointments', 0);
    }
}
