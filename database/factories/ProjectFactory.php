<?php

namespace Database\Factories;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<\App\Models\Project>
 */
class ProjectFactory extends Factory
{
    public function definition(): array
    {
        $languages = ['php', 'javascript', 'python', 'typescript', 'rust'];
        $language  = fake()->randomElement($languages);

        return [
            'user_id'      => User::factory(),
            'name'         => fake()->words(3, true),
            'type'         => fake()->randomElement(['website', 'web_app', 'static_site', 'api']),
            'language'     => $language,
            'framework'    => null,
            'integrations' => [],
            'stack_config' => [],
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
