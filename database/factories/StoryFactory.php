<?php

namespace Database\Factories;

use App\Models\Organization;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class StoryFactory extends Factory
{
    public function definition(): array
    {
        $title = fake()->sentence(6);
        
        return [
            'organization_id' => Organization::factory(),
            'user_id' => User::factory(),
            'title' => $title,
            'slug' => Str::slug($title) . '-' . Str::random(5),
            'summary' => fake()->paragraph(),
            'full_story' => fake()->paragraphs(5, true),
            'author' => fake()->name(),
            'problem' => fake()->paragraph(),
            'approach' => fake()->paragraph(),
            'outcome' => fake()->paragraph(),
            'lessons' => fake()->paragraph(),
            'status' => 'published',
            'published_at' => now(),
        ];
    }

    public function draft(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'draft',
            'published_at' => null,
        ]);
    }

    public function submitted(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'submitted',
            'published_at' => null,
        ]);
    }
}
