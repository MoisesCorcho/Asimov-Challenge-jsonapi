<?php

namespace Tests\Feature\Appointments;

use Tests\TestCase;
use App\Models\Appointment;
use Illuminate\Foundation\Testing\RefreshDatabase;

class IncludeCategoryTest extends TestCase
{

    use RefreshDatabase;

    /** @test */
    public function can_include_related_category_of_an_article(): void
    {
        $appointment = Appointment::factory()->create();

        $url = route('api.v1.appointments.show', [
            'appointment' => $appointment,
            'include' => 'category'
        ]);

        $this->getJson($url)->assertJson([
            'included' => [
                [
                    'type' => 'categories',
                    'id' => $appointment->category->getRouteKey(),
                    'attributes' => [
                        'name' => $appointment->category->name
                    ]
                ]
            ]
        ]);
    }

    /** @test */
    public function can_include_related_categories_of_multiple_articles(): void
    {
        $appointment = Appointment::factory()->create()->load('category');
        $appointment2 = Appointment::factory()->create()->load('category');

        $url = route('api.v1.appointments.index', [
            'include' => 'category'
        ]);

        $this->getJson($url)->assertJson([
            'included' => [
                [
                    'type' => 'categories',
                    'id' => $appointment->category->getRouteKey(),
                    'attributes' => [
                        'name' => $appointment->category->name
                    ]
                ],
                [
                    'type' => 'categories',
                    'id' => $appointment2->category->getRouteKey(),
                    'attributes' => [
                        'name' => $appointment2->category->name
                    ]
                ],
            ]
        ]);
    }
}
