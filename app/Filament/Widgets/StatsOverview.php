<?php

namespace App\Filament\Widgets;

use Filament\Widgets\StatsOverviewWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class StatsOverview extends StatsOverviewWidget
{
    protected function getStats(): array
    {
        return [
            Stat::make('Total Users', \App\Models\User::count())
                ->description('Active bot users')
                ->descriptionIcon('heroicon-m-user-group')
                ->color('success'),
            Stat::make('Total Products', \App\Models\Product::count())
                ->description('Ads in the system')
                ->descriptionIcon('heroicon-m-shopping-bag')
                ->color('primary'),
            Stat::make('Total Categories', \App\Models\Category::count())
                ->description('Available categories')
                ->descriptionIcon('heroicon-m-tag')
                ->color('warning'),
        ];
    }
}
