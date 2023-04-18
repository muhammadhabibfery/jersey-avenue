<?php

namespace App\Filament\Resources\LeagueResource\Pages;

use App\Filament\Resources\LeagueResource;
use Filament\Pages\Actions;
use Filament\Resources\Pages\ListRecords;

class ListLeagues extends ListRecords
{
    protected static string $resource = LeagueResource::class;
    protected static ?string $title = 'List of leagues';

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
