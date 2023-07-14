<?php

namespace App\Filament\Resources\PromptResource\Pages;

use App\Filament\Resources\PromptResource;
use Filament\Pages\Actions;
use Filament\Resources\Pages\ListRecords;
use Illuminate\Contracts\Database\Query\Builder;

class ListPrompts extends ListRecords
{
    protected static string $resource = PromptResource::class;


    protected function getTableReorderColumn(): ?string
    {
        return 'sort_order';
    }
    
    protected function getActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
