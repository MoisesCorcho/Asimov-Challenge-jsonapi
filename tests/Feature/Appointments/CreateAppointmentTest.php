<?php

namespace Tests\Feature\Appointments;

use Tests\TestCase;
use App\Models\User;
use App\Models\Category;
use App\JsonApi\Document;
use App\Models\Appointment;
use Laravel\Sanctum\Sanctum;
use Illuminate\Foundation\Testing\RefreshDatabase;

class CreateAppointmentTest extends TestCase
{

    use RefreshDatabase;

    /** @test */
    public function guests_cannot_create_appointments()
    {
        $this->postJson(route('api.v1.appointments.store'))
            ->assertJsonApiError(
                title: 'Unauthenticated',
                detail: 'This action requires authentication.',
                status: '401'
            );

        $this->assertDatabaseCount('appointments', 0);
    }

    /** @test */
    public function can_create_appointments()
    {
        $this->withoutExceptionHandling();

        $user = User::factory()->create();
        $category = Category::factory()->create();

        Sanctum::actingAs($user);

        $response = $this->postJson(route('api.v1.appointments.store'),
            Document::type('appointments')
                ->attributes([
                    'date' => '2025-11-17',
                    'start_time' => '11:00',
                    'email' => 'falseemail@gmail.com'
                ])->relationshipsData([
                    'category' => $category,
                    'author'   => $user
                ])->toArray()
        );

        $response->assertCreated();

        $appointment = Appointment::first();

        $response->assertJsonApiResource($appointment, [
            'date' => $appointment->date,
            'start_time' => substr($appointment->start_time, 0, 5),
            'email' => $appointment->email
        ]);

        $this->assertDatabaseHas('appointments', [
            'date'        => $appointment->date,
            'user_id'     => $user->id,
            'category_id' => $category->id
        ]);

    }

    /** @test */
    public function date_is_required()
    {
        /** Cualquier usuario que se cree tendrá los permisos necesarios
         * para la autenticacion de Sanctum
         */
        Sanctum::actingAs(User::factory()->create());

        $response = $this->postJson(route('api.v1.appointments.store'), [
            'data' => [
                'type' => 'appointments',
                'attributes' => [
                    'start_time' => '11:00',
                    'email' => 'falseemail@gmail.com'
                ],
            ]
        ]);

        $response->assertJsonApiValidationErrors('date');
    }

    /** @test */
    public function date_format_must_be_Year_month_day()
    {
        /** Cualquier usuario que se cree tendrá los permisos necesarios
         * para la autenticacion de Sanctum
         */
        Sanctum::actingAs(User::factory()->create());

        $response = $this->postJson(route('api.v1.appointments.store'), [
            'data' => [
                'type' => 'appointments',
                'attributes' => [
                    'date' => '02-11-2025',
                    'start_time' => '11:00',
                    'email' => 'falseemail@gmail.com'
                ],
            ]
        ]);

        $response->assertJsonApiValidationErrors('date');
    }

    /** @test */
    public function the_appointment_date_must_be_greater_than_or_equal_to_the_current_date()
    {
        /** Cualquier usuario que se cree tendrá los permisos necesarios
         * para la autenticacion de Sanctum
         */
        Sanctum::actingAs(User::factory()->create());

        $response = $this->postJson(route('api.v1.appointments.store'), [
            'data' => [
                'type' => 'appointments',
                'attributes' => [
                    'date' => '2022-02-05',
                    'start_time' => '11:00',
                    'email' => 'falseemail@gmail.com'
                ],
            ]
        ]);

        $response->assertJsonApiValidationErrors('date');
    }

    /** @test */
    public function start_time_is_required()
    {
        /** Cualquier usuario que se cree tendrá los permisos necesarios
         * para la autenticacion de Sanctum
         */
        Sanctum::actingAs(User::factory()->create());

        $response = $this->postJson(route('api.v1.appointments.store'), [
            'data' => [
                'type' => 'appointments',
                'attributes' => [
                    'date' => date('Y-m-d'),
                    'email' => 'falseemail@gmail.com'
                ],
            ]
        ]);

        $response->assertJsonApiValidationErrors('start_time');
    }

    /** @test */
    public function email_is_required()
    {
        /** Cualquier usuario que se cree tendrá los permisos necesarios
         * para la autenticacion de Sanctum
         */
        Sanctum::actingAs(User::factory()->create());

        $response = $this->postJson(route('api.v1.appointments.store'), [
            'data' => [
                'type' => 'appointments',
                'attributes' => [
                    'date' => date('Y-m-d'),
                    'start_time' => '11:00'
                ],
            ]
        ]);

        $response->assertJsonApiValidationErrors('email');
    }

    /** @test */
    public function date_can_not_be_weekeend()
    {
        /** Cualquier usuario que se cree tendrá los permisos necesarios
         * para la autenticacion de Sanctum
         */
        Sanctum::actingAs(User::factory()->create());

        $response = $this->postJson(route('api.v1.appointments.store'), [
            'data' => [
                'type' => 'appointments',
                'attributes' => [
                    'date' => '2024-02-10',
                    'start_time' => '11:00',
                    'email' => 'falseemail@gmail.com'
                ],
            ]
        ]);

        $response->assertJsonApiValidationErrors('date');
    }

    /** @test */
    public function time_format_must_be_Hour_Minutes()
    {
        /** Cualquier usuario que se cree tendrá los permisos necesarios
         * para la autenticacion de Sanctum
         */
        Sanctum::actingAs(User::factory()->create());

        $response = $this->postJson(route('api.v1.appointments.store'), [
            'data' => [
                'type' => 'appointments',
                'attributes' => [
                    'date' => '2026-01-07',
                    'start_time' => '11:00:00',
                    'email' => 'falseemail@gmail.com'
                ],
            ]
        ]);

        $response->assertJsonApiValidationErrors('start_time');
    }

