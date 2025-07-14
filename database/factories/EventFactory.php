<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Event>
 */
class EventFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
        'title' => $this->faker->sentence(3),
        'description' => $this->faker->paragraph(),
        'date' => $this->faker->dateTimeBetween('+1 week', '+1 month'),
        'capacity' => $this->faker->numberBetween(10, 100),
        'created_by' => \App\Models\User::inRandomOrder()->first()->id,
  
    ];
    }
}
