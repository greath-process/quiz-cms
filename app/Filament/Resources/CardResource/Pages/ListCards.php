<?php

namespace App\Filament\Resources\CardResource\Pages;

use App\Filament\Resources\CardResource;
use App\Models\Card;
use App\Models\Course;
use Filament\Pages\Actions;
use Filament\Resources\Pages\ListRecords;
use Termwind\Components\Dd;

class ListCards extends ListRecords
{
    protected static string $resource = CardResource::class;

    protected function getActions(): array
    {
        return [
            Actions\CreateAction::make()
        ];
    }
}
