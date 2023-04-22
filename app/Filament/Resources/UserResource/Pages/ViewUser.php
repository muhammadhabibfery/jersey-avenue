<?php

namespace App\Filament\Resources\UserResource\Pages;

use Filament\Pages\Actions;
use Filament\Resources\Form;
use App\Filament\Resources\UserResource;
use App\Models\User;
use Filament\Forms\Components\Card;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Resources\Pages\ViewRecord;

class ViewUser extends ViewRecord
{
    protected static string $resource = UserResource::class;
    protected static ?string $title = 'Detail of user';

    public function getBreadcrumb(): string
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
            Actions\Action::make('back')
                ->label(trans('Back'))
                ->color('secondary')
                ->url($this->getResource()::getUrl())
        ];
    }

    public function form(Form $form): Form
    {
        return $form->schema([
            Card::make()
                ->schema([
                    TextInput::make('name')
                        ->label(trans('Name')),
                    TextInput::make('username')
                        ->label(trans('Username')),
                    TextInput::make('email')
                        ->label(trans('Email')),
                    TextInput::make('phone')
                        ->label(trans('Phone')),
                    TextInput::make('role')
                        ->label(trans('Role')),
                    Textarea::make('address')
                        ->label(trans('Address')),
                    TextInput::make('status')
                        ->label(trans('Status')),
                    TextInput::make('created_by')
                        ->label(trans('Created by'))
                        ->formatStateUsing(fn (?int $state = null): string => isset($state) ? (User::find($state))->name : ''),
                    TextInput::make('updated_by')
                        ->label(trans('Updated by'))
                        ->formatStateUsing(fn (?int $state = null): string => isset($state) ? (User::find($state))->name : ''),
                ])
                ->columns(2)
        ]);
    }
}
