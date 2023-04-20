<?php

namespace App\Filament\Resources;

use App\Filament\Resources\OrderResource\Pages;
use App\Models\Order;
use Filament\Resources\Resource;
use Filament\Resources\Table;
use Filament\Tables;
use Filament\Tables\Columns\BadgeColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Illuminate\Database\Eloquent\Model;

class OrderResource extends Resource
{
    protected static ?string $model = Order::class;
    protected static ?string $navigationIcon = 'heroicon-o-shopping-cart';
    protected static ?string $navigationGroup = 'Staff Management';
    protected static ?int $navigationSort = 3;
    protected static ?string $recordRouteKeyName = 'invoice_number';

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('invoice_number')
                    ->label(trans('Invoice Number'))
                    ->searchable(),
                TextColumn::make('courier_services')
                    ->label(trans('Courier Services'))
                    ->formatStateUsing(fn (Model $record): string => courierServiceFormat($record->courier_services)),
                BadgeColumn::make('status')
                    ->enum([
                        'IN CART' => 'In cart',
                        'PENDING' => 'Pending',
                        'SUCCESS' => 'Success',
                        'FAILED' => 'Failed',
                    ])
                    ->colors([
                        'secondary' => 'IN CART',
                        'primary' => 'PENDING',
                        'success' => 'SUCCESS',
                        'danger' => 'FAILED'
                    ]),
                TextColumn::make('total_price')
                    ->label(trans('Total price'))
                    ->formatStateUsing(fn (int $state): string => currencyFormat($state))
            ])
            ->filters([
                SelectFilter::make('status')
                    ->options([
                        'IN CART' => 'In cart',
                        'PENDING' => 'Pending',
                        'SUCCESS' => 'Success',
                        'FAILED' => 'Failed',
                    ])
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListOrders::route('/'),
            'view' => Pages\ViewOrder::route('/detail'),
        ];
    }
}
