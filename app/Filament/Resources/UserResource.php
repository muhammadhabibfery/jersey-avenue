<?php

namespace App\Filament\Resources;

use App\Filament\Resources\UserResource\Pages;
use App\Filament\Resources\UserResource\Pages\CreateUser;
use App\Filament\Resources\UserResource\Pages\EditUser;
use App\Models\User;
use Closure;
use Filament\Forms\Components\Card;
use Filament\Forms\Components\Radio;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Pages\Page;
use Filament\Resources\Form;
use Filament\Resources\Resource;
use Filament\Resources\Table;
use Filament\Tables;
use Filament\Tables\Columns\BadgeColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Validation\Rule;

class UserResource extends Resource
{
    protected static ?string $model = User::class;

    protected static ?string $navigationIcon = 'heroicon-o-users';
    protected static ?string $navigationGroup = 'Admin Management';
    protected static ?string $recordRouteKeyName = 'username';

    public static function getBreadcrumb(): string
    {
        return trans('Manage users');
    }

    protected static function getNavigationLabel(): string
    {
        return trans('Manage users');
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()
            ->withoutGlobalScopes()
            ->where('id', '!=', self::getUser()->id);
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
                            ->maxLength(90)
                            ->reactive()
                            ->afterStateUpdated(function (Closure $set, string $state): Closure {
                                return $set('username', function () use ($state): string {
                                    $state = str($state)->slug('')->value();
                                    return User::where('username', $state)->first()
                                        ? $state . rand(11, 99)
                                        : $state;
                                });
                            })
                            ->disabled(fn (Page $livewire): bool => $livewire instanceof EditUser)
                            ->dehydrated(fn (Page $livewire): bool => $livewire instanceof CreateUser),
                        TextInput::make('username')
                            ->label(trans('Username'))
                            ->required()
                            ->maxLength(35)
                            ->disabled()
                            ->dehydrated(fn (Page $livewire): bool => $livewire instanceof CreateUser),
                        TextInput::make('email')
                            ->label(trans('Email'))
                            ->email()
                            ->required()
                            ->maxLength(35)
                            ->unique('users', 'email', ignoreRecord: true)
                            ->disabled(fn (Page $livewire): bool => $livewire instanceof EditUser)
                            ->dehydrated(fn (Page $livewire): bool => $livewire instanceof CreateUser),
                        TextInput::make('phone')
                            ->label(trans('Phone'))
                            ->numeric()
                            ->required()
                            ->minLength(11)
                            ->maxLength(13)
                            ->unique('users', 'phone', ignoreRecord: true)
                            ->disabled(fn (Page $livewire): bool => $livewire instanceof EditUser)
                            ->dehydrated(fn (Page $livewire): bool => $livewire instanceof CreateUser),
                        Select::make('role')
                            ->label(trans('Role'))
                            ->required()
                            ->options([
                                'ADMIN' => 'Admin',
                                'STAFF' => 'Staff'
                            ])
                            ->rules([Rule::in(User::$roles)])
                            ->hidden(fn (Page $livewire): bool => $livewire instanceof CreateUser)
                            ->dehydrated(fn (Page $livewire): bool => $livewire instanceof EditUser),
                        TextInput::make('roles')
                            ->label(trans('Role'))
                            ->required()
                            ->default(User::$roles[1])
                            ->disabled()
                            ->hidden(fn (Page $livewire): bool => $livewire instanceof EditUser)
                            ->dehydrated(false),
                        Textarea::make('address')
                            ->label(trans('Address'))
                            ->required()
                            ->minLength(10)
                            ->disabled(fn (Page $livewire): bool => $livewire instanceof EditUser)
                            ->dehydrated(fn (Page $livewire): bool => $livewire instanceof CreateUser),
                        Radio::make('status')
                            ->label(trans('Status'))
                            ->required(fn (Page $livewire): bool => $livewire instanceof EditUser)
                            ->options([
                                'ACTIVE' => trans('ACTIVE'),
                                'INACTIVE' => trans('INACTIVE')
                            ])
                            ->rules([Rule::in(User::$status)])
                            ->hidden(fn (Page $livewire): bool => $livewire instanceof CreateUser)
                            ->dehydrated(fn (Page $livewire): bool => $livewire instanceof EditUser)
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
                TextColumn::make('username')
                    ->label(trans('username'))
                    ->searchable(),
                BadgeColumn::make('role')
                    ->label(trans('Role'))
                    ->enum([
                        'ADMIN' => 'Admin',
                        'STAFF' => 'Staff',
                        'CUSTOMER' => 'Customer'
                    ])
                    ->colors([
                        'danger' => 'Admin',
                        'primary' => 'Staff',
                        'success' => 'Customer'
                    ]),
                BadgeColumn::make('status')
                    ->fontFamily('mono')
                    ->weight('bold')
                    ->icons([
                        'heroicon-o-check' => 'ACTIVE',
                        'heroicon-o-x' => 'INACTIVE'
                    ])
                    ->colors([
                        'success' => 'ACTIVE',
                        'danger' => 'INACTIVE'
                    ])
                    ->formatStateUsing(fn (string $state) => trans($state)),
            ])
            ->filters([
                SelectFilter::make('role')
                    ->options([
                        'ADMIN' => 'Admin',
                        "STAFF" => "Staff",
                        "CUSTOMER" => "Customer"
                    ]),
                SelectFilter::make('status')
                    ->options([
                        'ACTIVE' => trans('ACTIVE'),
                        'INACTIVE' => trans('INACTIVE')
                    ])
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make()
                    ->successNotificationTitle(trans('User deleted succesfully')),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListUsers::route('/'),
            'create' => Pages\CreateUser::route('/create'),
            'view' => Pages\ViewUser::route('/{record:username}'),
            'edit' => Pages\EditUser::route('/edit/{record:username}'),
        ];
    }

    public static function getUser(): User
    {
        return auth()->user();
    }
}
