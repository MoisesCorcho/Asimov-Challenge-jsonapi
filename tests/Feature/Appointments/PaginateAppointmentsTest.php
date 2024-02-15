<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\Appointment;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class PaginateAppointmentsTest extends TestCase
{

    use RefreshDatabase;

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
    public function can_paginate_sorted_appointments(): void
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

    /** @test */
    public function can_paginate_filtered_appointments(): void
    {
        Appointment::factory()->count(3)->create();

        Appointment::factory()->create([
            'date' => '2025-04-01',
            'start_time' => '14:00',
            'email' => 'firstfalseemail@gmail.com'
        ]);

        Appointment::factory()->create([
            'date' => '2025-03-02',
            'start_time' => '09:00',
            'email' => 'secondfalseemail@gmail.com'
        ]);

        Appointment::factory()->create([
            'date' => '2025-04-02',
            'start_time' => '11:00',
            'email' => 'thirdfalseemail@gmail.com'
        ]);

        // appointments?filter[email]=false&page[size]=1&page[number]=2
        $url = route('api.v1.appointments.index', [
            'filter[email]' => 'false',
            'page' => [
                'size' => 1,
                'number' => 2
            ]
        ]);

        $response = $this->getJson($url);

        $firstLink = urldecode($response->json('links.first'));
        $lastLink = urldecode($response->json('links.last'));
        $prevLink = urldecode($response->json('links.prev'));
        $nextLink = urldecode($response->json('links.next'));

        $this->assertStringContainsString('filter[email]=false', $firstLink);
        $this->assertStringContainsString('filter[email]=false', $lastLink);
        $this->assertStringContainsString('filter[email]=false', $prevLink);
        $this->assertStringContainsString('filter[email]=false', $nextLink);

    }

}
