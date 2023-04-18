<?php

namespace App\Filament\Resources\LeagueResource\Pages;

use Filament\Pages\Actions\Action;
use App\Filament\Resources\LeagueResource;
use Filament\Resources\Pages\CreateRecord;

class CreateLeague extends CreateRecord
{
    protected static string $resource = LeagueResource::class;
    protected static bool $canCreateAnother = false;
    protected static ?string $title = 'Create league';

    public function getBreadcrumb(): string
    {
        return trans(self::$title);
    }

    protected function getTitle(): string
    {
        return trans(self::$title);
    }

    protected function getFormActions(): array
    {
        return array_merge(
            [$this->getCreateFormAction()],
            static::canCreateAnother() ? [$this->getCreateAnotherFormAction()] : [],
        );
    }

    protected function getActions(): array
    {
        return [
            Action::make('back')
                ->label(trans('Back'))
                ->color('secondary')
                ->url($this->getResource()::getUrl()),
        ];
    }

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $data['slug'] = str($data['name'])->slug()
            ->value();
        $data['created_by'] = $this->getResource()::getUser()->id;

        return $data;
    }

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl();
    }

    protected function getCreatedNotificationTitle(): ?string
    {
        return trans('League created succesfully');
    }
}
