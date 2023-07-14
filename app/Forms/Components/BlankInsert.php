<?php

namespace App\Forms\Components;

use Filament\Forms\Components\Field;
use Filament\Forms\Components\Textarea as FilamentTextarea;
use Wiebenieuwenhuis\FilamentCharCounter\Concerns\HasCharacterLimit;
class BlankInsert extends FilamentTextarea
{
    use HasCharacterLimit;

    protected string $view = 'forms.components.blank-insert';
}
