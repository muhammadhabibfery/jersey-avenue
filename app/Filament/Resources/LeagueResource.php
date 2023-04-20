<?php

namespace App\Filament\Resources;

use App\Filament\Resources\LeagueResource\Pages;
use App\Models\League;
use App\Models\User;
use App\Traits\ImageHandler;
use Filament\Forms\Components\Card;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\TextInput;
use Filament\Resources\Form;
use Filament\Resources\Resource;
use Filament\Resources\Table;
use Filament\Tables;
use Filament\Tables\Columns\TextColumn;
use Illuminate\Database\Eloquent\Model;
use Livewire\TemporaryUploadedFile;

class LeagueResource extends Resource
{
    use ImageHandler;

    protected static ?string $model = League::class;
    protected static ?string $navigationIcon = 'heroicon-o-globe-alt';
    protected static ?string $navigationGroup = 'Staff Management';
    protected static ?int $navigationSort = 1;
    protected static ?string $recordRouteKeyName = 'slug';

    public static function getModelLabel(): string
    {
        return trans('League');
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Card::make()
                    ->schema([
                        TextInput::make('name')
                            ->label(trans('Name'))
                            ->required()
                            ->maxLength(75)
                            ->unique(League::class, 'name', ignoreRecord: true),
                        TextInput::make('country')
                            ->label(trans('Country'))
                            ->required()
                            ->maxLength(75),
                        FileUpload::make('image')
                            ->label(trans('Choose image'))
                            ->required()
                            ->image()
                            ->maxSize(2500)
                            ->directory('leagues')
                            ->getUploadedFileNameForStorageUsing(
                                fn (TemporaryUploadedFile $file): string => self::setFileName($file->getClientOriginalName())
                            )
                    ])
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')
                    ->label(trans('Name'))
                    ->searchable(),
                TextColumn::make('country')
                    ->label(trans('Country'))
                    ->searchable()
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make()
                    ->before(fn (Model $record) => self::deleteImage($record->image))
                    ->successNotificationTitle(trans('League deleted succesfully')),
            ])
            ->defaultSort('created_at', 'desc');
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListLeagues::route('/'),
            'create' => Pages\CreateLeague::route('/create'),
            'view' => Pages\ViewLeague::route('/{record:slug}'),
            'edit' => Pages\EditLeague::route('/{record:slug}/edit'),
        ];
    }

    public static function getUser(): User
    {
        return auth()->user();
    }
}
