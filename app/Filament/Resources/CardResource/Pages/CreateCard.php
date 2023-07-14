<?php

namespace App\Filament\Resources\CardResource\Pages;

use App\Enums\CreationType;
use App\Enums\QuestionType;
use App\Filament\Resources\CardResource;
use App\Models\Answer;
use App\Models\Card;
use App\Models\Course;
use App\Models\Prompt;
use App\Models\Question;
use App\Services\ChatGPT;
use Filament\Pages\Actions\Action;
use Carbon\Carbon;
use Closure;
use Filament\Resources\Form;
use Filament\Forms;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Spatie\Fork\Fork;
use App\Forms\Components\BlankInsert;

class CreateCards extends CreateRecord
{
    protected static string $resource = CardResource::class;

    protected static string $view = 'filament.resources.card-create.pages.create';

    protected static bool $isLoad = false;
    protected static $prompt = '';
    protected static array $types = [
        'true or false' => 'true or false',
        'blank' => 'blank',
        'multiple choice' => 'multiple choice',
        'mix' => 'mix'
    ];


    protected static array $difficulty = [
        'easy' => 'easy',
        'medium' => 'medium',
        'hard' => 'hard'
    ];
    public $tab = 1;
    public static int $course = 0;
    public static int $card_id = 0;
    public static bool $isCreate = false;

