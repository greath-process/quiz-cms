<?php

namespace App\Filament\Resources;

use App\Filament\Resources\CourseResource\Pages;
use App\Filament\Resources\CourseResource\RelationManagers;
use App\Models\Course;
use App\Services\ExportAirTable;
use App\Services\ExportCSV;
use BezhanSalleh\FilamentShield\Contracts\HasShieldPermissions;
use Filament\Forms;
use Filament\Resources\Form;
use Filament\Resources\Resource;
use Filament\Resources\Table;
use Filament\Tables;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Filters\SelectFilter;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Auth;
use Termwind\Components\Dd;
use App\Models\User;

class CourseResource extends Resource implements HasShieldPermissions
{
    protected static ?string $model = Course::class;

    protected static ?string $navigationIcon = 'heroicon-o-collection';

    protected static ?string $navigationGroup = 'Courses';

    protected static ?int $navigationSort = 1;

    protected static bool $shouldRegisterNavigation = true;

    public static function getPermissionPrefixes(): array
    {
        return [
            'view_any',
            'create',
            'update',
            'delete',
            'delete_any',
            'view_own',
            'view_all',
            'archive',
        ];
    }

    public static function form(Form $form): Form
    {
        $years = Arr::sortDesc(range(1900, date('Y')));

        return $form
            ->schema([
                Forms\Components\Section::make("Course Details")
                    ->collapsed()
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
                            ->nullable(),
                        Forms\Components\Textarea::make('summary')
                            ->inlineLabel(),
                        Forms\Components\FileUpload::make('cover_image')
                            ->inlineLabel(),
                        Forms\Components\ColorPicker::make('cover_color')
                            ->nullable()
                            ->inlineLabel(),
                        Forms\Components\Toggle::make('archived')
                            ->inlineLabel()
                            ->hidden(auth()->user()->hasRole('Guest Quizmaster'))
                            ->required(),
                        Forms\Components\Select::make('user_id')
                            ->inlineLabel()
                            ->label('Quizmaster')
                            ->disabled(auth()->user()->hasRole('Guest Quizmaster'))
                            ->relationship('user', 'name')
                            ->required(),
                        Forms\Components\DateTimePicker::make('created_at')
                            ->inlineLabel()
                            ->hidden(auth()->user()->hasRole('Guest Quizmaster'))
                            ->disabled(),
                    ]),
            ]);
    }


    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('title')
                    ->sortable()
                    ->searchable(),
                Tables\Columns\BadgeColumn::make('cards_count')
                    ->counts('cards')
                    ->alignment('right')
                    ->colors([
                        'primary',
                    ])
                    ->label('Cards')
                    ->sortable(),
                Tables\Columns\TextColumn::make('user.name')
                    ->label('Quizmaster')
                    ->sortable(),
                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime('d.m.Y H:i')
                    ->extraAttributes(['class' => 'table-date-text'])
                    ->sortable(),
            ])
            ->defaultSort('id', 'desc')
            ->filters([
                SelectFilter::make('user_id')
                    ->label('Quizmaster')
                    ->hidden(auth()->user()->hasRole('Guest Quizmaster'))
                    ->options(
                        User::all()->pluck('name', 'id')->toArray()
                    ),
            ])
            ->actions([

                Tables\Actions\Action::make('Archive')
                    ->hidden(fn () => !Auth::user()->can('archive_course'))
                    ->action(fn (Course $record) => $record->update(['archived' => true]))
                    ->icon('heroicon-o-archive')
                    ->requiresConfirmation(),
                Tables\Actions\Action::make('Export to csv')
                    ->label('CSV')
                    ->icon('heroicon-o-upload')
                    ->hidden(auth()->user()->hasRole('Guest Quizmaster'))
                    ->action(function (Course $record) {
                        $record->load('cards.questions.answers');
                        $questions = $record->cards->flatMap(function ($card) {
                            return $card->questions->map(function ($question) use ($card) {
                                $question['summary'] = $card->summary;
                                $question['source_highlight'] = $card->source_text;
                                $question['page_number'] = $card->page_number;
                                $question['chapter'] = $card->chapter;
                                $question['intro_card'] = $card->isIntroCard();
                                $question['answers'] = $question->answers->toArray();
                                $question['cards_id'] = $card->id;
                                return $question;
                            });
                        })->sortBy('sort')->toArray();

                        return (new ExportCSV)->exportToFile($questions, $record->title);
                    }),
                Tables\Actions\Action::make('Export to AirTable')
                    ->label('Airtable')
                    ->icon('heroicon-o-upload')
                    ->hidden(auth()->user()->hasRole('Guest Quizmaster'))
                    ->requiresConfirmation()
                    ->modalHeading('Export Cards to Airtable')
                    ->modalSubheading('This will export this Course\'s Cards to Airtable into the Cards table. Are you sure you\'d like to continue?')
                    ->action(function (Course $record) {
                        $record->load('cards.questions.answers');
                        $questions = $record->cards->flatMap(function ($card) {
                            return $card->questions->map(function ($question) use ($card) {
                                $question['summary'] = $card->summary;
                                $question['intro_card'] = $card->isIntroCard();
                                $question['answers'] = $question->answers->toArray();
                                $question['cards_id'] = $card->id;
                                return $question;
                            });
                        })->sortBy('sort')->toArray();

                        return (new ExportAirTable())->exportToAirTable($questions);
                    }),
                Tables\Actions\EditAction::make(),
            ])
            ->actionsPosition(Tables\Actions\Position::AfterCells)
            ->bulkActions([
                Tables\Actions\DeleteBulkAction::make(),
            ]);
    }

    public static function getNavigationItems(): array
    {
        return array_merge(Pages\ListCourses::getNavigationItems(), Pages\ArchiveListCourses::getNavigationItems());
    }

    public static function getRelations(): array
    {
        return [
            RelationManagers\CardsRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListCourses::route('/'),
            'create' => Pages\CreateCourse::route('/create'),
            'edit' => Pages\EditCourse::route('/{record}/edit'),
            'archive' => Pages\ArchiveListCourses::route('/archive'),
        ];
    }

}
