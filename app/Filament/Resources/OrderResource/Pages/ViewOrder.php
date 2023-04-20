<?php

namespace App\Filament\Resources\OrderResource\Pages;

use Filament\Resources\Table;
use Filament\Pages\Actions\Action;
use Filament\Tables\Columns\ImageColumn;
use App\Filament\Resources\OrderResource;
use Filament\Resources\Pages\ListRecords;
use Filament\Tables\Columns\TextColumn;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

class ViewOrder extends ListRecords
{
    protected static string $resource = OrderResource::class;
    protected static ?string $title = 'Detail of orders';
    protected static string $directory = 'jerseys';

    public function getModel(): string
    {
        return $this->getResource()::getModel();
    }

    protected function getTableQuery(): Builder
    {
        $query = app($this->getModel())->where('invoice_number', request()->query('record'))
            ->first();

        return $query
            ? $query->jerseys()->getQuery()
            : app($this->getModel())->where('invoice_number', request()->query('record'));
    }

    public function getBreadcrumb(): ?string
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

    protected function getTableBulkActions(): array
    {
        return [];
    }

    public function table(Table $table): Table
    {
        return $table->columns([
            ImageColumn::make('image')
                ->label(trans('Image'))
                ->getStateUsing(fn (Model $record): string => self::$directory . "/{$record->image}"),
            TextColumn::make('name')
                ->label(trans('Name')),
            TextColumn::make('nameset')
                ->getStateUsing(fn (Model $record): string => namesetFormat($record->nameset)),
            TextColumn::make('size')
                ->label(trans('Size')),
            TextColumn::make('quantity')
                ->label(trans('Quantity'))
                ->getStateUsing(fn (Model $record): string => "$record->quantity pcs"),
            TextColumn::make('price')
                ->label(trans('Price'))
                ->getStateUsing(fn (Model $record): string => currencyFormat($record->price)),
            TextColumn::make('total_price')
                ->label(trans('Total Price'))
                ->getStateUsing(fn (Model $record): string => currencyFormat($record->total_price))
        ])
            ->actions([
                //
            ]);
    }

    protected function getTableRecordUrlUsing(): ?\Closure
    {
        return fn () => route('filament.resources.orders.view', ['record' => request()->query('record')]);
    }
}
