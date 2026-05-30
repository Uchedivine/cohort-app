<?php

namespace Database\Factories;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class OrganizationFactory extends Factory
{
    public function definition(): array
    {
        $name = fake()->company();
        
        return [
            'user_id' => User::factory(),
            'name' => $name,
            'slug' => Str::slug($name) . '-' . Str::random(5),
            'short_description' => fake()->sentence(15),
            'full_profile' => fake()->paragraphs(3, true),
            'location' => fake()->city() . ', ' . fake()->country(),
            'thematic_focus' => fake()->randomElement(['Education', 'Health', 'Environment', 'Governance']),
            'website' => fake()->url(),
            'contact_email' => fake()->companyEmail(),
            'contact_phone' => fake()->phoneNumber(),
            'status' => 'published',
        ];
    }

    public function draft(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'draft',
        ]);
    }

    public function submitted(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'submitted',
        ]);
    }
}
