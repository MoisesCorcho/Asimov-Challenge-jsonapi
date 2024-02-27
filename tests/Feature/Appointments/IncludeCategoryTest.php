<?php

namespace Tests\Feature\Appointments;

use App\Models\Appointment;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

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
}
