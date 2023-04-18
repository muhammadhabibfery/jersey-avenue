<?php

namespace App\Filament\Resources\LeagueResource\Pages;

use App\Filament\Resources\LeagueResource;
use App\Models\User;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\TextInput;
use Filament\Pages\Actions\Action;
use Filament\Resources\Form;
use Filament\Resources\Pages\ViewRecord;

class ViewLeague extends ViewRecord
{
    protected static string $resource = LeagueResource::class;

    protected static ?string $title = 'Detail of league';

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
            Action::make('back')
                ->label(trans('Back'))
                ->color('secondary')
                ->url($this->getResource()::getUrl()),
        ];
    }

    public function form(Form $form): Form
    {
        return $form->make()
            ->schema([
                TextInput::make('name')
                    ->label(trans('Name')),
                TextInput::make('country')
                    ->label(trans('Country')),
                FileUpload::make('image')
                    ->label(trans('Image')),
                TextInput::make('created_by')
                    ->label(trans('Created by'))
                    ->formatStateUsing(fn (?int $state = null): string => isset($state) ? (User::find($state))->name : ''),
                TextInput::make('updated_by')
                    ->label(trans('Updated by'))
                    ->formatStateUsing(fn (?int $state = null): string => isset($state) ? (User::find($state))->name : ''),
            ]);
    }
}