    public function __construct($id = null)
    {
        self::$isCreate = request()->input('updates.0.payload.params.0') == 'create';

        parent::__construct($id);
    }
    public function form(Form $form): Form
    {

        if (
            str_contains(url()->previous(), 'courses')
            || str_contains($this->previousUrl, 'courses')
        ) {
            if ((gettype($this->record) == 'integer' || gettype($this->record) == 'string')) {
                self::$course = intval($this->record);
            }
        } else {
            $card = null;
            if ((gettype($this->record) == 'integer' || gettype($this->record) == 'string'))
                $card = Card::find(intval($this->record));
            self::$course = $card ?  $card->course_id : 1;
        }
        $tabs = Forms\Components\Tabs::make('Card')
            ->activeTab($this->tab)
            ->tabs([
                Forms\Components\Tabs\Tab::make('From highlight')
                    ->schema([
                        Forms\Components\TextInput::make('chapter_highlight')
                            ->columnSpanFull()
                            ->hint('Can be a number or a title')
                            ->columnSpan([
                                'md' => 6,
                            ])
                            ->dehydrated(),
                        Forms\Components\TextInput::make('page_number')
                            ->columnSpanFull()
                            ->numeric()
                            ->columnSpan([
                                'md' => 6,
                            ])
                            ->dehydrated(),
                        \Wiebenieuwenhuis\FilamentCharCounter\Textarea::make('highlight')
                            ->label('Summary (Excerpt)')
                            ->characterLimit(280)
                            ->columnSpanFull()
                            ->required(
                                fn () => $this->tab == 1
                            )
                            ->dehydrated(),
                        Forms\Components\Card::make()
                            ->schema([
                                Forms\Components\Select::make('prompt')
                                    ->label('AI Prompt')
                                    ->options(function (Closure $get) {
                                        $prompts = Prompt::orderBy('sort_order', 'asc')->get();
                                        return $prompts->pluck('name', 'id');
                                    })
                                    ->columnSpan([
                                        'md' => 4,
                                    ])
                                    ->reactive()
                                    ->default(function (Closure $get) {
                                        $prompts = Prompt::orderBy('sort_order', 'asc')->get();
                                        return $prompts->first()?->id;
                                    })
                                    ->dehydrated(),
                                Forms\Components\Select::make('number_of_questions')
                                    ->options(range(1, 12))
                                    ->columnSpan([
                                        'md' => 4,
                                    ])
                                    ->default(1)
                                    ->dehydrated(),
                                Forms\Components\Select::make('difficulty')
                                    ->options(self::$difficulty)
                                    ->default('medium')
                                    ->columnSpan([
                                        'md' => 4,
                                    ])
                                    ->dehydrated(),

                            ])
                            ->columns([
                                'md' => 12,
                            ]),
                    ])
                    ->columns([
                        'md' => 12,
                    ]),
                Forms\Components\Tabs\Tab::make('From text')
                    //    ->hidden(!self::$isCreate)
                    ->schema([
                        Forms\Components\TextInput::make('chapter')
                            ->hint('Can be a number or a title'),
                        Forms\Components\Textarea::make('Text')
                            ->maxLength(25000)
                            ->dehydrated(),
                        Forms\Components\Card::make()
                            ->schema([
                                Forms\Components\Select::make('generate_question_type')
                                    ->options(self::$types)
                                    ->default('multiple choice')
                                    ->columnSpan([
                                        'md' => 4,
                                    ])
                                    ->dehydrated(),
                                Forms\Components\Select::make('number_of_questions')
                                    ->options(range(1, 12))
                                    ->columnSpan([
                                        'md' => 4,
                                    ])
                                    ->dehydrated(),
                                Forms\Components\Select::make('difficulty')
                                    ->options(self::$difficulty)
                                    ->default('medium')
                                    ->columnSpan([
                                        'md' => 4,
                                    ])
                                    ->dehydrated(),
                            ])
                            ->columns([
                                'md' => 12,
                            ]),
                    ]),
                Forms\Components\Tabs\Tab::make('Manual')
                    ->schema([
                        Forms\Components\TextInput::make('creation_type')
                            ->hidden()
                            ->default(CreationType::MANUAL),
                        Forms\Components\Card::make()
                            ->schema([
                                Forms\Components\Select::make('course_id')
                                    ->disabled()
                                    ->relationship('course', 'title')
                                    ->columnSpanFull()
                                    ->default(self::$course)
                                    ->inlineLabel(),
                                Forms\Components\Select::make('lesson_id')
                                    ->relationship('lesson', 'title')
                                    ->hidden(auth()->user()->hasRole('Guest Quizmaster'))
                                    ->columnSpanFull()
                                    ->inlineLabel(),
                                Forms\Components\TextInput::make('chapter')
                                    ->hint('Can be a number or a title')
                                    ->columnSpanFull()
                                    ->inlineLabel(),
                                Forms\Components\TextInput::make('page_number')
                                    ->columnSpanFull()
                                    ->numeric()
                                    ->inlineLabel(),
                                Forms\Components\Toggle::make('intro_card')
                                    ->hidden(auth()->user()->hasRole('Guest Quizmaster'))
                                    ->columnSpanFull()
                                    ->inlineLabel()
                                    ->default(false),
                                Forms\Components\Textarea::make('source_text')
                                    ->label("Context")
                                    ->hint('You may paste any context your quiz question is based on, e.g. a whole paragraph or page.')
                                    ->columnSpanFull()
                                    ->inlineLabel(),
                                Forms\Components\MarkdownEditor::make('summary')
                                    ->label("Question summary")
                                    ->hint('A short excerpt from the content the question is based on. Highlight the correct answer by formatting it bold.')
                                    ->columnSpanFull()
                                    ->inlineLabel()
                                    ->maxLength(255)
                                    ->toolbarButtons([
                                        'bold',
                                        'bulletList',
                                        'edit',
                                        'italic',
                                        'preview'
                                    ]),
                            ])
                            ->columns([
                                'md' => 12,
                            ]),
                        Forms\Components\Repeater::make('questions')
                            ->relationship()
                            ->schema([
                                Forms\Components\Select::make('type')
                                    ->options(QuestionType::array())
                                    ->label('Question Type')
                                    ->hint('For True/False only set first answer to correct or incorrect')
                                    ->columnSpanFull()
                                    ->inlineLabel(),
                                BlankInsert::make('text')
                                    ->characterLimit(140)
                                    ->label('Question (or statement)')
                                    ->hint('For BLANK statements add <code>{{BLANK}}</code> where the blank(s) is/are placed.')
                                    ->columnSpanFull()
                                    ->inlineLabel(),
                                Forms\Components\Grid::make()
                                    ->schema([
                                        Forms\Components\Repeater::make('answers')
                                            ->createItemButtonLabel('Add answer')
                                            ->extraAttributes(['class' => 'answer-repeater'])
                                            ->hint('Set toggle to blue for correct answer')
                                            ->relationship()
                                            ->schema([
                                                Forms\Components\TextInput::make('text')
                                                    ->disableLabel()
                                                    ->columnSpan([
                                                        'md' => 10,
                                                    ]),
                                                Forms\Components\Toggle::make('correct')
                                                    ->disableLabel()
                                                    ->default(false),
                                            ])
                                            ->columns([
                                                'md' => 12,
                                            ])
                                            ->columnSpanFull()
                                            ->defaultItems(4)
                                            ->maxItems(8),
                                    ]),
                            ])
                            ->columnSpanFull()
                            ->hint('<span class="text-red-500 font-bold">Only the first question will be used for this card\'s quiz.</span>')
                            ->createItemButtonLabel('Add question alternative')
                            ->defaultItems(1),
                    ]),
            ])
            ->columnSpanFull();

        $form->schema([
            $tabs
        ]);
        return $form;
    }

