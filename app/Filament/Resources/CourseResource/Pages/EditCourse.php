<?php

namespace App\Filament\Resources\CourseResource\Pages;

use App\Filament\Resources\CourseResource;
use App\Models\Card;
use App\Models\Course;
use Closure;
use Filament\Pages\Actions\Action;
use Filament\Resources\Pages\EditRecord;
use PharIo\Manifest\Url;
use Termwind\Components\Dd;
use Filament\Support\Actions\Modal\Actions\Action as Actione;
use Filament\Tables\Actions\BulkAction;
use HtmlSanitizer\Extension\Listing\Node\DdNode;
use Symfony\Component\HttpFoundation\UrlHelper;

class EditCourse extends EditRecord
{
    protected static string $resource = CourseResource::class;
    protected function beforeFill(): void
    {
        if ($all_card  = Card::where('summary', null)->get()) {
            foreach ($all_card as $card) {
                $card->delete();
            }
        }
    }
    public function afterFill(): void
    {
        if ($all_card  = Card::where('summary', null)->get()) {
            foreach ($all_card as $card) {
                $card->delete();
            }
        }
    }
    protected function getActions(): array
    {
        return [
            Action::make('archive')
                ->icon('heroicon-o-archive')
                ->action(
                    function () {
                        $this->getRecord()->update(['archived' => true]);
                        return redirect()->to('/admin/courses/');
                    }
                 )->requiresConfirmation()
                 ,
        ];
    }
}
