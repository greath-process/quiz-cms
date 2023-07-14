<?php

namespace App\Filament\Resources;

use App\Filament\Resources\PromptResource\Pages;
use App\Models\Prompt;
use Filament\Forms;
use Filament\Resources\Form;
use Filament\Resources\Resource;
use Filament\Resources\Table;
use Filament\Tables;
use Illuminate\Contracts\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

class PromptResource extends Resource
{
    protected static ?string $model = Prompt::class;

    protected static ?string $navigationIcon = 'heroicon-o-finger-print';

    protected static ?string $navigationGroup = "Settings";
    protected static array $model_type =
    [
        1 => 'Chat Completion',
        2 => 'Completion'
    ];

    protected static array $types = [
        'mix' => 'mix',
        'multiple choice' => 'multiple choice',
        'true or false' => 'true or false',
        'blank' => 'blank'
    ];

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Card::make()
                    ->schema([
                        Forms\Components\TextInput::make('name')
                            ->columnSpanFull()
                            ->required()
                            ->inlineLabel(),
                        Forms\Components\Textarea::make('description')
                            ->label("Description")
                            ->inlineLabel()
                            ->columnSpanFull(),
                        Forms\Components\Select::make('model_type')
                            ->options(self::$model_type)
                            ->default(1)
                            ->inlineLabel()
                            ->columnSpanFull(),
                        Forms\Components\Select::make('quiz_type')
                            ->options(self::$types)
                            ->default('multiple choice')
                            ->inlineLabel()
                            ->columnSpanFull(),
                        Forms\Components\MarkdownEditor::make('prompt')
                            ->label("Prompt")
                            ->required()
                            ->helperText('Variables:<br><code>{NUM_QUESTION}</code>, <code>{DIFFICULTY}</code>, <code>{SUMMARY}</code>')
                            ->columnSpanFull()
                            ->toolbarButtons(),
                        Forms\Components\DateTimePicker::make('created_at')
                            ->columnSpanFull()
                            ->inlineLabel()
                            ->disabled(),
                    ])
                    ->columns([
                        'md' => 12,
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')->words(10)->sortable(),
                Tables\Columns\BadgeColumn::make('quiz_type')
                    ->sortable(),
                Tables\Columns\BadgeColumn::make('model_type')
                    ->getStateUsing(function (Model $record) {
                        return self::$model_type[$record->model_type];
                    })
                    ->sortable(),
                Tables\Columns\TextColumn::make('description')->words(5),
                Tables\Columns\TextColumn::make('created_at')->dateTime()->sortable(),
            ])
            ->defaultSort('sort_order', 'asc')
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\DeleteBulkAction::make(),
            ]);
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
            'index' => Pages\ListPrompts::route('/'),
        ];
    }
}
