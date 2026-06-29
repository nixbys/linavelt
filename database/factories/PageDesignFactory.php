<?php

namespace Database\Factories;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<\App\Models\PageDesign>
 */
class PageDesignFactory extends Factory
{
    public function definition(): array
    {
        return [
            'user_id'      => User::factory(),
            'name'         => fake()->words(3, true),
            'status'       => 'draft',
            'project_data' => null,
            'html'         => null,
            'css'          => null,
            'published_at' => null,
        ];
    }

    public function published(): static
    {
        return $this->state(fn (array $attributes) => [
            'status'       => 'published',
            'published_at' => now(),
        ]);
    }
}
