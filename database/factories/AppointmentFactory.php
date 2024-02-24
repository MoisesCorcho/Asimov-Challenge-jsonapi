<?php

namespace Database\Factories;

use App\Models\Category;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Appointment>
 */
class AppointmentFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'date' => $this->faker->date('Y-m-d'),
            'start_time' => $this->faker->time('H:i:s'),
            'email' => $this->faker->unique()->safeEmail,
            'category_id' => Category::factory()
        ];
    }
}
