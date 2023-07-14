<?php

namespace App\Http\Livewire;

use App\Enums\CreationType;
use App\Enums\QuestionType;
use App\Models\Answer;
use App\Models\Card;
use App\Models\Question;
use App\Services\ChatGPT;
use App\Services\ExportCSV;
use App\Services\Helpers;
use App\Services\ImageGenerate;
use Exception;
use Livewire\Component;

class CardsCreate extends Component
{
    public string $chapter = '';
    public string $page_position = '';
    public string $highlight = '';
    public string $questionType = '';
    public string $difficult = '';
    public string $numQuestions = '';
    public array $questionTypes = [];
    public array $difficultTypes = [];
    public bool $error = false;
    public array $questions = [];
    public array $alphabet = [];
    public bool $loading = false;


    public function mount() {
        $this->questionTypes = ['mix', 'multiple choice', 'true or false', 'blank'];
        $this->difficultTypes = ['easy', 'medium', 'hard'];
        $this->questionType = current($this->questionTypes);
        $this->difficult = current($this->difficultTypes);
        $this->numQuestions = 3;
        $this->alphabet = range('A', 'Z');
    }

    public function generate() {
        if($this->validateFields()) {
            $this->loading = true;
            $params = [
                'NUM_QUESTION' => $this->numQuestions,
                'FORMAT' => $this->questionType == 'mix'
                    ? 'different for each question: one for \'multiple choice\', one for \'true or false\' one for \'blank\' etc.;'
                    : $this->questionType,
                'DIFFICULTY' => $this->difficult,
                'SUMMARY' => $this->highlight
            ];
            /* temporary test construct for expired keys */
            try {
                $response = (new ChatGPT())->getQuizesByText($params);
            } catch (Exception $e) {
                $response = json_encode($this->getTestQuestions());
            }

            $this->createCards(trim($response));
        }
    }

    public function validateFields() {
        if ($this->chapter && $this->page_position && $this->highlight) {
            $this->error = false;
            return true;
        }
        $this->error = true;
        return false;
    }

    public function isMix($i) {
        return !($this->questionType != 'mix' || $this->questionType == 'mix' && $i > 2);
    }

    public function updatedQuestionType($value)
    {
        if($value == 'mix' && $this->numQuestions < 3)
            $this->numQuestions = 3;
    }

    public function clear() {
        $this->chapter = '';
        $this->page_position = '';
        $this->highlight = '';
        $this->questionType = current($this->questionTypes);
        $this->difficult = current($this->difficultTypes);
        $this->numQuestions = 3;
    }

    public function getRightAnswer(int $key, string $title = ''): string
    {
        if(isset($this->questions[$key])) {
            $correctAnswer = array_filter($this->questions[$key]['answers'], function($answer) {
                return $answer['correct'] === true;
            });

            $title = array_column($correctAnswer, 'title')[0];
        }

        return $title;
    }

    public function setRightAnswer(int $key, int $key_a): void
    {
        if(isset($this->questions[$key]) && isset($this->questions[$key]['answers'][$key_a]) ) {
            foreach ($this->questions[$key]['answers'] as $k => $answer)
                $this->questions[$key]['answers'][$k]['correct'] = $key_a == $k;
        }
    }

    public function setBool(int $key, string $param, bool $bool): void
    {
        if(isset($this->questions[$key])) {
            $this->questions[$key][$param] = $bool;
        }
    }

    public function removeQuestion(int $key): void
    {
        if(isset($this->questions[$key])) {
            Question::destroy($this->questions[$key]['id']);
            unset($this->questions[$key]);
        }
    }

