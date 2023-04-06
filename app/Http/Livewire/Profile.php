<?php

namespace App\Http\Livewire;

use Closure;
use App\Models\User;
use Livewire\Component;
use Livewire\Redirector;
use App\Traits\ImageHandler;
use Livewire\TemporaryUploadedFile;
use Filament\Forms\Components\Section;
use Filament\Forms\Contracts\HasForms;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\FileUpload;
use Illuminate\Validation\Rules\Password;
use Filament\Forms\Concerns\InteractsWithForms;
use Illuminate\Support\Facades\Hash;

class Profile extends Component implements HasForms
{
    use InteractsWithForms, ImageHandler;

    public User $user;

    public function mount(): void
    {
        $this->user = auth()->user();

        $this->form->fill([
            'name' => $this->user->name,
            'username' => $this->user->username,
            'email' => $this->user->email,
            'phone' => $this->user->phone,
            'address' => $this->user->address,
            'avatar' => $this->user->avatar,

        ]);
    }

    protected function getFormSchema(): array
    {
        return [
            Section::make(trans('Profile'))
                ->description(trans('Update profile form'))
                ->schema([
                    TextInput::make('name')
                        ->label(trans('Name'))
                        ->required()
                        ->string()
                        ->maxLength(90)
                        ->reactive()
                        ->afterStateUpdated(function (Closure $set, $state) {
                            return $set('username', function () use ($state) {
                                $usernameExists = User::where('username', $state)->first();
                                $state = str($state)->slug('')->value();
                                return $usernameExists && $usernameExists !== $this->user->username
                                    ? $state . rand(11, 99)
                                    : $state;
                            });
                        }),
                    TextInput::make('username')
                        ->required()
                        ->string()
                        ->alphaDash()
                        ->maxLength(35)
                        ->unique(ignorable: $this->user)
                        ->disabled(),
                    TextInput::make('email')
                        ->required()
                        ->string()
                        ->email()
                        ->maxLength(35)
                        ->unique(ignorable: $this->user),
                    TextInput::make('phone')
                        ->label(trans('Phone'))
                        ->numeric()
                        ->minValue(1)
                        ->rules(['digits_between:11,13'])
                        ->nullable()
                        ->unique(ignorable: $this->user),
                    Textarea::make('address')
                        ->label(trans('Address'))
                        ->nullable(),
                    FileUpload::make('avatar')
                        ->label(trans('Profile Picture'))
                        ->image()
                        ->maxSize(2500)
                        ->directory(User::$directory)
                        ->getUploadedFileNameForStorageUsing(
                            fn (TemporaryUploadedFile $file): string => self::setFileName($file->getClientOriginalName())
                        )
                ])
                ->columns(2)
                ->collapsible(),
            Section::make('Password')
                ->label(trans('Change password form'))
                ->schema([
                    TextInput::make('current_password')
                        ->label(trans('Current password'))
                        ->password()
                        ->nullable()
                        ->rules(['current_password'])
                        ->dehydrated(false),
                    TextInput::make('new_password')
                        ->label(trans('New password'))
                        ->password()
                        ->requiredWith('current_password')
                        ->different('current_password')
                        ->rules(['confirmed', Password::min(10)->numbers()->symbols()]),
                    TextInput::make('new_password_confirmation')
                        ->label(trans('Password confimation'))
                        ->password()
                        ->requiredWith('new_password')
                        ->dehydrated(false)
                ])
                ->collapsible()
        ];
    }

    public function getFormModel(): User
    {
        return $this->user;
    }

    public function mutateFormDataBeforeSave(array $formData): array
    {
        if (isset($formData['new_password']))
            $formData['password'] = Hash::make($formData['new_password']);

        if (isset($formData['avatar']))
            if ($formData['avatar'] !== $this->user->avatar && !is_null($this->user->avatar))
                $this->deleteAvatar();

        if (empty($formData['avatar']) && !is_null($this->user->avatar)) {
            $this->deleteAvatar();
            $this->user->update(['avatar' => null]);
            unset($formData['avatar']);
        }

        unset($formData['new_password']);

        return $formData;
    }

    public function save(): Redirector
    {
        $data = $this->mutateFormDataBeforeSave($this->form->getState());

        $this->user->update($data);
        $redirectRoute = to_route('home');

        if ($this->user->wasChanged()) {
            $status = isset($data['password'])
                ? trans('Your profile and password successfully updated')
                : trans('Your profile successfully updated');

            $redirectRoute->with('status', $status);
        }

        return $redirectRoute;
    }

    public function render()
    {
        return view('livewire.profile');
    }

    public function deleteAvatar(): void
    {
        $this->deleteImage($this->user->avatar);
    }
}
