<?php

namespace App\Filament\Widgets;

use App\Models\IctTicket;
use Filament\Widgets\Concerns\InteractsWithPageFilters;
use Filament\Widgets\ChartWidget;
use Carbon\Carbon;

class IctTicketPriorityChart extends ChartWidget
{
    use InteractsWithPageFilters;

    protected static ?string $heading = '⚡ Distribusi Prioritas';

    protected static ?string $description = 'Pembagian tiket berdasarkan tingkat prioritas';

    protected static ?string $pollingInterval = '60s';

    protected static ?string $maxHeight = '300px';

    protected int|string|array $columnSpan = 1;

    protected function getData(): array
    {
        [$startDate, $endDate] = $this->getDateRange();

        $priorities = ['Low', 'Medium', 'High', 'Critical'];
        $counts = [];

        foreach ($priorities as $priority) {
            $counts[] = IctTicket::where('priority', $priority)
                ->whereBetween('created_at', [$startDate, $endDate])
                ->count();
        }

        return [
            'datasets' => [
                [
                    'label' => 'Jumlah Tiket',
                    'data' => $counts,
                    'backgroundColor' => [
                        '#10b981',
                        '#f59e0b',
                        '#f97316',
                        '#ef4444',
                    ],
                    'borderColor' => [
                        '#059669',
                        '#d97706',
                        '#ea580c',
                        '#dc2626',
                    ],
                    'borderWidth' => 1,
                    'borderRadius' => 6,
                ],
            ],
            'labels' => ['🟢 Low', '🟡 Medium', '🟠 High', '🔴 Critical'],
        ];
    }

    protected function getType(): string
    {
        return 'bar';
    }

    protected function getOptions(): array
    {
        return [
            'plugins' => [
                'legend' => [
                    'display' => false,
                ],
            ],
            'scales' => [
                'y' => [
                    'beginAtZero' => true,
                    'ticks' => [
                        'stepSize' => 5,
                    ],
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