    public function checkAITabs($get): bool
    {
        if ($this->tab == 3 || self::$isLoad) return true;
        $extraFields = $this->tab == 1 ? ['Text', 'difficulty'] : ['highlight', 'prompt'];
        $data = [
            'number_of_questions' => intval($get('number_of_questions')) + 1,
            'generate_question_type' => 'multiple choice',
            'difficulty' => $get('difficulty'),
            'text' => $this->tab == 1 ? $get('highlight') : $get('Text'),
            'chapter' => $this->tab == 1 ? $get('chapter_highlight') : $get('chapter'),
            'type' => $this->tab == 1 ? 'highlight' : 'text',
            'page_number' => $get('page_number') ?: '1'
        ];
        if (self::$course && !$data['text']) $data['text'] = $get('Text');
        foreach ($data as $attribute => $value) {
            if ($attribute != 'chapter') {
                if (!$value && !in_array($attribute, $extraFields)) {
                    return false;
                }
            }
        }
        self::$isLoad = true;
        $data['prompt'] = $get('prompt');
        if ($prompts = Prompt::find($data['prompt'])) {
            if ($prompts->quiz_type == 'mix' || $prompts->quiz_type == null) {
                self::$prompt = 4;
            } else {
                self::$prompt = array_search($prompts->quiz_type, array_keys(self::$types)) + 1 ?: 4;
            }

            if ($prompts->model_type == 1) {
                self::generateByChat($data);
            } else {
                self::generate($data);
            }
        }
        return false;
    }
    protected function getBreadcrumbs(): array
    {

        if (
            str_contains(url()->previous(), 'courses')
            || str_contains($this->previousUrl, 'courses')
        ) {
            if ((gettype($this->record) == 'integer' || gettype($this->record) == 'string')) {
                self::$course = intval($this->record);
            }
        } else {
            $card = null;
            if ((gettype($this->record) == 'integer' || gettype($this->record) == 'string'))
                $card = Card::find(intval($this->record));
            self::$course = $card ?  $card->course_id : 1;
        }
        $courses = Course::find(self::$course);
        return [
            url('/admin/courses') => 'Courses',
            url('/admin/courses/' . $courses?->id . '/edit') => $courses?->title,
            0 => 'Create'
        ];
    }
    public static function generateByChat($data): void
    {
        $create = (new ChatGPT())->getQuizByChat($data);
        self::createCards($create, $data);
    }
    public static function generate($data): void
    {
        if (self::validateFields($data)) {
            $number_of_questions = intval($data['number_of_questions']);
            if (self::$prompt == 4) {
                $responses = [];
                [$result, $result2, $result3] = Fork::new()
                    ->before(fn () => DB::connection('mysql')->reconnect())
                    ->run(
                        function () use (&$data, &$number_of_questions) {
                            $result = (new ChatGPT())->getQuizesByText([
                                'NUM_QUESTION' => $number_of_questions,
                                'FORMAT' => 'blank',
                                'DIFFICULTY' => $data['difficulty'],
                                'SUMMARY' => $data['text'],
                                'PROMPT' => 1,
                            ]);
                            return $result;
                        },
                        function () use (&$data, &$number_of_questions) {
                            $result = (new ChatGPT())->getQuizesByText([
                                'NUM_QUESTION' => $number_of_questions,
                                'FORMAT' => 'true or false',
                                'DIFFICULTY' => $data['difficulty'],
                                'SUMMARY' => $data['text'],
                                'PROMPT' => 2,
                            ]);

                            return $result;
                        },
                        function () use (&$data, &$number_of_questions) {
                            $results = '';
                            $result = (new ChatGPT())->getQuizesByText([
                                'NUM_QUESTION' => $number_of_questions,
                                'FORMAT' => 'multiple choice',
                                'DIFFICULTY' => $data['difficulty'],
                                'SUMMARY' => $data['text'],
                                'PROMPT' => 3,
                            ]);

                            return $result;
                        },
                    );

                $response[] = trim($result);
                $response[] = trim($result2);
                $response[] = trim($result3);
                self::createCardsMix($response, $data);
            } else {
                $params = [
                    'NUM_QUESTION' => self::$prompt == 4 ? 3 : intval($data['number_of_questions']),
                    'FORMAT' => $data['generate_question_type'],
                    'DIFFICULTY' => $data['difficulty'],
                    'SUMMARY' => $data['text'],
                    'PROMPT' => $data['prompt'],
                ];
                $response = (new ChatGPT())->getQuizesByText($params);
                $response = trim($response);
                self::createCards($response, $data);
            }
        }
    }
    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $this->checkAITabs(function ($key) use ($data) {
            return $data[$key];
        });
        return $data;
    }
    protected function getFormActions(): array
    {
        return array_merge(
            [$this->getCreateFormAction()],
            [$this->getCancelFormAction()],
        );
    }
    protected function handleRecordCreation(array $data): Model
    {
        if (self::$card_id)
            $dd = Card::find(self::$card_id);
        else {
            $dd = static::getModel()::create($data);
            self::$card_id = $dd->id;
        }
        return $dd;
    }
    public static function validateFields($data): bool
    {
        return $data['text'];
    }

    protected function getRedirectUrl(): string
    {
        return '/admin/cards/' .  self::$card_id . '/edit';
    }
    public static function createCardsMix($text, array $data)
    {
        $NewQuestions = [];
        foreach ($text as $key => $value) {
            $start = strpos($value, "[{");
            $end = strrpos($value, "}]");
            $json_string = substr($value, $start, $end - $start + 2);
            if ($start !== false && $end !== false) {
                if (!json_decode(trim($json_string))) {
                    $response = (new ChatGPT())->fixTheIncorrect($json_string, 'from createMix');
                    $text[$key] = $response;
                    self::createCardsMix($text, $data);
                    return false;
                } else {
                    $NewQuestions[] = json_decode($json_string, true);
                }
            } else {
                self::generate($data);
                return false;
            }
        }
        $text = $data['text'];
        $creation_type = $data['type'] == 'highlight' ? CreationType::HIGHLIGHT : CreationType::TEXT;
        $rule = false;
        if ($last_card_in_database = Card::orderBy('id', 'desc')->first()) {
            $rule = Carbon::now()->diffInSeconds($last_card_in_database->created_at) < 15;
        }
        if (!$rule) {
            $card = Card::create([
                'summary' => $text,
                'position' => 1,
                'creation_type' => $creation_type,
                'chapter' => $data['chapter'] ?? null,
                'page_number' => $data['page_number'] ?? '1'
            ]);
            if (self::$course) {
                $card->course_id = self::$course;
                $card->save();
            }
            $prompt = 1;
            foreach ($NewQuestions as $key => $question) {
                if ($prompt == 4) $prompt = 1;
                foreach ($question as $key => $value) {
                    if (!$value['title'] || !self::$prompt) continue;
                    $newQuestion = Question::create([
                        'type' => intval($prompt),
                        'text' => $value['title'],
                        'card_id' => $card->id,
                    ]);
                    if (isset($value['answers'])) {
                        if (isset($value['answers']['title'])) {
                            Answer::create([
                                'text' => $value['answers']['title'],
                                'correct' => isset($value['answers']['correct']) ? $value['answers']['correct'] : True,
                                'question_id' => $newQuestion->id,
                            ]);
                        } else {
                            foreach ($value['answers'] as $k => $answer) {
                                Answer::create([
                                    'text' => $answer['title'],
                                    'correct' => $answer['correct'],
                                    'question_id' => $newQuestion->id,
                                ]);
                            }
                        }
                    }
                }
                $prompt++;
                self::$card_id = $card->id;
            }
        }
    }
    public static function parser($text, array $data)
    {
        // handle error in cas
        $prompt = Prompt::find($data['prompt']);


        if ($prompt->model_type == 1) {
            if (!json_decode($text)) {
                $data['text'] = (new ChatGPT())->fixTheSummary($data['text']);
                self::generateByChat($data);
                return false;
            } else {
                return json_decode($text, true);
            }
        } else {
            $start = strpos($text, "[{");
            $end = strrpos($text, "}]");
            if ($start !== false && $end !== false) {
                $json_string = substr($text, $start, $end - $start + 2);
                if (!json_decode($json_string)) {
                    $response = (new ChatGPT())->fixTheIncorrect($json_string, 'from parser');
                    self::parser($response, $data);
                    return false;
                } else {
                    return json_decode($json_string, true);
                }
            } else {
                self::generate($data);
                return false;
            }
        }
    }
    public static function createCards($text, array $data)
    {
        $NewQuestions = [];

        $NewQuestions = self::parser($text, $data);
        $text = $data['text'];
        $creation_type = $data['type'] == 'highlight' ? CreationType::HIGHLIGHT : CreationType::TEXT;
        $rule = false;
        if ($last_card_in_database = Card::orderBy('id', 'desc')->first()) {
            $rule = Carbon::now()->diffInSeconds($last_card_in_database->created_at) < 15;
        }
        if (!$rule) {
            $card = Card::create([
                'summary' => $text,
                'position' => 1,
                'creation_type' => $creation_type,
                'chapter' => $data['chapter'] ?? null,
                'page_number' => $data['page_number'] ?? '1'
            ]);
            if (self::$course) {
                $card->course_id = self::$course;
                $card->save();
            }
            foreach ($NewQuestions as $key => $question) {
                if (!$question['title'] || !self::$prompt) continue;

                $newQuestion = Question::create([
                    'type' => intval(self::$prompt),
                    'text' => $question['title'],
                    'card_id' => $card->id,
                ]);
                if (isset($question['answers'])) {
                    if (isset($question['answers']['title'])) {
                        Answer::create([
                            'text' => $question['answers']['title'],
                            'correct' => isset($question['answers']['correct']) ? $question['answers']['correct'] : True,
                            'question_id' => $newQuestion->id,
                        ]);
                    } else {
                        foreach ($question['answers'] as $k => $answer) {
                            Answer::create([
                                'text' => $answer['title'],
                                'correct' => $answer['correct'],
                                'question_id' => $newQuestion->id,
                            ]);
                        }
                    }
                }
            }
            self::$card_id = $card->id;
        }
    }
    protected function getCancelFormAction(): Action
    {
        return Action::make('Back to course')
            ->label('Back to course')
            ->url($this->previousUrl ?? static::getResource()::getUrl())
            ->color('secondary');
    }
}
