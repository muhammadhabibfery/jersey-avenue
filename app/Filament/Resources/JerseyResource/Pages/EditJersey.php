<?php

namespace App\Filament\Resources\JerseyResource\Pages;

use App\Traits\ImageHandler;
use Filament\Pages\Actions\Action;
use Filament\Resources\Pages\EditRecord;
use App\Filament\Resources\JerseyResource;

class EditJersey extends EditRecord
{
    use ImageHandler;

    protected static string $resource = JerseyResource::class;
    protected static ?string $title = 'Edit jersey';

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

    protected function mutateFormDataBeforeFill(array $data): array
    {
        foreach ($data['stock'] as $key => $stock)
            $data[$key] = $stock;

        unset($data['stock']);
        return $data;
    }

    protected function mutateFormDataBeforeSave(array $data): array
    {
        $jersey = $this->getRecord();

        $data = $this->getResource()::mergeSizeFields($data);

        if ($data['image'] !== $jersey->image)
            $this->deleteImage($jersey->image);

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
