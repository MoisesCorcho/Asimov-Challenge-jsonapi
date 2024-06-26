<?php

namespace Tests\Feature\Appointments;

use Tests\TestCase;
use App\Models\Category;
use App\Models\Appointment;
use Illuminate\Foundation\Testing\RefreshDatabase;

/**
 * Este archivo se utiliza para verificar el correcto funcionamiento de los
 * filtros en las respuestas JSON de las citas (Appointments). Esto es crucial
 * para garantizar que la API cumpla con los requisitos de búsqueda y filtrado
 * según lo especificado por la especificación JSON:API.
 *
 * En la especificación JSON:API, los filtros son parámetros de consulta que
 * permiten a los clientes solicitar un subconjunto específico de recursos que
 * cumplan con ciertos criterios. Los filtros se aplican a las colecciones de
 * recursos y son útiles cuando se desea recuperar solo los recursos que coincidan
 * con ciertas condiciones.
 *
 * El formato general para especificar filtros en una solicitud JSON:API es agregar
 *  un parámetro de consulta llamado filter seguido de los criterios de filtro
 * específicos. Los filtros pueden ser simples o complejos, dependiendo de las
 * necesidades del cliente y de la implementación del servidor.
 *
 * Ej. GET /api/v1/appointments?filter[date]=2025-01-01
 *
 * En este ejemplo, la solicitud está solicitando todas las citas que tengan la
 * fecha igual a '2025-01-01'. El parámetro de consulta filter[date] indica que
 * se debe aplicar un filtro a la fecha de las citas, y el valor '2025-01-01'
 * especifica el valor que se debe coincidir.
 */
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
    public function can_filter_appointments_by_category(): void
    {
        $cat1 = Category::factory()->hasAppointments(3)->create(['id' => 1]);
        $cat2 = Category::factory()->hasAppointments()->create(['id' => 2]);
        Appointment::factory()->count(2)->create();

        // appointments?filter[categories]=1,2
        $url = route('api.v1.appointments.index', [
            'filter' => [
                'categories' => '1,2'
            ]
        ]);

        $this->getJson($url)
            ->assertJsonCount(4, 'data')
            ->assertSee($cat1->appointments[0]->date)
            ->assertSee($cat1->appointments[1]->date)
            ->assertSee($cat1->appointments[2]->date)
            ->assertSee($cat2->appointments[0]->date);
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
                'start-time' => '10'
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

        $this->getJson($url)->assertJsonApiError(
            title: 'Bad Request',
            detail: "The filter 'unknown' is not allowed in the 'appointments' resource.",
            status: '400'
        );
    }
}
