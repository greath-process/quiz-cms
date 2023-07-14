<?php

namespace App\Filament\Resources\CourseResource\Pages;

use App\Filament\Resources\CourseResource;
use App\Models\Course;
use Filament\Facades\Filament;
use Filament\Navigation\NavigationItem;
use Filament\Pages\Actions;
use Filament\Resources\Pages\ListRecords;
use Filament\Tables\Actions\Action;
use Filament\Tables\Actions\DeleteAction;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;

class ArchiveListCourses extends ListRecords
{
    protected static string $resource = CourseResource::class;

    protected static ?string $title = 'Archive';

    protected static ?string $navigationGroup = 'Courses';

    protected static ?string $navigationIcon = 'heroicon-o-archive';

    protected static ?string $navigationLabel = 'Archive';

    protected static ?string $slug = 'archive';

    protected static ?int $navigationSort = 1;

    protected static bool $shouldRegisterNavigation = true;

    public static function getRouteName(): string
    {
        $routeBaseName = static::getResource()::getRouteBaseName();
        $slug = static::getSlug();

        return "{$routeBaseName}.{$slug}";
    }

    public static function getNavigationItems(): array
    {
        return [
            NavigationItem::make(static::getNavigationLabel())
                ->group(static::getNavigationGroup())
                ->icon(static::getNavigationIcon())
                ->activeIcon(static::getActiveNavigationIcon())
                ->isActiveWhen(fn () => request()->routeIs(static::getRouteName()))
                ->badge(static::getNavigationBadge(), color: static::getNavigationBadgeColor())
                ->sort(static::getNavigationSort())
                ->url(static::getNavigationUrl()),
        ];
    }

    protected function getTableQuery(): Builder
    {
        $user = Auth::user();
        if ($user->can('view_all_course')) {
            return Course::query()->where('archived', 1);
        } else if ($user->can('view_own_course')) {
            return Course::query()->where('user_id', $user->id)->where('archived', 1);
        }
        return Course::query()->where('user_id', 0);
    }

    protected function getTableActions(): array
    {
        return [
            Action::make('Unarchive')
                ->action(fn (Course $record) => $record->update(['archived' => false]))
                ->requiresConfirmation(),
            DeleteAction::make(),
        ];
    }

    protected function getActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }

}
