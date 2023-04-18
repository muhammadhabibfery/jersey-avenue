<?php

namespace App\Filament\Resources\LeagueResource\Pages;

use Filament\Pages\Actions\Action;
use Filament\Resources\Pages\EditRecord;
use App\Filament\Resources\LeagueResource;
use App\Traits\ImageHandler;

class EditLeague extends EditRecord
{
    use ImageHandler;

    protected static string $resource = LeagueResource::class;
    protected static ?string $title = 'Edit league';

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
        return [
            $this->getSaveFormAction(),
        ];
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

    protected function mutateFormDataBeforeSave(array $data): array
    {
        $league = $this->getRecord();

        if ($data['image'] !== $league->image)
            $this->deleteImage($league->image);

        $data['slug'] = str($data['name'])->slug()
            ->value();
        $data['updated_by'] = $this->getResource()::getUser()->id;

        return $data;
    }

    protected function getSaveFormAction(): Action
    {
        return Action::make('save')
            ->label(trans('Update'))
            ->submit('save')
            ->keyBindings(['mod+s']);
    }

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl();
    }

    protected function getSavedNotificationTitle(): ?string
    {
        return trans('League updated succesfully');
    }
}
