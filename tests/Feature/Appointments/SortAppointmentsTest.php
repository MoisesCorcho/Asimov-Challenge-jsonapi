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

        $this->getJson($url)->assertStatus(400);
    }

    /** @test */
    public function can_paginate_appointments(): void
    {
        $appointments = Appointment::factory()->count(6)->create();

        // appointments?page[size]=2&page[number]=2
        $url = route('api.v1.appointments.index', [
            'page' => [
                'size' => 2,
                'number' => 2
            ]
        ]);

        $response = $this->getJson($url);

        $response->assertSee([
            $appointments[2]->date,
            $appointments[3]->date
        ]);

        $response->assertDontSee([
            $appointments[0]->date,
            $appointments[1]->date,
            $appointments[4]->date,
            $appointments[5]->date
        ]);

        $response->assertJsonStructure([
            'links' => ['first', 'last', 'prev', 'next']
        ]);

        $firstLink = urldecode($response->json('links.first'));
        $lastLink = urldecode($response->json('links.last'));
        $prevLink = urldecode($response->json('links.prev'));
        $nextLink = urldecode($response->json('links.next'));

        $this->assertStringContainsString('page[size]=2', $firstLink);
        $this->assertStringContainsString('page[number]=1', $firstLink);

        $this->assertStringContainsString('page[size]=2', $lastLink);
        $this->assertStringContainsString('page[number]=3', $lastLink);

        $this->assertStringContainsString('page[size]=2', $prevLink);
        $this->assertStringContainsString('page[number]=1', $prevLink);

        $this->assertStringContainsString('page[size]=2', $nextLink);
        $this->assertStringContainsString('page[number]=3', $nextLink);

    }

    /** @test */
    public function can_paginate_and_sort_appointments(): void
    {
        Appointment::factory()->create([
            'date' => '2025-04-01',
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

        // appointments?sort=date&page[size]=1&page[number]=2
        $url = route('api.v1.appointments.index', [
            'sort' => 'date',
            'page' => [
                'size' => 1,
                'number' => 2
            ]
        ]);

        $response = $this->getJson($url);

        $response->assertSee([
            '2025-04-01',
        ]);

        $response->assertDontSee([
            '2025-03-02',
            '2025-04-02'
        ]);

        $firstLink = urldecode($response->json('links.first'));
        $lastLink = urldecode($response->json('links.last'));
        $prevLink = urldecode($response->json('links.prev'));
        $nextLink = urldecode($response->json('links.next'));

        $this->assertStringContainsString('sort=date', $firstLink);
        $this->assertStringContainsString('sort=date', $lastLink);
        $this->assertStringContainsString('sort=date', $prevLink);
        $this->assertStringContainsString('sort=date', $nextLink);

    }

}
