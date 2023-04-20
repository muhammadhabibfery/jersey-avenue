<?php

namespace App\Filament\Resources\JerseyResource\Pages;

use Filament\Pages\Actions;
use Filament\Resources\Pages\ListRecords;
use Illuminate\Database\Eloquent\Builder;
use App\Filament\Resources\JerseyResource;
use App\Models\User;

class ListJerseys extends ListRecords
{
    protected static string $resource = JerseyResource::class;
    protected static ?string $title = 'List of jerseys';

    protected function getTableQuery(): Builder
    {
        $query = static::getResource()::getEloquentQuery();

        return $this->getResource()::getUser()->role == User::$roles[0]
            ? $query
            : $query->withoutTrashed();
    }

    public function getBreadcrumb(): ?string
    {
        return trans(self::$title);
    }

    protected function getTitle(): string
    {
        return trans(self::$title);
    }

    protected function getActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }

    protected function getTableBulkActions(): array
    {
        return [];
    }
}
