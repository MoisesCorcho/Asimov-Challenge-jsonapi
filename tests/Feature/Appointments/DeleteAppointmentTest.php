<?php

namespace Tests\Feature\Appointments;

use Tests\TestCase;
use App\Models\User;
use App\Models\Appointment;
use Laravel\Sanctum\Sanctum;
use Illuminate\Foundation\Testing\RefreshDatabase;

class DeleteAppointmentTest extends TestCase
{

    use RefreshDatabase;

    /** @test */
    public function guests_cannot_delete_appointments(): void
    {
        $appointment = Appointment::factory()->create();

        $this->deleteJson(route('api.v1.appointments.destroy', $appointment))
            ->assertJsonApiError(
                title: 'Unauthenticated',
                detail: 'This action requires authentication.',
                status: '401'
            );

        $this->assertDatabaseCount('appointments', 1);
    }

    /** @test */
    public function can_delete_owned_appointments(): void
    {
        /** Cualquier usuario que se cree tendrá los permisos necesarios
         * para la autenticacion de Sanctum
         */
        $appointment = Appointment::factory()->create();

        /** El token para este usuario se crea con la habilidad (Hability)
         *  de crear, es decir, con la convencion 'appointment:delete'
         *  para que asi, no haya problemas de autorizacion con los Policies
         */
        Sanctum::actingAs($appointment->author, ['appointment:delete']);

        $this->deleteJson(route('api.v1.appointments.destroy', $appointment))
            ->assertNoContent();

        $this->assertDatabaseCount('appointments', 0);
    }

    /** @test */
    public function can_delete_appointments_owned_by_other_users(): void
    {
        /** Cualquier usuario que se cree tendrá los permisos necesarios
         * para la autenticacion de Sanctum
         */
        $appointment = Appointment::factory()->create();

        Sanctum::actingAs(User::factory()->create());

        $this->deleteJson(route('api.v1.appointments.destroy', $appointment))
            ->assertForbidden();

        $this->assertDatabaseCount('appointments', 1);
    }
}
