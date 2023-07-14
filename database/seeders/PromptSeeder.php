<?php

namespace Database\Seeders;

use App\Models\Prompt;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class PromptSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Prompt::insert(
            [
                [
                    'name' => 'Konrad V2 - TRUEFALSE',
                    'description' => 'Konrad V2 - TRUEFALSE',
                    'model_type' => 2,
                    'prompt' => '{
                    "model": "text-davinci-003",
                    "prompt": "You are a quiz generator. Create {NUM_QUESTION} {DIFFICULTY} true/false question(s) of the following highlight. Leave the answer empty and set \"correct\" as true or false.\n\nHighlight: {SUMMARY}\n\nImportant: only return a JSON like this: [{\"title\": \"statement\",\"answers\": [{\"title\": \"\",\"correct\":true}]}]",
                    "temperature": 0.3,
                    "max_tokens": 256,
                    "top_p": 1,
                    "frequency_penalty": 0,
                    "presence_penalty": 0
                  }',

                ],
                [
                    'name' => 'Konrad V2 - BLANK',
                    'description' => 'Konrad V2 - BLANK',
                    'model_type' => 2,
                    'prompt' => '{
                    "model": "text-davinci-003",
                    "prompt": "You are a quiz generator. Create {NUM_QUESTION} {DIFFICULTY} fill-in-the-blank question(s) of the following highlight. Create statements with blanks as ' . "{{BLANK}}" . ' and provide 3 to 5 answers.\n\nHighlight: {SUMMARY}\n\nImportant: only return a JSON like this: [{\"title\": \"statement\",\"answers\": [{\"title\": \"answer text\",\"correct\":true}]}]",
                    "temperature": 0.3,
                    "max_tokens": 256,
                    "top_p": 1,
                    "frequency_penalty": 0,
                    "presence_penalty": 0
                  }',
                ],
                [
                    'name' => 'Konrad V2 - CHOICE',
                    'description' => 'Konrad V2 - CHOICE',
                    'model_type' => 2,
                    'prompt' => '{
                    "model": "text-davinci-003",
                    "prompt": "You are a quiz generator. Create {NUM_QUESTION} {DIFFICULTY} multiple-choice question(s) of the following highlight:\n\nHighlight: {SUMMARY}\n\nIMPORTANT: only return a JSON like this: [{\"title\": \"question text\",\"answers\": [{\"title\": \"answer text\",\"correct\":true}]}]",
                    "temperature": 0.3,
                    "max_tokens": 256,
                    "top_p": 1,
                    "frequency_penalty": 0,
                    "presence_penalty": 0
                  }',
                ]
            ]
        );
    }
}
