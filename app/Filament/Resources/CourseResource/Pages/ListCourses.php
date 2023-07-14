<?php

namespace App\Filament\Resources\CourseResource\Pages;

use App\Filament\Resources\CourseResource;
use App\Models\Course;
use Filament\Navigation\NavigationItem;
use Filament\Pages\Actions;
use Filament\Resources\Pages\ListRecords;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;
use IntlChar;
use Psy\Command\WhereamiCommand;
use Termwind\Components\Dd;

class ListCourses extends ListRecords
{
    protected static string $resource = CourseResource::class;

    protected static ?string $title = 'Courses';

    protected static ?string $navigationIcon = 'heroicon-o-book-open';

    protected static bool $shouldRegisterNavigation = true;

    protected static ?string $navigationGroup = "Courses";

    protected static ?string $slug = 'index';

    protected function getTableQuery(): Builder
    {
        $user = Auth::user();
        if ($user->can('view_all_course')) {
            return Course::query()->where('archived', 0);
        } else if ($user->can('view_own_course')) {
            return Course::query()->where('user_id', $user->id)->where('archived', 0);
        }
        return Course::query()->where('user_id', 0);
    }

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

    protected function getActions(): array
    {
        return [
            Actions\CreateAction::make()
        ];
    }


}
