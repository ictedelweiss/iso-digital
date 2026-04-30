<?php

namespace App\Filament\Widgets;

use App\Models\IctTicket;
use Filament\Widgets\Concerns\InteractsWithPageFilters;
use Filament\Widgets\ChartWidget;
use Carbon\Carbon;

class IctTicketStatusChart extends ChartWidget
{
    use InteractsWithPageFilters;

    protected static ?string $heading = '📋 Distribusi Status';

    protected static ?string $description = 'Status tiket dalam periode terpilih';

    protected static ?string $pollingInterval = '60s';

    protected static ?string $maxHeight = '300px';

    protected int|string|array $columnSpan = 1;

    protected function getData(): array
    {
        [$startDate, $endDate] = $this->getDateRange();

        $statuses = ['Open', 'In Progress', 'Resolved', 'Closed'];
        $counts = [];

        foreach ($statuses as $status) {
            $counts[] = IctTicket::where('status', $status)
                ->whereBetween('created_at', [$startDate, $endDate])
                ->count();
        }

        return [
            'datasets' => [
                [
                    'data' => $counts,
                    'backgroundColor' => [
                        '#3b82f6',
                        '#f59e0b',
                        '#10b981',
                        '#6b7280',
                    ],
                    'borderColor' => '#1f2937',
                    'borderWidth' => 2,
                ],
            ],
            'labels' => ['🔵 Open', '🟡 In Progress', '✅ Resolved', '⬜ Closed'],
        ];
    }

    protected function getType(): string
    {
        return 'pie';
    }

    protected function getOptions(): array
    {
        return [
            'plugins' => [
                'legend' => [
                    'display' => true,
                    'position' => 'bottom',
                ],
            ],
        ];
    }

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
}