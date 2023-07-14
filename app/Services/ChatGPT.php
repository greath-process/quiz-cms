<?php

namespace App\Services;

use App\Models\Prompt;
use OpenAI\Laravel\Facades\OpenAI;
use Illuminate\Support\Facades\Log;


class ChatGPT
{
    public function getQuizByChat(array $data): string
    {
        Log::info("\n\n\n\n####################### INFO: Quiz generation started (" . Prompt::find($data['prompt'])->name . " (chat completion))");
        $number_of_questions = intval($data['number_of_questions']);
        $content_user = "Highlight:" . $data['text'] . " Number of questions:" . $number_of_questions;
        $promptId = $data['prompt'];
        if ($promptId) {
            $promptEl = Prompt::find($promptId);
            $arrChat = json_decode(trim(str_replace(["\n", '""'], "", $promptEl->prompt)), TRUE);
            $arrChat['messages'][1]['content'] = $content_user;
        } else {
            $arrChat = [
                'model' => 'gpt-3.5-turbo-0613',
                'messages' => [
                    [
                        "role" => "system",
                        "content" => "You are a quiz generator for fill-in-the-blank questions. You will be provided with a highlight and how many questions you should generate. IMPORTANT: Only create statements with blanks as {{BLANK}} and provide 3 to 5 answers each. Only return a JSON like this: [ {\"title\": \"statement\",\"answers\": [{\"title\": \"answer text\",\"correct\":true}]}, {\"title\": \"statement\",\"answers\": [{\"title\": \"answer text\",\"correct\":true}]}]"
                    ],
                    [
                        "role" => "user",
                        "content" => $content_user
                    ]
                ]
            ];
        }
        $create = OpenAI::chat();
        $result = $create->create($arrChat);
        $result = trim($result['choices'][0]['message']['content']);
        Log::info("RESULT: " . $result);
        return $result;
    }
    public function getQuizesByText(array $params): string
    {
        Log::info("\n\n\n\n####################### INFO: Quiz generation started (" . Prompt::find($params['PROMPT'])->name . " (completion))");
        $promptId = $params['PROMPT'];
        unset($params['PROMPT']);

        if ($promptId) {
            $promptEl = Prompt::find($promptId);
            if ($promptEl->getQuizType()) {
                $params['FORMAT'] = $promptEl->getQuizType();
            }
            $arrPrompt = json_decode(trim(str_replace(["\n", '""'], "", $promptEl->prompt)), TRUE);

            if ($arrPrompt == null) {
                $arrPrompt = [
                    'model' => 'text-davinci-003',
                    'prompt' => $promptEl->prompt,
                    'max_tokens' => 4097 - strlen($promptEl->prompt)
                ];
            }
            foreach ($params as $param => $value) {
                if ($param == 'SUMMARY') $value = "\n" . $value;
                $arrPrompt['prompt'] = str_replace('{' . $param . '}', $value, $arrPrompt['prompt']);
            }
            $arrPrompt['prompt'] .= "\n\n" . config('openai.end_quiz_prompt');
            Log::info("PROMPT: \n" . $arrPrompt['prompt']);
        } else {
            $prompt = config('openai.quiz_prompt');

            foreach ($params as $param => $value) {
                $prompt = str_replace('{' . $param . '}', $value, $prompt);
            }

            $arrPrompt = [
                'model' => 'text-davinci-003',
                'prompt' => $prompt,
                'max_tokens' => 4097 - strlen($prompt)
            ];
        }
        // Log::info( $arrPrompt);
        $result = "";
        while ($result === "")
            $result = OpenAI::completions()->create($arrPrompt);
        Log::info("RESULT: " . $result['choices'][0]['text']);

        return $result['choices'][0]['text'];
    }

    public function fixTheIncorrect(string $brokenCode, string $from_were): string
    {
        Log::info("INFO: Fixing started from :" . $from_were . "\n and this the value :" . $brokenCode);
        $prompt = config('openai.fix_json');
        $prompt = str_replace('{JSON}', $brokenCode, $prompt);

        $result = OpenAI::completions()->create([
            'model' => 'text-davinci-003',
            'prompt' => $prompt,
            'max_tokens' => 4097 - strlen($prompt)
        ]);

        Log::info("FIXED RESULT: " . $result['choices'][0]['text']);

        return $result['choices'][0]['text'];
    }
    public function fixTheSummary(string $brokenSummary): string
    {
        $prompt = config('openai.fix_summary');
        Log::info("INFO: Fixing started Summary :" . $brokenSummary);
        $result = OpenAI::completions()->create([
            'model' => 'text-davinci-003',
            'prompt' => $prompt,
            'max_tokens' => 4097 - strlen($prompt)
        ]);
        Log::info("FIXED RESULT: " . $result['choices'][0]['text']);
        return $result['choices'][0]['text'];
    }
}
