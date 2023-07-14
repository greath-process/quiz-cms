<?php

namespace App\Services;

use Airtable;
use App\Enums\QuestionType;
use HtmlSanitizer\Extension\Listing\Node\DdNode;
use HtmlSanitizer\Extension\Listing\NodeVisitor\DdNodeVisitor;
use Tapp\Airtable\Airtable as AirtableAirtable;
use Termwind\Components\Dd;

class ExportAirTable
{
    public array $fields = [];
    public array $types = [];
    public string $fileName = 'Quiz -';
    public string $fileExt = '.csv';
    public string $pathToFile = 'export.csv';

    public function __construct()
    {
        $this->fields = [
            'summary',
            'question',
            'type',
            'isIntroCard',
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

    public function exportToAirTable(array $data)
    {
        $data = $this->formatData($data);
        foreach($data as $key => $row)
         {
            if($key == 0) continue;
            $insert = [
                'summary' => $row[0],
                'question' => $row[1],
                'type' => $row[2],
                'isIntroCard' => $row[3],
                'choice1' => array_key_exists(4, $row)? $row[4] : null,
                'choice1correct' => array_key_exists(5, $row)? $row[5] : null,
                'choice2' => array_key_exists(6, $row)? $row[6] : null,
                'choice2correct' => array_key_exists(7, $row)? $row[7] : null,
                'choice3' => array_key_exists(8, $row)? $row[8] : null,
                'choice3correct' => array_key_exists(9, $row)? $row[9] : null,
                'choice4' => array_key_exists(10, $row)? $row[10] : null,
                'choice4correct' => array_key_exists(11, $row)? $row[11] : null,
                'choice5' => array_key_exists(12, $row)? $row[12] : null,
                'choice5correct' => array_key_exists(13, $row)? $row[13] : null,
                'choice6' => array_key_exists(14, $row)? $row[14] : null,
                'choice6correct' => array_key_exists(15, $row)? $row[15] : null,
                'choice7' => array_key_exists(16, $row)? $row[16] : null,
                'choice7correct' => array_key_exists(17, $row)? $row[17] : null,
                'choice8' => array_key_exists(18, $row)? $row[18] : null,
                'choice8correct' => array_key_exists(19, $row)? $row[19] : null,
            ];
            Airtable::create($insert);
        }
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
            $fileArray[$key] = [$question['summary'], $question['text'], $this->getType($question), ($question['intro_card'] ? true : false)];

            foreach ($question['answers'] as $answer) {
                $fileArray[$key][] = $answer['text'];
                $fileArray[$key][] = $answer['correct'] ? true : false;
            }
        }

        return $fileArray;
    }
}
