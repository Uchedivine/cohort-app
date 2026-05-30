<?php

namespace Database\Factories;

use App\Models\Organization;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class ResourceFactory extends Factory
{
    public function definition(): array
    {
        $title = fake()->sentence(4);
        
        return [
            'organization_id' => Organization::factory(),
            'user_id' => User::factory(),
            'title' => $title,
            'slug' => Str::slug($title) . '-' . Str::random(5),
            'description' => fake()->paragraph(),
            'resource_type' => fake()->randomElement(['file', 'external_link', 'video_link']),
            'theme' => fake()->randomElement(['education', 'health', 'environment', 'governance']),
            'year' => fake()->year(),
            'status' => 'published',
            'published_at' => now(),
        ];
    }

    public function file(): static
    {
        return $this->state(fn (array $attributes) => [
            'resource_type' => 'file',
            'file_path' => 'resources/sample.pdf',
            'mime_type' => 'application/pdf',
            'file_size' => 1024000,
        ]);
    }

    public function link(): static
    {
        return $this->state(fn (array $attributes) => [
            'resource_type' => 'external_link',
            'external_url' => fake()->url(),
        ]);
    }
}
