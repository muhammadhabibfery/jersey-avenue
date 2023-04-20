<?php

namespace App\Filament\Resources;

use App\Models\User;
use Filament\Tables;
use App\Models\Jersey;
use Filament\Resources\Form;
use Filament\Resources\Table;
use Filament\Resources\Resource;
use Illuminate\Database\Eloquent\Builder;
use App\Filament\Resources\JerseyResource\Pages;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use App\Models\League;
use App\Traits\ImageHandler;
use Filament\Forms\Components\Card;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\Filter;
use Illuminate\Database\Eloquent\Model;
use Livewire\TemporaryUploadedFile;

class JerseyResource extends Resource
{
    use ImageHandler;

    protected static ?string $model = Jersey::class;
    protected static ?string $navigationIcon = 'heroicon-o-identification';
    protected static ?string $navigationGroup = 'Staff Management';
    protected static ?int $navigationSort = 2;
    protected static ?string $recordRouteKeyName = 'slug';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Card::make()
                    ->schema([
                        Select::make('league_id')
                            ->label(trans('League'))
                            ->relationship('league', 'name')
                            ->required()
                            ->exists(League::class, 'id')
                            ->searchable()
                            ->preload(),
                        TextInput::make('name')
                            ->label(trans('Name'))
                            ->required()
                            ->maxLength(150)
                            ->unique(Jersey::class, 'name', ignoreRecord: true),
                        TextInput::make('type')
                            ->label(trans('Type'))
                            ->required()
                            ->maxLength(150),
                        TextInput::make('weight')
                            ->label(trans('Weight'))
                            ->numeric()
                            ->required()
                            ->minValue(1),
                        TextInput::make('price')
                            ->label(trans('Price'))
                            ->numeric()
                            ->required()
                            ->minValue(1)
                            ->mask(
                                fn (TextInput\Mask $mask) => $mask->money('Rp. ', '.', 3, false)
                            ),
                        TextInput::make('price_nameset')
                            ->label(trans('Nameset price'))
                            ->required()
                            ->minValue(1)
                            ->mask(
                                fn (TextInput\Mask $mask): TextInput\Mask => $mask->money('Rp. ', '.', 3, false)
                            ),
                        TextInput::make('S')
                            ->label(trans('Size :value', ['value' => 'S']))
                            ->numeric()
                            ->required()
                            ->minValue(0)
                            ->default(0),
                        TextInput::make('M')
                            ->label(trans('Size :value', ['value' => 'M']))
                            ->numeric()
                            ->required()
                            ->minValue(0)
                            ->default(0),
                        TextInput::make('L')
                            ->label(trans('Size :value', ['value' => 'L']))
                            ->numeric()
                            ->required()
                            ->minValue(0)
                            ->default(0),
                        TextInput::make('XL')
                            ->label(trans('Size :value', ['value' => 'XL']))
                            ->numeric()
                            ->required()
                            ->minValue(0)
                            ->default(0),
                        FileUpload::make('image')
                            ->label(trans('Choose image'))
                            ->required()
                            ->image()
                            ->maxSize(2500)
                            ->directory('jerseys')
                            ->getUploadedFileNameForStorageUsing(
                                fn (TemporaryUploadedFile $file): string => self::setFileName($file->getClientOriginalName())
                            )
                    ])
                    ->columns(2)
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('league.name')
                    ->label(trans('League')),
                TextColumn::make('name')
                    ->label(trans('Name'))
                    ->searchable(),
                TextColumn::make('type')
                    ->label(trans('Type')),
                TextColumn::make('weight')
                    ->label(trans('Weight'))
                    ->formatStateUsing(fn (int $state): string => "$state Gram"),
                TextColumn::make('price')
                    ->label(trans('Price'))
                    ->formatStateUsing(fn (int $state): string => currencyFormat($state)),
                TextColumn::make('sold')
                    ->label(trans('Sold'))
                    ->formatStateUsing(fn (int $state): string => "$state pcs")
            ])
            ->filters([
                Filter::make('league')
                    ->form([
                        Select::make('league')
                            ->label(trans('Choose league'))
                            ->options(fn (): array => League::get()->pluck('name', 'slug')->toArray())
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        return $query->when(
                            $data['league'],
                            fn (Builder $query, string $value): Builder => $query->hasLeague($value)
                        );
                    }),
                Tables\Filters\TrashedFilter::make()
                    ->hidden(fn (): bool => !setPermissions(User::$roles[0], self::getUser())),
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make()
                    ->before(fn (Model $record) => $record->update(['deleted_by' => self::getUser()->id]))
                    ->successNotificationTitle(trans('League deleted succesfully')),
                Tables\Actions\RestoreAction::make()
                    ->before(fn (Model $record) => $record->update(['deleted_by' => null]))
                    ->successNotificationTitle(trans('Jersey restored succesfully')),
                Tables\Actions\ForceDeleteAction::make()
                    ->before(fn (Model $record) => self::deleteImage($record->image))
                    ->successNotificationTitle(trans('Jersey succesfully deleted permanently'))
            ])
            ->defaultSort('sold', 'desc');
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListJerseys::route('/'),
            'create' => Pages\CreateJersey::route('/create'),
            'view' => Pages\ViewJersey::route('/{record:slug}'),
            'edit' => Pages\EditJersey::route('/{record:slug}/edit'),
        ];
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()
            ->withoutGlobalScopes([
                SoftDeletingScope::class,
            ]);
    }

    public static function getUser(): User
    {
        return auth()->user();
    }

    public static function mergeSizeFields(array $data): array
    {
        $data['stock'] = array_combine(
            Jersey::$sizes,
            [$data['S'], $data['M'], $data['L'], $data['XL']]
        );

        foreach (Jersey::$sizes as $size)
            unset($data[$size]);

        return $data;
    }
}
