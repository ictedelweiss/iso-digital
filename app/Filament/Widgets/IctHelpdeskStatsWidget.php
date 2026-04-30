<?php

namespace App\Filament\Widgets;

use App\Models\IctTicket;
use App\Filament\Pages\IctHelpdeskDashboard;
use Filament\Widgets\Concerns\InteractsWithPageFilters;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Carbon\Carbon;

class IctHelpdeskStatsWidget extends BaseWidget
{
    use InteractsWithPageFilters;

    protected static ?string $pollingInterval = '30s';

    protected function getStats(): array
    {
        [$startDate, $endDate] = $this->getDateRange();

        // Total tiket dalam range
        $totalInRange = IctTicket::whereBetween('created_at', [$startDate, $endDate])->count();

        // Tiket Open dalam range
        $openTickets = IctTicket::where('status', 'Open')
            ->whereBetween('created_at', [$startDate, $endDate])
            ->count();

        // Tiket In Progress dalam range
        $inProgressTickets = IctTicket::where('status', 'In Progress')
            ->whereBetween('created_at', [$startDate, $endDate])
            ->count();

        // Tiket Resolved dalam range
        $resolvedInRange = IctTicket::where('status', 'Resolved')
            ->whereBetween('created_at', [$startDate, $endDate])
            ->count();

        // Tiket Closed dalam range
        $closedInRange = IctTicket::where('status', 'Closed')
            ->whereBetween('created_at', [$startDate, $endDate])
            ->count();

        // Average Resolution Time (in hours) - within range
        $avgResolutionMinutes = IctTicket::whereNotNull('resolved_at')
            ->whereBetween('created_at', [$startDate, $endDate])
            ->get()
            ->avg(function ($ticket) {
            return $ticket->created_at->diffInMinutes($ticket->resolved_at);
        });

        $avgResolutionHours = $avgResolutionMinutes ? round($avgResolutionMinutes / 60, 1) : 0;

        // SLA Breach count - within range
        $slaBreachCount = IctTicket::whereIn('status', ['Open', 'In Progress'])
            ->whereBetween('created_at', [$startDate, $endDate])
            ->get()
            ->filter(fn($ticket) => $ticket->getIsSlaBreach())
            ->count();

        // Period label
        $periodLabel = $startDate->format('d M') . ' - ' . $endDate->format('d M Y');

        return [
            Stat::make('📊 Total Tiket', $totalInRange)
            ->description($periodLabel)
            ->descriptionIcon('heroicon-o-calendar')
            ->chart($this->getSparklineData($startDate, $endDate))
            ->color('info'),

            Stat::make('🔵 Open', $openTickets)
            ->description('Menunggu ditangani')
            ->descriptionIcon('heroicon-o-inbox')
            ->color($openTickets > 5 ? 'danger' : 'info'),

            Stat::make('🟡 In Progress', $inProgressTickets)
            ->description('Sedang dikerjakan')
            ->descriptionIcon('heroicon-o-cog-6-tooth')
            ->color('warning'),

            Stat::make('✅ Resolved', $resolvedInRange)
            ->description('Selesai dalam periode')
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
     * Parse filters into Carbon date range
     */
    private function getDateRange(): array
    {
        $period = $this->filters['period'] ?? 'this_month';
        $now = Carbon::now();

        return match ($period) {
                'today' => [$now->copy()->startOfDay(), $now->copy()->endOfDay()],
                'this_week' => [$now->copy()->startOfWeek(), $now->copy()->endOfWeek()],
                'this_month' => [$now->copy()->startOfMonth(), $now->copy()->endOfMonth()],
                'last_month' => [$now->copy()->subMonth()->startOfMonth(), $now->copy()->subMonth()->endOfMonth()],
                'last_3_months' => [$now->copy()->subMonths(3)->startOfMonth(), $now->copy()->endOfMonth()],
                'last_6_months' => [$now->copy()->subMonths(6)->startOfMonth(), $now->copy()->endOfMonth()],
                'this_year' => [$now->copy()->startOfYear(), $now->copy()->endOfYear()],
                'custom' => [
                Carbon::parse($this->filters['start_date'] ?? $now->copy()->startOfMonth()),
                Carbon::parse($this->filters['end_date'] ?? $now->copy()->endOfMonth())->endOfDay(),
            ],
                default => [$now->copy()->startOfMonth(), $now->copy()->endOfMonth()],
            };
    }

    /**
     * Generate sparkline data for the stat card
     */
    private function getSparklineData(Carbon $startDate, Carbon $endDate): array
    {
        $data = [];
        $diffDays = $startDate->diffInDays($endDate);

        if ($diffDays <= 7) {
            // Daily sparkline
            for ($i = 0; $i <= $diffDays; $i++) {
                $day = $startDate->copy()->addDays($i);
                $data[] = IctTicket::whereDate('created_at', $day)->count();
            }
        }
        elseif ($diffDays <= 60) {
            // Weekly sparkline
            $weeks = ceil($diffDays / 7);
            for ($i = 0; $i < $weeks; $i++) {
                $weekStart = $startDate->copy()->addWeeks($i);
                $weekEnd = $weekStart->copy()->addDays(6)->min($endDate);
                $data[] = IctTicket::whereBetween('created_at', [$weekStart, $weekEnd])->count();
            }
        }
        else {
            // Monthly sparkline
            $current = $startDate->copy()->startOfMonth();
            while ($current->lte($endDate)) {
                $monthEnd = $current->copy()->endOfMonth()->min($endDate);
                $data[] = IctTicket::whereBetween('created_at', [$current, $monthEnd])->count();
                $current->addMonth();
            }
        }

        return $data;
    }
}