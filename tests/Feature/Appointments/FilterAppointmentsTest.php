<?php

namespace Tests\Feature\Appointments;

use App\Models\Appointment;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class FilterAppointmentsTest extends TestCase
{

    use RefreshDatabase;

    /** @test */
    public function can_filter_appointments_by_date(): void
    {
        Appointment::factory()->create([
            'date' => '2025-02-03'
        ]);

        Appointment::factory()->create([
            'date' => '2026-01-01'
        ]);

        // appointments?filter[date]='2025'

        $url = route('api.v1.appointments.index', [
            'filter' => [
                'date' => '2025'
            ]
        ]);

        $this->getJson($url)
            ->assertJsonCount(1, 'data')
            ->assertSee('2025-02-03')
            ->assertDontSee('2026-01-01');
    }

    /** @test */
    public function can_filter_appointments_by_year(): void
    {
        Appointment::factory()->create([
            'date' => '2025-02-03'
        ]);

        Appointment::factory()->create([
            'date' => '2026-01-01'
        ]);

        // appointments?filter[year]='2025'

        $url = route('api.v1.appointments.index', [
            'filter' => [
                'year' => '2025'
            ]
        ]);

        $this->getJson($url)
            ->assertJsonCount(1, 'data')
            ->assertSee('2025-02-03')
            ->assertDontSee('2026-01-01');
    }

    /** @test */
    public function can_filter_appointments_by_month(): void
    {
        Appointment::factory()->create([
            'date' => '2025-02-03'
        ]);

        Appointment::factory()->create([
            'date' => '2026-01-01'
        ]);

        // appointments?filter[month]='1'

        $url = route('api.v1.appointments.index', [
            'filter' => [
                'month' => '1'
            ]
        ]);

        $this->getJson($url)
            ->assertJsonCount(1, 'data')
            ->assertSee('2026-01-01')
            ->assertDontSee('2025-02-03');
    }

    /** @test */
    public function can_filter_appointments_by_start_time(): void
    {
        Appointment::factory()->create([
            'date' => '2025-02-03',
            'start_time' => '10:00'
        ]);

        Appointment::factory()->create([
            'date' => '2026-01-01',
            'start_time' => '12:00'
        ]);

        // appointments?filter[start_time]='10'

        $url = route('api.v1.appointments.index', [
            'filter' => [
                'start_time' => '10'
            ]
        ]);

        $this->getJson($url)
            ->assertJsonCount(1, 'data')
            ->assertSee('10:00')
            ->assertDontSee('12:00');
    }

    /** @test */
    public function can_filter_appointments_by_email(): void
    {
        Appointment::factory()->create([
            'date' => '2025-02-03',
            'start_time' => '10:00',
            'email' => 'firstemail@gmail.com'
        ]);

        Appointment::factory()->create([
            'date' => '2026-01-01',
            'start_time' => '12:00',
            'email' => 'secondemail@gmail.com'
        ]);

        // appointments?filter[email]='firstemail'

        $url = route('api.v1.appointments.index', [
            'filter' => [
                'email' => 'secondemail'
            ]
        ]);

        $this->getJson($url)
            ->assertJsonCount(1, 'data')
            ->assertSee('secondemail@gmail.com')
            ->assertDontSee('firstemail@gmail.com');
    }

    /** @test */
    public function cannot_filter_appointments_by_unknown_filters(): void
    {
        Appointment::factory()->count(2)->create();

        // appointments?filter[unknown]='filter'

        $url = route('api.v1.appointments.index', [
            'filter' => [
                'unknown' => 'filter'
            ]
        ]);

        $this->getJson($url)->assertStatus(400);
    }
}