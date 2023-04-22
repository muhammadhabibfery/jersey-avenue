<?php

namespace App\Filament\Widgets;

use App\Filament\Resources\JerseyResource;
use App\Filament\Resources\OrderResource;
use App\Models\Jersey;
use App\Models\Order;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Card;

class OrderStatsOverview extends BaseWidget
{
    protected function getCards(): array
    {
        return [
            Card::make(trans('Jerseys count'), Jersey::count())
                ->color('primary')
                ->url(JerseyResource::getUrl()),
            Card::make(trans('Orders count'), Order::count())
                ->color('success')
                ->url(OrderResource::getUrl())
        ];
    }
}
