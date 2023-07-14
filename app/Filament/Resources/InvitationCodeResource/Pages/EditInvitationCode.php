<?php

namespace App\Filament\Resources\InvitationCodeResource\Pages;

use App\Filament\Resources\InvitationCodeResource;
use Filament\Pages\Actions;
use Filament\Resources\Pages\EditRecord;

class EditInvitationCode extends EditRecord
{
    protected static string $resource = InvitationCodeResource::class;

    protected function getActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
