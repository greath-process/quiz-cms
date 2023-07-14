<?php

namespace Database\Seeders;

use App\Enums\QuestionType;
use App\Models\Card;
use App\Models\Question;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class QuestionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $cards = Card::all();
        foreach($cards as $card)
        {
            Question::factory(rand(3, 10))->create([
                'card_id' => $card->id,
                'type' => QuestionType::CHOICE
            ]);

            Question::factory(rand(1,3))->create([
                'card_id' => $card->id,
                'type' => QuestionType::BLANK
            ]);

            Question::factory(rand(1,3))->create([
                'card_id' => $card->id,
                'type' => QuestionType::TRUEFALSE
            ]);
        }
    }
}
