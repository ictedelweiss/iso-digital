<?php

namespace App\Filament\Widgets;

use App\Models\IctTicket;
use App\Filament\Pages\IctHelpdeskDashboard;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Carbon\Carbon;

class IctHelpdeskStatsWidget extends BaseWidget
{
    protected static ?string $pollingInterval = '30s';

    // Only show on ICT Helpdesk Dashboard page
    protected static ?string $page = IctHelpdeskDashboard::class;

    protected function getStats(): array
    {
        $now = Carbon::now();
        $startOfMonth = $now->copy()->startOfMonth();
        $endOfMonth = $now->copy()->endOfMonth();

        // Total tiket bulan ini
        $totalThisMonth = IctTicket::whereBetween('created_at', [$startOfMonth, $endOfMonth])->count();

        // Total tiket bulan lalu (untuk perbandingan)
        $lastMonthStart = $now->copy()->subMonth()->startOfMonth();
        $lastMonthEnd = $now->copy()->subMonth()->endOfMonth();
        $totalLastMonth = IctTicket::whereBetween('created_at', [$lastMonthStart, $lastMonthEnd])->count();
        $monthDiff = $totalThisMonth - $totalLastMonth;
        $monthTrend = $monthDiff >= 0 ? '+' . $monthDiff : (string)$monthDiff;

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
            ->description($monthTrend . ' dari bulan lalu')
            ->descriptionIcon($monthDiff >= 0 ? 'heroicon-m-arrow-trending-up' : 'heroicon-m-arrow-trending-down')
            ->chart($this->getMonthlyTrendData())
            ->color($monthDiff > 5 ? 'danger' : 'info'),

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
            ->description('Rata-rata penyelesaian')
            ->descriptionIcon('heroicon-o-clock')
            ->color($avgResolutionHours > 24 ? 'danger' : 'success'),

            Stat::make('🚨 SLA Breach', $slaBreachCount)
            ->description('Melebihi batas waktu')
            ->descriptionIcon('heroicon-o-exclamation-triangle')
            ->color($slaBreachCount > 0 ? 'danger' : 'success'),
        ];
    }

    /**
     * Small sparkline data for the total tickets stat card
     */
    private function getMonthlyTrendData(): array
    {
        $data = [];
        for ($i = 5; $i >= 0; $i--) {
            $start = Carbon::now()->subMonths($i)->startOfMonth();
            $end = Carbon::now()->subMonths($i)->endOfMonth();
            $data[] = IctTicket::whereBetween('created_at', [$start, $end])->count();
        }
        return $data;
    }
}