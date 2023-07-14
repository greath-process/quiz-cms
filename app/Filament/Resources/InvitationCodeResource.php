<?php

namespace App\Filament\Resources;

use App\Filament\Resources\InvitationCodeResource\Pages;
use App\Filament\Resources\InvitationCodeResource\RelationManagers;
use App\Models\InvitationCode;
use Filament\Forms;
use Filament\Resources\Form;
use Filament\Resources\Resource;
use Filament\Resources\Table;
use Filament\Tables;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class InvitationCodeResource extends Resource
{
    protected static ?string $model = InvitationCode::class;

    protected static ?string $navigationIcon = 'heroicon-o-collection';

    protected static ?string $navigationGroup = "Settings";

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('code')
                    ->label('Code')
                    ->required()
                    ->unique()
                    ->rules('required', 'unique:invitation_codes,code,{{record}}'),
            ]);

    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('code')
                    ->label('Code')
                    ->searchable()
                    ->sortable(),
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
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
            'index' => Pages\ListInvitationCodes::route('/'),
            'create' => Pages\CreateInvitationCode::route('/create'),
            'edit' => Pages\EditInvitationCode::route('/{record}/edit'),
        ];
    }
}
