<?php

namespace App\Filament\Resources\UserResource\Pages;

use App\Filament\Resources\UserResource;
use App\Models\User;
use Filament\Pages\Actions;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Support\Facades\Hash;

class CreateUser extends CreateRecord
{
    protected static string $resource = UserResource::class;
    protected static bool $canCreateAnother = false;
    protected static ?string $title = 'Create user';

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
            Actions\Action::make('back')
                ->label(trans('Back'))
                ->color('secondary')
                ->url($this->getResource()::getUrl()),
        ];
    }
    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $data['role'] = User::$roles[1];
        $data['password'] = Hash::make($data['phone']);
        $data['created_by'] = $this->getResource()::getUser()->id;
        return $data;
    }

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl();
    }

    protected function getCreatedNotificationTitle(): ?string
    {
        return trans('User created succesfully');
    }
}
