<?php

namespace App\Filament\Widgets;

use App\Models\Asset;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class AssetStatsWidget extends BaseWidget
{
    protected static ?int $sort = 2;

    protected function getStats(): array
    {
        $totalAssets = Asset::count();
        $activeAssets = Asset::where('status', 'Active')->count();
        $maintenanceAssets = Asset::where('status', 'Maintenance')->count();
        $retiredAssets = Asset::where('status', 'Retired')->count();
        $totalValue = Asset::sum('purchase_price') ?? 0;

        return [
            Stat::make('Total Asset', $totalAssets)
                ->description('Semua asset')
                ->descriptionIcon('heroicon-m-archive-box')
                ->color('primary')
                ->chart([7, 3, 4, 5, 6, 3, 5, 8, 9, $totalAssets]),

            Stat::make('Asset Aktif', $activeAssets)
                ->description('Status: Active')
                ->descriptionIcon('heroicon-m-check-circle')
                ->color('success'),

            Stat::make('Maintenance', $maintenanceAssets)
                ->description('Dalam perawatan')
                ->descriptionIcon('heroicon-m-wrench-screwdriver')
                ->color('warning'),

            Stat::make('Retired', $retiredAssets)
                ->description('Tidak digunakan')
                ->descriptionIcon('heroicon-m-archive-box-x-mark')
                ->color('danger'),

            Stat::make('Total Nilai Asset', 'Rp ' . number_format($totalValue, 0, ',', '.'))
                ->description('Nilai pembelian')
                ->descriptionIcon('heroicon-m-currency-dollar')
                ->color('success'),
        ];
    }
}
