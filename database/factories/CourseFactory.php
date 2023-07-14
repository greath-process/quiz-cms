<?php

namespace Database\Factories;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Course>
 */
class CourseFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'title' => fake()->jobTitle(),
            'subtitle' => fake()->jobTitle(),
            'author'  => fake()->firstName() . ' ' . fake()->lastName(),
            'year' => fake()->year(),
            'summary' => fake()->realText(),
            'cover_color' => fake()->hexColor(),
            'archived' => fake()->boolean(20),
            'user_id' => User::factory(),
        ];
    }
}
