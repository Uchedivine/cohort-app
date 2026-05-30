<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class TagFactory extends Factory
{
    public function definition(): array
    {
        $name = fake()->word();
        
        return [
            'name' => $name,
            'slug' => Str::slug($name),
            'type' => fake()->randomElement(['general', 'sdg', 'thematic']),
        ];
    }

    public function sdg(): static
    {
        return $this->state(fn (array $attributes) => [
            'type' => 'sdg',
        ]);
    }

    public function thematic(): static
    {
        return $this->state(fn (array $attributes) => [
            'type' => 'thematic',
        ]);
    }
}
