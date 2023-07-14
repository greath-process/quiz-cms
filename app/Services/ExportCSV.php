<?php

namespace App\Services;

use App\Enums\QuestionType;
use Carbon\Carbon;
use HtmlSanitizer\Util\Dumper;
use Spatie\LaravelIgnition\Recorders\DumpRecorder\Dump;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Termwind\Components\Dd;

class ExportCSV
{
    public array $fields = [];
    public array $types = [];
    public string $fileName = 'Quiz -';
    public string $fileExt = '.csv';
    public string $pathToFile = 'export.csv';

    public function __construct()
    {
        $this->fields = [
            'question',
            'summary',
            'Page Number',
            'sourceChapter',
            'sourceHighlight',
            'isIntroCard',
            'type',
            'choice1',
            'choice1correct',
            'choice2',
            'choice2correct',
            'choice3',
            'choice3correct',
            'choice4',
            'choice4correct',
            'choice5',
            'choice5correct',
            'choice6',
            'choice6correct',
            'choice7',
            'choice7correct',
            'choice8',
            'choice8correct'
        ];

        $this->types = [
            'true or false' => 'TRUEFALSE',
            'blank' => 'BLANK',
            'multiple choice' => 'CHOICE'
        ];
    }

    public function exportToFile(array $data, ?string $title = ''): BinaryFileResponse
    {
        $data = $this->formatData($data);

        $this->setFileName($title);
        $handle = fopen($this->pathToFile, 'w');

        foreach ($data as $key => $row) {
            fputcsv($handle, $row, ',');
        }

        fclose($handle);

        return response()->download($this->pathToFile);
    }

    public function getType(array $question): string
    {
        return isset($question['type']) && $question['type'] != 'mix'
            ? (is_numeric($question['type'])
                    ? QuestionType::fromValue($question['type'])
                    : $this->types[ $question['type'] ])
            : (!isset($question['answers']) || count($question['answers']) == 1 || isset($question['answers']['title'])
                    ? $this->types['blank']
                    : (array_filter($question['answers'], fn($item) =>
                        is_array($item)
                        && strcasecmp((isset($item['title']) ? $item['title'] : $item['text']), 'true') == 0)
                        ? $this->types['true or false']
                        : $this->types['multiple choice']
                    )
            );
    }

    public function formatData(array $questions): array
    {
        $fileArray = [$this->fields];
        foreach ($questions as $key => $question) {
            if($key > 0 && $question['cards_id'] == $questions[($key - 1)]['cards_id']) continue;
            $key++;
            $fileArray[$key] = [$question['text'], $question['summary'], $question['page_number'], $question['chapter'], $question['source_highlight'], ($question['intro_card'] ? 'checked' : ''), $this->getType($question)];

            foreach ($question['answers'] as $answer) {
                $fileArray[$key][] = $answer['text'];
                $fileArray[$key][] = $answer['correct'] ? 'checked' : '';
            }
        }

        return $fileArray;
    }

    public function setFileName(?string $title): void
    {
        $currentDateTime = Carbon::now()->format('d.m.Y H.i');
        $this->pathToFile = "$this->fileName $title - $currentDateTime$this->fileExt";
    }
}
