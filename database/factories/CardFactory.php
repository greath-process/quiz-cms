<?php

namespace Database\Factories;

use App\Enums\CreationType;
use App\Models\Course;
use App\Models\Lesson;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Card>
 */
class CardFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'summary' => fake()->realTextBetween(50, 200),
            'intro_card' => fake()->boolean(10),
            'position' => rand(1, 10),
            'page_number' => rand(1, 500),
            'course_id' => Course::factory(),
            'lesson_id' => Lesson::factory(),
        ];
    }
}
