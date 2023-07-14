<?php

namespace Filament\Resources\Pages\Concerns;

use App\Models\Course;


trait HasRecordBreadcrumb
{
    protected function getBreadcrumbs(): array
    {
        $resource = static::getResource();

        $breadcrumbs = [
            $resource::getUrl() => $resource::getBreadcrumb(),
        ];
        // dd($this->getRecordTitle());
        if ($this->getRecordTitle() == 'Card') {
            $courses = Course::find($this->getRecord()->course_id);
            $breadcrumbs = [
                url('/admin/courses') => 'Courses',
                url('/admin/courses/' . $this->getRecord()->course_id . '/edit') => $courses->title,
            ];
        } else {
            $breadcrumbs = [
                $resource::getUrl() => $resource::getBreadcrumb(),
            ];
            if ($this->getRecord()->exists && $resource::hasRecordTitle()) {
                if ($resource::hasPage('view') && $resource::canView($this->getRecord())) {
                    $breadcrumbs[$resource::getUrl('view', ['record' => $this->getRecord()])] = $this->getRecordTitle();
                } elseif ($resource::hasPage('edit') && $resource::canEdit($this->getRecord())) {
                    $breadcrumbs[$resource::getUrl('edit', ['record' => $this->getRecord()])] = $this->getRecordTitle();
                } else {
                    $breadcrumbs[] = $this->getRecordTitle();
                }
            }
        }
        $breadcrumbs[] = $this->getBreadcrumb();
        return $breadcrumbs;
    }
}
