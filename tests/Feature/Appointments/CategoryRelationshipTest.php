<?php

namespace Tests\Feature\Appointments;

use Tests\TestCase;
use App\Models\Appointment;
use App\Models\Category;
use Illuminate\Foundation\Testing\RefreshDatabase;

class CategoryRelationshipTest extends TestCase
{

    use RefreshDatabase;

    /** @test */
    public function can_fetch_the_associated_category_identifier(): void
    {
        $appointment = Appointment::factory()->create();

        $url = route('api.v1.appointments.relationships.category', $appointment);

        $response = $this->getJson($url);

        $response->assertExactJson([
            'data' => [
                'id' => (string) $appointment->category->getRouteKey(),
                'type' => 'categories'
            ]
        ]);
    }

    /** @test */
    public function can_fetch_the_associated_category_resource(): void
    {
        $appointment = Appointment::factory()->create();

        $url = route('api.v1.appointments.category', $appointment);

        $response = $this->getJson($url);

        $response->assertJson([
            'data' => [
                'id' => (string) $appointment->category->getRouteKey(),
                'type' => 'categories',
                'attributes' => [
                    'name' => $appointment->category->name
                ]
            ]
        ]);
    }

    /** @test */
    public function can_update_the_associated_category(): void
    {
        $category = Category::factory()->create();
        $appointment = Appointment::factory()->create();

        $url = route('api.v1.appointments.relationships.category', $appointment);

        $response = $this->patchJson($url, [
            'data' => [
                'type' => 'categories',
                'id'   => (string) $category->getRouteKey()
            ]
        ]);

        $response->assertExactJson([
            'data' => [
                'type' => 'categories',
                'id'   => (string) $category->getRouteKey()
            ]
        ]);

        $this->assertDatabaseHas('appointments', [
            'date' => $appointment->date,
            'category_id' => (string) $category->id
        ]);

    }
}
