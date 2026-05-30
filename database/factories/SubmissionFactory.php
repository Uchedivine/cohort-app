<?php

namespace Database\Factories;

use App\Models\Story;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class SubmissionFactory extends Factory
{
    public function definition(): array
    {
        return [
            'submittable_type' => Story::class,
            'submittable_id' => Story::factory(),
            'submitted_by' => User::factory(),
            'status' => 'submitted',
            'submitted_at' => now(),
        ];
    }

    public function draft(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'draft',
            'submitted_at' => null,
        ]);
    }

    public function approved(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'approved',
            'reviewer_id' => User::factory(),
            'reviewed_at' => now(),
        ]);
    }

    public function rejected(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'rejected',
            'reviewer_id' => User::factory(),
            'reviewer_notes' => fake()->sentence(),
            'reviewed_at' => now(),
        ]);
    }
}