    /** @test */
    public function time_can_not_be_before_the_current_time()
    {
        /** Cualquier usuario que se cree tendrá los permisos necesarios
         * para la autenticacion de Sanctum
         */
        Sanctum::actingAs(User::factory()->create());

        $response = $this->postJson(route('api.v1.appointments.store'), [
            'data' => [
                'type' => 'appointments',
                'attributes' => [
                    'date' => now()->toDateString(),
                    'start_time' => now()->subHour()->format('H:i'),
                    'email' => 'falseemail@gmail.com'
                ],
            ]
        ]);

        $response->assertJsonApiValidationErrors('start_time');
    }

    /** @test */
    public function time_must_be_in_office_time()
    {
        /** Cualquier usuario que se cree tendrá los permisos necesarios
         * para la autenticacion de Sanctum
         */
        Sanctum::actingAs(User::factory()->create());

        $response = $this->postJson(route('api.v1.appointments.store'), [
            'data' => [
                'type' => 'appointments',
                'attributes' => [
                    'date' => '2026-01-01',
                    'start_time' => '07:00',
                    'email' => 'falseemail@gmail.com'
                ],
            ]
        ]);

        $response = $this->postJson(route('api.v1.appointments.store'), [
            'data' => [
                'type' => 'appointments',
                'attributes' => [
                    'date' => '2026-01-01',
                    'start_time' => '16:00',
                    'email' => 'falseemail@gmail.com'
                ],
            ]
        ]);

        $response->assertJsonApiValidationErrors('start_time');
    }

    /** @test */
    public function appointments_may_only_last_an_hour()
    {
        /** Cualquier usuario que se cree tendrá los permisos necesarios
         * para la autenticacion de Sanctum
         */
        Sanctum::actingAs(User::factory()->create());

        $user = User::factory()->create();
        $category = Category::factory()->create();

        Appointment::create([
            'date' => '2026-01-01',
            'start_time' => '12:00',
            'email' => 'falseemail@gmail.com',
            'category_id' => $category->getRouteKey(),
            'user_id' => $user->getRouteKey()
        ]);

        $response = $this->postJson(route('api.v1.appointments.store'), [
            'data' => [
                'type' => 'appointments',
                'attributes' => [
                    'date' => '2026-01-01',
                    'start_time' => '12:00',
                    'email' => 'falseemail@gmail.com'
                ],
            ]
        ]);

        $response->assertJsonApiValidationErrors('start_time');

        $response2 = $this->postJson(route('api.v1.appointments.store'), [
            'data' => [
                'type' => 'appointments',
                'attributes' => [
                    'date' => '2026-01-01',
                    'start_time' => '11:35',
                    'email' => 'falseemail@gmail.com'
                ],
            ]
        ]);

        $response2->assertJsonApiValidationErrors('start_time');

        $response3 = $this->postJson(route('api.v1.appointments.store'), [
            'data' => [
                'type' => 'appointments',
                'attributes' => [
                    'date' => '2026-01-01',
                    'start_time' => '12:55',
                    'email' => 'falseemail@gmail.com'
                ],
            ]
        ]);

        $response3->assertJsonApiValidationErrors('start_time');

        $response4 = $this->postJson(route('api.v1.appointments.store'), [
            'data' => [
                'type' => 'appointments',
                'attributes' => [
                    'date' => '2026-01-01',
                    'start_time' => '14:05',
                    'email' => 'falseemail@gmail.com'
                ],
            ]
        ]);

        $response4->assertJsonApiValidationErrors('start_time');
    }

    /** @test */
    public function email_address_must_be_a_valid_email()
    {
        /** Cualquier usuario que se cree tendrá los permisos necesarios
         * para la autenticacion de Sanctum
         */
        Sanctum::actingAs(User::factory()->create());

        $response = $this->postJson(route('api.v1.appointments.store'), [
            'data' => [
                'type' => 'appointments',
                'attributes' => [
                    'date' => '2026-01-01',
                    'start_time' => '10:00',
                    'email' => 'wrongemail.com'
                ],
            ]
        ]);

        $response->assertJsonApiValidationErrors('email');
    }

    /** @test */
    public function category_relationship_is_required()
    {
        /** Cualquier usuario que se cree tendrá los permisos necesarios
         * para la autenticacion de Sanctum
         */
        Sanctum::actingAs(User::factory()->create());

        $response = $this->postJson(route('api.v1.appointments.store'), [
            'data' => [
                'type' => 'appointments',
                'attributes' => [
                    'date' => '2026-01-01',
                    'start_time' => '11:00',
                    'email' => 'falseemail@gmail.com'
                ],
            ]
        ]);

        $response->assertJsonApiValidationErrors('relationships.category');
    }

    /** @test */
    public function category_must_exist_in_database()
    {
        /** Cualquier usuario que se cree tendrá los permisos necesarios
         * para la autenticacion de Sanctum
         */
        Sanctum::actingAs(User::factory()->create());

        $response = $this->postJson(route('api.v1.appointments.store'),
            Document::type('appointments')
                ->attributes([
                    'date' => '2025-11-17',
                    'start_time' => '11:00',
                    'email' => 'falseemail@gmail.com'
                ])->relationshipsData([
                    'category' => Category::factory()->make()
                ])->toArray()
        );

        $response->assertJsonApiValidationErrors('data.relationships.category.data.id');
    }
}
