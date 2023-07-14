<?php

namespace App\Filament\Resources\CardResource\Pages;

use App\Filament\Resources\CardResource;
use App\Filament\Resources\CourseResource;
use App\Models\Answer;
use App\Models\Course;
use App\Models\Prompt;
use App\Models\Question;
use App\Services\ChatGPT;
use Faker\Core\Color;
use Filament\Pages\Actions\Action;
use Filament\Pages\Actions;
use Filament\Resources\Pages\EditRecord;
use Filament\Support\Exceptions\Halt;
use HtmlSanitizer\Extension\Listing\Node\DdNode;
use HtmlSanitizer\Extension\Listing\NodeVisitor\DdNodeVisitor;
use Illuminate\Http\RedirectResponse;
use Termwind\Components\Dd;

class EditCard extends EditRecord
{
    protected static string $resource = CardResource::class;
    protected string $question_type = '';

    public string $page;
    protected static array $types = [
        'true or false' => 'true or false',
        'blank' => 'blank',
        'multiple choice' => 'multiple choice',
        'mix' => 'mix',
    ];

    public function mount($record): void
    {
        parent::mount($record);
        $this->page = request()->getPathInfo();
    }

    protected function getActions(): array
    {
        $action[] = Actions\DeleteAction::make()->modalSubheading("Are you sure you want to delete this card? It will also delete all questions. It cannot be undone.");
        if ($course = Course::find($this->data['course_id'])) {
            $card = $course->cards()->where('id', $this->data['id'])->first();

            $previous_card = $course->cards()->where('id', '<', $card->id)->orderBy('id', 'desc')->first();
            $next_card = $course->cards()->where('id', '>', $card->id)->orderBy('id', 'asc')->first();
            if ($previous_card) {
                $action[] = Actions\Action::make('Previous Card')
                    ->url(fn () => CardResource::getUrl('edit', ['record' => $previous_card]))
                    ->icon('heroicon-s-arrow-left')
                    ->extraAttributes(['class' => 'floating-navigation floating-navigation-l'])
                    ->color('secondary');
            }
            if ($next_card) {
                $action[] = Actions\Action::make('Next Card')
                    ->url(fn () => CardResource::getUrl('edit', ['record' => $next_card]))
                    ->icon('heroicon-s-arrow-right')
                    ->extraAttributes(['class' => 'floating-navigation floating-navigation-r'])
                    ->color('secondary');
            }
        }

        return $action;
    }

    public function redirectt(): void
    {
        $this->redirect_back($this->data['id']);
    }

    public function getQuestion(string $q): array
    {
        $form = $this->data;
        $question_code = strstr($q, "record-");

        return $form['questions'][$question_code];
    }
    public function redirect_back(string $q)
    {
        if ($this->data['course_id'])
            return redirect('admin/courses/' . $this->data['course_id'] . '/edit');
        else
            return redirect('admin/cards/' . $this->data['id'] . '/edit');
    }
    protected function getCancelFormAction(): Action
    {
        return Action::make('cancel')
            ->label('Back to Course')
            ->url(fn () => CourseResource::getUrl('edit', ['record' => $this->data['course_id']]))
            ->color('secondary');
    }
    protected function getCreateAnotherFormAction(): Action
    {
        return Action::make('create_another')
            ->label('Save & create another')
            ->action('saved')
            ->color('primary');
    }

    public function saved(): void
    {
        $this->authorizeAccess();

        try {
            $this->callHook('beforeValidate');

            $data = $this->form->getState();

            $this->callHook('afterValidate');

            $data = $this->mutateFormDataBeforeSave($data);

            $this->callHook('beforeSave');

            $this->handleRecordUpdate($this->getRecord(), $data);

            $this->callHook('afterSave');
        } catch (Halt $exception) {
            return;
        }
        $redirect = CardResource::getUrl('course', ['record' => $this->record]);
        $this->redirect($redirect);
    }
    protected function getFormActions(): array
    {
        return [
            $this->getSaveFormAction(),
            $this->getCreateAnotherFormAction(),
            $this->getCancelFormAction(),
        ];
    }
    public function regenerate(string $q): void
    {
        $form = $this->data;
        $prompt = Prompt::where('id', $form['prompt'])->first();
        $this->question_type = $prompt->quiz_type ?? 'mix';
        if ($form["prompt"] && $form['summary']) {
            if ($prompt->model_type == 1) {
                $form['number_of_questions'] = 1;
                $form['text'] = $this->data['summary'];
                $response = (new ChatGPT())->getQuizByChat($form);
            } else {
                $params = [
                    'NUM_QUESTION' => 1,
                    'DIFFICULTY' => $form["difficulty"] ?? '',
                    'SUMMARY' => $form['summary'],
                    'PROMPT' => $form["prompt"],
                ];
                $response = (new ChatGPT())->getQuizesByText($params);
            }

            $this->newQuestion($response, $q);
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
    public function newQuestion(string $text, string $q)
    {
        $NewQuestion = [];

        $NewQuestion = self::parser($text, $this->data);

        $NewQuestion = current($NewQuestion);
        if ($NewQuestion) {
            $question = new Question;
            $question->card_id = $this->data['id'];
            $question->type = array_search($this->question_type, array_keys(self::$types)) + 1;
            $question->text = $NewQuestion['title'];
            $question->save();

            if (isset($NewQuestion['answers']) && is_array($NewQuestion['answers'])) {
                foreach ($NewQuestion['answers'] as $answer) {
                    Answer::create([
                        'text' => is_array($answer) ? $answer['title'] : $answer,
                        'correct' => is_array($answer) ? $answer['correct'] : true,
                        'question_id' => $question->id,
                    ]);
                }
            }

            return redirect($this->page);
        }

        return false;
    }
}
