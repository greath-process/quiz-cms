<?php

namespace App\Filament\Resources\InvitationCodeResource\Pages;

use App\Filament\Resources\InvitationCodeResource;
use Filament\Pages\Actions;
use Filament\Resources\Pages\ListRecords;

class ListInvitationCodes extends ListRecords
{
    protected static string $resource = InvitationCodeResource::class;

    protected function getActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
