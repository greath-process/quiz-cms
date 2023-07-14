<?php

return [

    /*
    |--------------------------------------------------------------------------
    | OpenAI API Key and Organization
    |--------------------------------------------------------------------------
    |
    | Here you may specify your OpenAI API Key and organization. This will be
    | used to authenticate with the OpenAI API - you can find your API key
    | and organization on your OpenAI dashboard, at https://openai.com.
    */

    'api_key' => env('OPENAI_API_KEY'),
    'organization' => env('OPENAI_ORGANIZATION'),

    'quiz_prompt' => 'Take the text below and generate from it {NUM_QUESTION} questions in "{FORMAT}" format with difficulty level "{DIFFICULTY}" and return all this to me only in JSON format [{"title":"text of the question","answers":{"title":"answer text","correct":true},},]. Text: {SUMMARY}',
    'fix_json' => 'Fix this JSON and return to me only fixed JSON: {JSON}',
    'fix_summary' => 'Fix that Summary by using the same meaning and return to me only fixed summary : {SUMMARY}',
    'end_quiz_prompt' => '',
];
