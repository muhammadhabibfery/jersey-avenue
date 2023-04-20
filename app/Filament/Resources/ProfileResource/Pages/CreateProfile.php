<?php

namespace App\Filament\Resources\ProfileResource\Pages;

use App\Traits\ImageHandler;
use Filament\Pages\Actions\Action;
use Illuminate\Support\Facades\Hash;
use Illuminate\Database\Eloquent\Model;
use Filament\Resources\Pages\CreateRecord;
use App\Filament\Resources\ProfileResource;

class CreateProfile extends CreateRecord
{
    use ImageHandler;

    protected static string $resource = ProfileResource::class;
    protected static bool $canCreateAnother = false;
    private string $notificationMessage = 'Your profile updated successfully';

    protected function mutateFormDataBeforeFill(array $data): array
    {
        return $data;
    }

    protected function fillForm(): void
    {
        $this->callHook('beforeFill');

        $data = $this->getResource()::getUser()->toArray();

        $data = $this->mutateFormDataBeforeFill($data);

        $this->form->fill($data);

        $this->callHook('afterFill');
    }

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        if (isset($data['new_password']))
            $data['password'] = Hash::make($data['new_password']);

        if (isset($data['avatar'])) {
            if ($data['avatar'] !== $this->getResource()::getUser()->avatar && !is_null($this->getResource()::getUser()->avatar))
                $this->deleteImage($this->getResource()::getUser()->avatar);
        } else {
            if (!is_null($this->getResource()::getUser()->avatar)) {
                $this->deleteImage($this->getResource()::getUser()->avatar);
                $this->getResource()::getUser()->update(['avatar' => null]);
                unset($data['avatar']);
            }
        }

        unset($data['new_password']);

        return $data;
    }

    protected function handleRecordCreation(array $data): Model
    {
        $this->getResource()::getUser()->update($data);
        $sessionName = 'password_hash_' . auth()->getDefaultDriver();

        if (session()->has($sessionName) && isset($data['password'])) {
            session()->forget($sessionName);
            session()->put([$sessionName => $this->getResource()::getUser()->getAuthPassword()]);
            $this->notificationMessage = 'Your profile and password updated successfully';
        }

        return $this->getResource()::getUser();
    }

    public function getBreadcrumb(): string
    {
        return '';
    }

    protected function getTitle(): string
    {
        return $this->getResource()::getUser()->name;
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
                ->label(trans('Back to dashboard'))
                ->color('secondary')
                ->url($this->getDefaultRoute()),
        ];
    }

    protected function getCreateFormAction(): Action
    {
        return Action::make('create')
            ->label(trans('Update'))
            ->submit('create')
            ->keyBindings(['mod+s']);
    }

    protected function getRedirectUrl(): string
    {
        return $this->getDefaultRoute();
    }

    protected function getCreatedNotificationTitle(): ?string
    {
        return trans($this->notificationMessage);
    }

    private function getDefaultRoute(): string
    {
        return route('filament.pages.dashboard');
    }
}
