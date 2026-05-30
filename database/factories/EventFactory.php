<?php

namespace Database\Factories;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class EventFactory extends Factory
{
    public function definition(): array
    {
        $title = fake()->sentence(4);
        $startDate = fake()->dateTimeBetween('now', '+3 months');
        
        return [
            'user_id' => User::factory(),
            'title' => $title,
            'slug' => Str::slug($title) . '-' . Str::random(5),
            'description' => fake()->paragraph(),
            'start_date' => $startDate,
            'end_date' => fake()->dateTimeBetween($startDate, '+1 week'),
            'location' => fake()->city(),
            'status' => 'published',
            'published_at' => now(),
        ];
    }

    public function upcoming(): static
    {
        return $this->state(fn (array $attributes) => [
            'start_date' => fake()->dateTimeBetween('now', '+3 months'),
        ]);
    }

    public function past(): static
    {
        return $this->state(fn (array $attributes) => [
            'start_date' => fake()->dateTimeBetween('-3 months', 'now'),
        ]);
    }
}
