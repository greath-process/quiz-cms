<?php

namespace Database\Seeders;

use App\Enums\QuestionType;
use App\Models\Answer;
use App\Models\Question;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class AnswerSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $questions = Question::all();
        foreach($questions as $question)
        {
            switch($question->getType())
            {
                case QuestionType::CHOICE:
                    Answer::factory(3)->create([
                        'question_id' => $question->id,
                    ]);
                    Answer::factory(1)->create([
                        'question_id' => $question->id,
                        'correct' => true
                    ]);
                    break;
                case QuestionType::BLANK:
                    Answer::factory(3)->create([
                        'question_id' => $question->id,
                        'text' => fake()->word(),
                        'correct' => false
                    ]);
                    Answer::factory(1)->create([
                        'question_id' => $question->id,
                        'text' => fake()->word(),
                        'correct' => true
                    ]);
                    break;
                case QuestionType::TRUEFALSE:
                    Answer::factory()->create([
                        'question_id' => $question->id,
                        'correct' => fake()->boolean()
                    ]);
            }
        }
    }
}
