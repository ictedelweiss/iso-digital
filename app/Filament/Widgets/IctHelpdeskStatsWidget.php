<?php

namespace App\Filament\Widgets;

use App\Models\IctTicket;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Carbon\Carbon;

class IctHelpdeskStatsWidget extends BaseWidget
{
    protected static ?string $pollingInterval = '30s';

    protected function getStats(): array
    {
        $now = Carbon::now();
        $startOfMonth = $now->copy()->startOfMonth();
        $endOfMonth = $now->copy()->endOfMonth();

        // Total tiket bulan ini
        $totalThisMonth = IctTicket::whereBetween('created_at', [$startOfMonth, $endOfMonth])->count();

        // Tiket Open
        $openTickets = IctTicket::where('status', 'Open')->count();

        // Tiket In Progress
        $inProgressTickets = IctTicket::where('status', 'In Progress')->count();

        // Tiket Resolved bulan ini
        $resolvedThisMonth = IctTicket::where('status', 'Resolved')
            ->whereBetween('resolved_at', [$startOfMonth, $endOfMonth])
            ->count();

        // Average Resolution Time (in hours)
        $avgResolutionMinutes = IctTicket::whereNotNull('resolved_at')
            ->whereBetween('resolved_at', [$startOfMonth, $endOfMonth])
            ->get()
            ->avg(function ($ticket) {
            return $ticket->created_at->diffInMinutes($ticket->resolved_at);
        });

        $avgResolutionHours = $avgResolutionMinutes ? round($avgResolutionMinutes / 60, 1) : 0;

        // SLA Breach count
        $slaBreachCount = IctTicket::whereIn('status', ['Open', 'In Progress'])
            ->get()
            ->filter(fn($ticket) => $ticket->getIsSlaBreach())
            ->count();

        return [
            Stat::make('📊 Total Tiket Bulan Ini', $totalThisMonth)
            ->description($now->format('F Y'))
            ->descriptionIcon('heroicon-o-calendar')
            ->color('info'),

            Stat::make('🔵 Open', $openTickets)
            ->description('Menunggu ditangani')
            ->descriptionIcon('heroicon-o-inbox')
            ->color($openTickets > 5 ? 'danger' : 'info'),

            Stat::make('🟡 In Progress', $inProgressTickets)
            ->description('Sedang dikerjakan')
            ->descriptionIcon('heroicon-o-cog-6-tooth')
            ->color('warning'),

            Stat::make('✅ Resolved', $resolvedThisMonth)
            ->description('Selesai bulan ini')
            ->descriptionIcon('heroicon-o-check-circle')
            ->color('success'),

            Stat::make('⏱️ Avg Resolution', $avgResolutionHours . ' jam')
            ->description('Rata-rata waktu penyelesaian')
            ->descriptionIcon('heroicon-o-clock')
            ->color($avgResolutionHours > 24 ? 'danger' : 'success'),

            Stat::make('🚨 SLA Breach', $slaBreachCount)
            ->description('Melebihi batas waktu')
            ->descriptionIcon('heroicon-o-exclamation-triangle')
            ->color($slaBreachCount > 0 ? 'danger' : 'success'),
        ];
    }
}