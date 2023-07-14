<?php

namespace App\Filament\Resources\CourseResource\RelationManagers;

use App\Filament\Resources\CardResource;
use App\Filament\Resources\CardResource\Pages\CreateCards;
use App\Models\Card;
use App\Models\Course;
use Filament\Resources\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Resources\Table;
use Filament\Tables;
use HtmlSanitizer\Extension\Listing\Node\DdNode;
use Illuminate\Database\Eloquent\Model;
use Termwind\Components\Dd;

class CardsRelationManager extends RelationManager
{
    protected static string $relationship = 'cards';
    public static $type = '';
    public static int $course_id = 0;

    public function mount(): void
    {
        self::$course_id = $this->ownerRecord->id;
    }

    public static function form(Form $form): Form
    {
        if (self::$type == 'create') {
            return (new CreateCards)->form($form);
        } elseif (self::$type == 'edit') {
            return CardResource::form($form);
        } else {
            return $form;
        }
    }

    public static function table(Table $table): Table
    {
        $table = CardResource::table($table);
        $table->headerActions([
            Tables\Actions\CreateAction::make()
                ->url(fn (): string => CardResource::getUrl('course', ['record' => self::$course_id]))
        ]);
        return $table;
    }
}
