<?php

namespace App\Filament\Resources\CourseResource\Pages;

use App\Enums\CreationType;
use App\Enums\QuestionType;
use App\Filament\Resources\CourseResource;
use Filament\Resources\Form;
use Filament\Forms;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Auth;

class CreateCourse extends CreateRecord
{
    protected static string $resource = CourseResource::class;

    protected static bool $shouldRegisterNavigation = true;

    protected static ?string $navigationLabel = 'create';
    protected static ?string $navigationGroup = 'Courses';

    protected static ?string $slug = 'courses.create';

    protected function form(Form $form): Form
    {
        $years = Arr::sortDesc(range(1900, date('Y')));

        return $form
            ->schema([
                Forms\Components\Card::make()
                    ->schema([
                        Forms\Components\TextInput::make('title')
                            ->inlineLabel()
                            ->required()
                            ->maxLength(255),
                        Forms\Components\TextInput::make('subtitle')
                            ->inlineLabel()
                            ->maxLength(255),
                        Forms\Components\TextInput::make('author')
                            ->inlineLabel()
                            ->maxLength(255),
                        Forms\Components\Select::make('year')
                            ->inlineLabel()
                            ->options(array_combine($years, $years))
                            ->searchable()
                            ->nullable(),
                        Forms\Components\MarkdownEditor::make('summary')
                            ->inlineLabel()
                            ->toolbarButtons([
                                'bold',
                                'italic',
                                'edit',
                                'preview'
                            ]),
                        Forms\Components\FileUpload::make('cover_image')
                            ->inlineLabel(),
                        Forms\Components\ColorPicker::make('cover_color')
                            ->inlineLabel()
                            ->nullable(),
                        Forms\Components\Toggle::make('archived')
                            ->inlineLabel()
                            ->hidden(auth()->user()->hasRole('Guest Quizmaster')),
                        Forms\Components\Select::make('user_id')
                            ->inlineLabel()
                            ->label('Quizmaster')
                            ->disabled(auth()->user()->hasRole('Guest Quizmaster'))
                            ->relationship('user', 'name')
                            ->default(Auth::user()->id),
                    ])
                    ->columnSpanFull(),
            ]);
    }
    protected function getFormActions(): array
    {
        return array_merge(
            [$this->getCreateFormAction()],
            [$this->getCancelFormAction()],
        );
    }
}
