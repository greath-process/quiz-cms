<?php

namespace App\Filament\Resources;

use App\Enums\CreationType;
use App\Enums\QuestionType;
use App\Filament\Resources\CardResource\Pages;
use App\Forms\Components\BlankInsert;
use App\Forms\Components\Regenerate;
use App\Forms\Components\RegenerateButton;
use App\Models\Card;
use App\Models\Prompt;
use Filament\Forms;
use Filament\Resources\Form;
use Filament\Resources\Resource;
use Filament\Resources\Table;
use Filament\Tables;
use Illuminate\Database\Eloquent\Model;

class CardResource extends Resource
{
    protected static ?string $model = Card::class;
    protected static ?string $navigationIcon = 'heroicon-o-collection';
    protected static bool $shouldRegisterNavigation = false;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Card::make()
                    ->schema([
                        Forms\Components\Select::make('course_id')
                            ->disabled(auth()->user()->hasRole('Guest Quizmaster'))
                            ->relationship('course', 'title')
                            ->columnSpanFull()
                            ->inlineLabel(),
                        Forms\Components\Select::make('lesson_id')
                            ->relationship('lesson', 'title')
                            ->hidden(auth()->user()->hasRole('Guest Quizmaster'))
                            ->columnSpanFull()
                            ->inlineLabel(),
                        Forms\Components\TextInput::make('chapter')
                            ->columnSpanFull()
                            ->inlineLabel(),
                        Forms\Components\TextInput::make('page_number')
                            ->numeric()
                            ->columnSpanFull()
                            ->inlineLabel(),
                        Forms\Components\Toggle::make('intro_card')
                            ->hidden(auth()->user()->hasRole('Guest Quizmaster'))
                            ->columnSpanFull()
                            ->default(false)
                            ->inlineLabel()
                            ->required(),
                        Forms\Components\Textarea::make('source_text')
                            ->label("Context")
                            ->inlineLabel()
                            ->hint('You may paste any context your quiz question is based on, e.g. a whole paragraph or page.')
                            ->columnSpanFull(),
                        Forms\Components\MarkdownEditor::make('summary')
                            ->label("Question summary")
                            ->inlineLabel()
                            ->hint('A short excerpt from the content the question is based on. Highlight the correct answer by formatting it bold.')
                            ->columnSpanFull()
                            ->toolbarButtons([
                                'bold',
                                'bulletList',
                                'edit',
                                'italic',
                                'preview'
                            ]),
                        Forms\Components\Select::make('creation_type')
                            ->label("Question generation type")
                            ->inlineLabel()
                            ->columnSpanFull()
                            ->options(CreationType::array()),
                        Forms\Components\DateTimePicker::make('created_at')
                            ->columnSpanFull()
                            ->inlineLabel()
                            ->disabled(),
                        Forms\Components\Repeater::make('questions')
                            ->createItemButtonLabel('Add question alternative (manual)')
                            ->relationship()
                            ->collapsible()
                            ->itemLabel(function (array $state): ?string {
                                $name = $state['type']  ? QuestionType::fromValue($state['type']) : null;
                                return $state['text'] ? $state['text']  . ' (' . $name . ')' : null;
                            })
                            ->schema([
                                Forms\Components\Select::make('type')
                                    ->options(QuestionType::array())
                                    ->label('Question Type')
                                    ->inlineLabel()
                                    ->reactive(),
                                BlankInsert::make('text')
                                    ->characterLimit(140)
                                    ->label('Question (or statement)')
                                    ->inlineLabel()
                                    ->reactive()
                                    ->hint('For BLANK statements add <code>{{BLANK}}</code> where the blank(s) is/are placed.'),
                                Forms\Components\Grid::make()->id('answer_container')
                                    ->schema([
                                        Forms\Components\Repeater::make('answers')
                                            ->relationship()
                                            ->hint('Set toggle to blue for correct answer')
                                            ->createItemButtonLabel('Add answer')
                                            ->extraAttributes(['class' => 'answer-repeater'])
                                            ->schema([
                                                \Wiebenieuwenhuis\FilamentCharCounter\TextInput::make('text')
                                                    ->characterLimit(80)
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
                            ->orderable('sort')
                            ->columnSpanFull()
                            ->hint('<span class="text-red-500 font-bold">Only the first question will be used for this card\'s quiz.</span>')
                            ->minItems(1),
                        Forms\Components\Card::make(['title' => 'AI Prompt'])
                            ->schema([
                                Regenerate::make('Generate :')
                                    ->columnSpan(1),
                                Forms\Components\Select::make('prompt')
                                    ->label('AI Prompt')
                                    ->options(Prompt::orderBy('sort_order', 'asc')->pluck('name', 'id')->toArray())
                                    ->columnSpan(1),
                                RegenerateButton::make('')
                                    ->columnSpan(1),

                            ])
                            ->columns(3),

                    ])
                    ->columns([
                        'md' => 12,
                    ]),

            ]);
    }

    public static function table(Table $table): Table
    {
        $table = $table
            ->columns([
                Tables\Columns\TextColumn::make('summary')->words(10)->searchable(),
                Tables\Columns\TextColumn::make('course.title')
                    ->sortable(),
                Tables\Columns\BadgeColumn::make('questions.type')
                    ->label('Q. type')
                    ->getStateUsing(function (Model $record) {
                        return $record->questions()->first()?->type?->name;
                    })
                    ->weight('bold')
                    ->colors([
                        'q_choice' => 'CHOICE',
                        'q_truefalse' => 'TRUEFALSE',
                        'q_blank' => 'BLANK',
                    ]),
                Tables\Columns\TextColumn::make('created_at')->dateTime()
                    ->sortable(),
            ])
            ->defaultSort('id', 'desc')
            ->filters([
                //
            ])
            ->actions([

                Tables\Actions\EditAction::make()
                    ->url(fn (Card $record): string =>  CardResource::getUrl('edit', ['record' => $record])),
                Tables\Actions\DeleteAction::make()->modalSubheading("Are you sure you want to delete this card? It will also delete all its questions. It cannot be undone."),
            ])
            ->bulkActions([
                Tables\Actions\DeleteBulkAction::make(),
            ]);
        return $table;
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListCards::route('/'),
            'course' => Pages\CreateCards::route('/{record}/create'),
            'create' => Pages\CreateCards::route('/create'),
            'edit' => Pages\EditCard::route('/{record}/edit'),
        ];
    }
}
