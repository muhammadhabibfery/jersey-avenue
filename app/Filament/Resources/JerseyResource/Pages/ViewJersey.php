<?php

namespace App\Filament\Resources\JerseyResource\Pages;

use App\Models\User;
use App\Models\League;
use Filament\Resources\Form;
use Filament\Pages\Actions\Action;
use Filament\Forms\Components\TextInput;
use Filament\Resources\Pages\ViewRecord;
use Filament\Forms\Components\FileUpload;
use App\Filament\Resources\JerseyResource;

class ViewJersey extends ViewRecord
{
    protected static string $resource = JerseyResource::class;
    protected static ?string $title = 'Detail of jersey';

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
                TextInput::make('league_id')
                    ->label(trans('League'))
                    ->formatStateUsing(fn (int $state): string => (League::find($state))->name),
                TextInput::make('name')
                    ->label(trans('Name')),
                TextInput::make('type')
                    ->label(trans('Type')),
                TextInput::make('weight')
                    ->label(trans('Weight'))
                    ->formatStateUsing(fn (int $state): string => "$state Gram"),
                TextInput::make('price')
                    ->label(trans('Price'))
                    ->formatStateUsing(fn (int $state): string => currencyFormat($state)),
                TextInput::make('price_nameset')
                    ->label(trans('Nameset price'))
                    ->formatStateUsing(fn (int $state): string => currencyFormat($state)),
                TextInput::make('stock')
                    ->label(trans('Stock'))
                    ->formatStateUsing(function (array $state): string {
                        $result = '';
                        foreach ($state as $key => $stock)
                            $result .= "$key : $stock,  ";
                        return rtrim($result, ',  ');
                    }),
                TextInput::make('sold')
                    ->label(trans('Sold'))
                    ->formatStateUsing(fn (int $state): string => "$state pcs"),
                FileUpload::make('image')
                    ->label(trans('Image')),
                TextInput::make('created_by')
                    ->label(trans('Created by'))
                    ->formatStateUsing(fn (?int $state = null): string => isset($state) ? (User::find($state))->name : ''),
            ])
            ->columns(2);
    }
}
