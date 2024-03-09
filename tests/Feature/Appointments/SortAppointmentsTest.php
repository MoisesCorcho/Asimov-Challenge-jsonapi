<?php

namespace Tests\Feature\Appointments;

use App\Models\Appointment;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class SortAppointmentsTest extends TestCase
{

    use RefreshDatabase;

    /** @test */
    public function can_sort_appointments_by_date(): void
    {
        Appointment::factory()->create(['date' => '2025-03-02']);
        Appointment::factory()->create(['date' => '2025-01-02']);
        Appointment::factory()->create(['date' => '2025-02-02']);

        // appointments?sort=date -- Ascending
        // appointments?sort=-date -- Descending - The minus sign (-) is added in front of the field
        $url = route('api.v1.appointments.index', ['sort' => 'date']);

        $this->getJson($url)->assertSeeInOrder([
            '2025-01-02',
            '2025-02-02',
            '2025-03-02'
        ]);
    }

    /** @test */
    public function can_sort_appointments_by_date_descending(): void
    {
        Appointment::factory()->create(['date' => '2025-02-02']);
        Appointment::factory()->create(['date' => '2025-02-03']);
        Appointment::factory()->create(['date' => '2025-02-01']);

        // appointments?sort=date -- Ascending
        // appointments?sort=-date -- Descending - The minus sign (-) is added in front of the field
        $url = route('api.v1.appointments.index', ['sort' => '-date']);

        $this->getJson($url)->assertSeeInOrder([
            '2025-02-03',
            '2025-02-02',
            '2025-02-01'
        ]);
    }

    /** @test */
    public function can_sort_appointments_by_start_time(): void
    {
        Appointment::factory()->create([
            'date' => '2025-03-02',
            'start_time' => '14:00'
        ]);

        Appointment::factory()->create([
            'date' => '2025-03-02',
            'start_time' => '09:00'
        ]);

        Appointment::factory()->create([
            'date' => '2025-03-02',
            'start_time' => '11:00'
        ]);

        $url = route('api.v1.appointments.index', ['sort' => 'start_time']);

        $this->getJson($url)->assertSeeInOrder([
            '09:00',
            '11:00',
            '14:00'
        ]);
    }

    /** @test */
    public function can_sort_appointments_by_start_time_descending(): void
    {
        Appointment::factory()->create([
            'date' => '2025-03-02',
            'start_time' => '14:00'
        ]);

        Appointment::factory()->create([
            'date' => '2025-03-02',
            'start_time' => '09:00'
        ]);

        Appointment::factory()->create([
            'date' => '2025-03-02',
            'start_time' => '11:00'
        ]);

        // appointments?sort=start_time  -- Ascending
        // appointments?sort=-start_time -- Descending - The minus sign (-) is added in front of the field
        $url = route('api.v1.appointments.index', ['sort' => '-start_time']);

        $this->getJson($url)->assertSeeInOrder([
            '14:00',
            '11:00',
            '09:00'
        ]);
    }

    /** @test */
    public function can_sort_appointments_by_date_and_start_time(): void
    {
        Appointment::factory()->create([
            'date' => '2025-04-02',
            'start_time' => '14:00'
        ]);

        Appointment::factory()->create([
            'date' => '2025-03-02',
            'start_time' => '09:00'
        ]);

        Appointment::factory()->create([
            'date' => '2025-04-02',
            'start_time' => '11:00'
        ]);

        $url = route('api.v1.appointments.index', ['sort' => 'date,-start_time']);

        $this->getJson($url)->assertSeeInOrder([
            '09:00',
            '14:00',
            '11:00'
        ]);
    }

    /** @test */
    public function cannot_sort_appointments_by_unknown_fields(): void
    {
        Appointment::factory()->count(3)->create();

        $url = route('api.v1.appointments.index', ['sort' => 'unknown']);

        $this->getJson($url)->assertJsonApiError(
            title: 'Bad Request',
            detail: "The sort field 'unknown' is not allowed in the 'appointments' resource.",
            status: '400'
        );
    }

}
