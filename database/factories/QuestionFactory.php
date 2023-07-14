<?php

namespace Database\Factories;

use App\Enums\QuestionType;
use App\Models\Card;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Question>
 */
class QuestionFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'type' => fake()->randomElement(QuestionType::values()),
            'text' => fake()->sentence(rand(3, 10)),
            'card_id' => Card::factory(),
        ];
    }
}
