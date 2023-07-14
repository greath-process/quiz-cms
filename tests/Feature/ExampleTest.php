<?php

namespace Tests\Feature;

// use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use Airtable;
use App\Models\Course;
use App\Models\Prompt;
use App\Services\ChatGPT;
use App\Services\ExportAirTable;
use HtmlSanitizer\Extension\Listing\Node\DdNode;
use HtmlSanitizer\Extension\Listing\NodeVisitor\DdNodeVisitor;
use OpenAI\Resources\Chat;
use Termwind\Components\Dd;
use OpenAI\Laravel\Facades\OpenAI;


class ExampleTest extends TestCase
{
    /**
     * A basic test example.
     */
    public function test_the_application_returns_a_successful_response(): void
    {

        $prompt = Prompt::find(7);


        // $arrChat = [
        //     "model" => "gpt-3.5-turbo-0613",                                                                               vvv               cv
        //     "messages" => [
        //         [
        //             "role" => "system",
        //             "content" => "You are a quiz generator for fill-in-the-blank questions. You will be provided with a highlight and how many questions you should generate. IMPORTANT: Only create statements with blanks as {{BLANK}} and provide 3 to 5 answers each. Only return a JSON like this: [ {\"title\": \"statement\",\"answers\": [{\"title\": \"answer text\",\"correct\":true}]}, {\"title\": \"statement\",\"answers\": [{\"title\": \"answer text\",\"correct\":true}]}]"
        //         ],
        //         [
        //             "role" => "user",
        //             "content" => "Highlight:create of love :  Number of questions: j"
        //     ]
        //     ]
        // ];
        $create = OpenAI::chat();
        $result = $create->create(json_decode(trim(str_replace(["\n", '""'], "", $prompt->prompt)), TRUE));
        $result = trim($result['choices'][0]['message']['content']);
        $json_decode = json_decode(trim($result), true);

        dd($json_decode);
    }
}