    /**
     * @throws \Exception
     */
    public function createCards(string $text): void
    {
        $NewQuestions = [];

        $start = strpos($text, "[{");
        $end = strrpos($text, "}]");
        if ($start !== false && $end !== false) {
            $json_string = substr($text, $start, $end - $start + 2);
            if(!json_decode($json_string)) {
                $response = (new ChatGPT())->fixTheIncorrect($json_string, 'from create card');
                $this->createCards($response);
            } else {
                $NewQuestions = json_decode($json_string, true);
            }
        } else {
            $this->generate();
        }

        $card = Card::create([
            'summary' => $this->highlight,
            'page_number' => $this->page_position,
            'position' => $this->chapter,
            'creation_type' => CreationType::HIGHLIGHT,
        ]);

        foreach ($NewQuestions as $key => $question){
            $type_str = (new ExportCSV)->getType($question);
            $type = QuestionType::fromName($type_str);

            $newQuestion = Question::create([
                'type' => $type,
                'text' => $question['title'],
                'card_id' => $card->id,
            ]);

            if (isset($question['answers'])) {
                foreach ($question['answers'] as $k => $answer){
                    $nsw = Answer::create([
                        'text' => $answer['title'],
                        'correct' => $answer['correct'],
                        'question_id' => $newQuestion->id,
                    ]);
                    $NewQuestions[$key]['answers'][$k]['id'] = $nsw->id;
                }
            } else {
                $NewQuestions[$key]['answers'] = [];
            }

            $NewQuestions[$key]['id'] = $newQuestion->id;
            $NewQuestions[$key]['order'] = 100;
            $NewQuestions[$key]['type'] = $this->questionType;
            $NewQuestions[$key]['info'] = '';
            $NewQuestions[$key]['edit'] = false;
            $NewQuestions[$key]['check'] = false;
            $NewQuestions[$key]['delete'] = false;
            $NewQuestions[$key]['answer'] = '';
            $NewQuestions[$key]['summary'] = $this->highlight;
        }
        $this->loading = false;
        $this->questions = $this->questions + $NewQuestions;
    }

    public function exportCSV() {
        return (new ExportCSV())->exportToFile($this->questions);
    }

    public function editQuestion(int $key): void
    {
        if(isset($this->questions[$key])) {
            $question = $this->questions[$key];
             Question::find($question['id'])->update([
                'text' => $question['title'],
            ]);

            foreach ($question['answers'] as $answer){
                Answer::find($answer['id'])->update([
                    'text' => $answer['title'],
                    'correct' => $answer['correct'],
                ]);
            }
        }

        $this->setBool($key, 'edit', false);
    }

    public function render()
    {
        return view('livewire.cards-create');
    }







    /* temporary test construct for expired keys */
    public function getTestQuestions(): array {
        return [
            [
                'id' => 226470,
                'order' => 100,
                'title' => 'What caused the protagonist to lose consciousness?',
                'type' => 'multiple_choice',
                'answers' => [
                    [
                        'id' => 5,
                        'title' => 'A seizure',
                        'correct' => true,
                    ],
                    [
                        'id' => 6,
                        'title' => 'A stroke',
                        'correct' => false,
                    ],
                    [
                        'id' => 3,
                        'title' => 'A panic attack',
                        'correct' => false,
                    ],
                    [
                        'id' => 4,
                        'title' => 'A heart attack',
                        'correct' => false,
                    ],
                ],
                'info' => 'The protagonist had their first seizure of the day and then stopped breathing entirely, leading to their hospitalization.',
                'edit' => false,
                'check' => false,
                'delete' => false,
                'answer' => '',
                'summary' => 'test',
            ],
            [
                'id' => 226555,
                'order' => 100,
                'title' => 'True or false: The protagonist\'s sister had leukemia when she was three years old',
                'type' => 'true_false',
                'answers' => [
                    [
                        'id' => 1,
                        'title' => 'True',
                        'correct' => true,
                    ],
                    [
                        'id' => 2,
                        'title' => 'False',
                        'correct' => false,
                    ],
                ],
                'info' => '',
                'edit' => false,
                'check' => false,
                'delete' => false,
                'answer' => '',
                'summary' => 'test',
            ],
            [
                'id' => 228555,
                'order' => 100,
                'title' => 'After my first seizure of the day, my body struggled with basic functions like ______ and breathing.',
                'type' => 'short_answer',
                'answers' => [
                    [
                        'id' => 7,
                        'title' => 'seeing',
                        'correct' => true,
                    ],
                ],
                'info' => '',
                'edit' => false,
                'check' => false,
                'delete' => false,
                'answer' => '',
                'summary' => 'test',
            ],
        ];
    }
}
