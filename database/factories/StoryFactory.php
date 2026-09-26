<?php

namespace Database\Factories;

use App\Models\Story;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Story>
 */
class StoryFactory extends Factory
{
    public function definition(): array
    {
        return [
            'user_id' => User::factory(),
            'title' => rtrim(fake()->sentence(6), '.'),
            'url' => fake()->unique()->url(),
            'text' => null,
        ];
    }

    public function ask(): static
    {
        return $this->state(fn () => [
            'title' => 'Ask: '.rtrim(fake()->sentence(5), '.').'?',
            'url' => null,
            'text' => fake()->paragraphs(2, true),
        ]);
    }
}
